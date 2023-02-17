<?php
/**
* Funções úteis de Rotas
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;

	use \Fault;
	
	trait Routes
	{
		private $navigationConfig = null;	//Configurações de navegação
		private $pagesConfig = null;		//Configurações da página

		/**
		* Carrega e retorna configurações de navegação
		*
		* @return array
		*/
		protected function _navigationData ()
		{
			if($this->navigationConfig === null)
				$this->navigationConfig = $this->_config('navigation');
			return $this->navigationConfig;
		}

		/**
		* Carrega e retorna configurações de páginas
		*
		* @return array
		*/
		protected function _pagesData ()
		{
			if($this->pagesConfig === null)
				$this->pagesConfig = array_merge($this->_config('page'), $this->_listConfig('page'));
			
			return $this->pagesConfig;
		}

		/**
		 * Formata a URL sem barras duplas
		 * 
		 * @param string|array $url String com a URL a ser consertada ou array para ser unido
		 * @param string|array $add (Opcional) Dados a serem acrescidos na URL
		 * 
		 * @return string
		 */
		public function formatURL ($url, $add = null)
		{
			if(is_array($url))
				$url = implode("/", $url);

			if(is_array($add))
				$url .= "/" . implode("/", $add);

			if(is_string($add))
				$url .= "/" . $add;
			
			$url = preg_replace("/(?<!:)[\\/]{2,}/i", "/", $url);
			$url = preg_replace("/\\/$/i", "", $url);

			return $url;
		}
	
		/**
		 * Resgata caminho do domínio
		 * 
		 * @return string
		 */
		public function getDomainPath ()
		{
			//Se for um acesso por console, retornar o caminho configurado
			if(context() == CONTEXT_CONSOLE_BASH || context() == CONTEXT_CONSOLE_CRON)
				$domainPath = $this->_navigationData()['path'];
			else
				$domainPath = dirname($_SERVER['PHP_SELF']);

			return ($domainPath == "/") ? '' : $domainPath;
		}

		/**
		 * Resgata URI solicitada
		 * 
		 * @return string
		 */
		public function getURI ()
		{
			$uri = str_replace($this->getDomainPath(), "", urldecode($_SERVER['REQUEST_URI']));
			return preg_replace("/^\\/{1,}/", "", $uri);
		}

		/**
		 * Resgata protocolo (Http/Https)
		 * 
		 * @return string
		 */
		public function getProtocol ()
		{
			//Se for um acesso por console, retornar protocolo configurado
			if(context() == CONTEXT_CONSOLE_BASH || context() == CONTEXT_CONSOLE_CRON)
				return $this->_navigationData()['protocol'];

			if (isset($_SERVER['HTTPS']) &&
			    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
			    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			  	
			  	return 'https';
			}

			return  'http';
		}

		/**
		 * Resgata domínio
		 * 
		 * @return string
		 */
		public function getDomain ()
		{
			//Se for um acesso por console, retornar o caminho configurado
			if(context() == CONTEXT_CONSOLE_BASH || context() == CONTEXT_CONSOLE_CRON)
				return $this->_navigationData()['domain'];

			return $_SERVER['HTTP_HOST'];
		}

		/**
		 * Resgata URL da aplicação
		 * 
		 * @return string
		 */
		public function getURL ()
		{
			return $this->getProtocol() . "://" . $this->formatURL($this->getDomain(), $this->getDomainPath());
		}

		/**
		 * Resgata URL atual
		 * 
		 * @return string
		 */
		public function getCurrentURL ()
		{
			return $this->getProtocol() . "://" . $this->formatURL([$this->getDomain() , $this->getDomainPath() , $this->getURI()]);
		}

		/**
		* Obter URL da pasta Public.
		*
		* @param string $path Concatena o nome do arquivo/caminho se enviado
		*
		* @return string
		*/
		public function getPublicURL ($path = '')
		{
			return $this->formatURL([$this->getURL(), 'public'], $path);
		}

		/**
		 * Listar todas as rotas
		 * 
		 * @return array
		 */
		public function listRoutes ()
		{
			return $this->_pagesData();
		}

		/**
		 * Resgata uma rota específica
		 * 
		 * @param string $routeName Nome da rota
		 * 
		 * @return array
		 */
		public function getRoute ($routeName)
		{
			return isset($this->_pagesData()[$routeName]) ? $this->_pagesData()[$routeName] : false;
		}

		/**
		 * Resgata uma rota específica
		 * 
		 * @param string $routeName Nome da rota
		 * 
		 * @return array
		 */
		public function getRouteURL ($routeName = '', $variables = array())
		{
			//Verificar se a página solicitada existe
			if($routeName != '' && !isset($this->_pagesData()[$routeName]))
				return false;

			//Resgatar conteúdo da página
			$pageContent = ($routeName != '') ? $this->_pagesData()[$routeName] : $this->currentPage;

			//Se a página tiver um caminho estático
			if(isset($pageContent['static']))
			{
				$uri = "/" . $pageContent['static'];
			}

			//Se a página tiver um caminho por padrão
			else if(isset($pageContent['pattern']))
			{
				//Separar todos os segmentos da URL
				$pattern = explode("/", $pageContent['pattern']);
				
				//Uri
				$uri = "";

				//Índice da variável
				$varIndex = 0;

				//Avaliar cada segmento do padrão exigido
				foreach($pattern as $mark)
				{
					//Se o segmento tiver alguma variável
					if( preg_match("/^\\?|^\\#|^\\%|^\\$/", $mark))
					{
						//Se a variável para esse segmento for opcional e não foi definida, ignorar
						if(!isset($variables[$varIndex]) && $mark == "?")
							continue;

						//Resgatar o valor das variáveis passadas
						$value = (isset($variables[$varIndex]) ? $variables[$varIndex] : 0);
						$varIndex++;

						//Concatenar valor
						$uri .= "/" . $value;
					}
					//Se o segmento for um termo fixo
					else
					{
						$uri .= "/" . $mark;
					}					
				}
			}

			//Se a página não tiver um caminho válido
			else
			{
				return false;
			}

			//Retornar URL com caminho da página
			return $this->getURL() . $uri;
		}

		/**
		 * Resgata a rota de acorod com a URL atual
		 * 
		 * @return array
		 */
		public function getCurrentURIRoute ()
		{
			return $this->getRouteByURI($this->getURI());
		}

		/**
		* Processar as variáveis encontradas na URL
		*
		* @param array $block Recebe o array com dados do bloco
		* @param array $matches Valores encontrados
		* @return string
		*/
		private function _processVariables ($block, $matches)
		{
			//Receberá as variáveis
			$variables = [];

			//Para cada variável encontrada
			for ($i=1; $i < count($matches); $i++)
			{	
				//Resgatar o nome da variável definida no escopo da página
				$variableName = isset($block['variables'][$i-1]) ? $block['variables'][$i-1] : $i;

				//Incluir a variável e o dado encontrado
				$variables[$variableName] = preg_replace("/\\//", "", self::SanitizeURL($matches[$i][0]));
			}
			return $variables;
		}

		/**
		 * Valida uma rota de acordo com o contexto atual
		 * 
		 * @param string|Page $routeName 
		 * 
		 * @return bool|Fault
		 */
		protected function validateRouteContext ($route)
		{
			if(!($route instanceof Page))
				$route = new Page($route);

			$filters = $route->getFilters();

			foreach ($filters as $context => $value)
			{
				//Filtrar por admin (exigir admin)
				if($context == "admin" && $value === true && (!\CMS\CMS::user() || \CMS\CMS::user() instanceof Fault))
					return new Fault($this->write("Current route requires logged admin"), "route_filter_admin",  $route->getName(), 403);

				//Filtrar por protocolo
				if($context == "protocol" && $this->getProtocol() != $value)
					return new Fault($this->write("Current protocol does not match this route's accepted protocol"), "route_filter_protocol",   $route->getName(), 404);

				//Filtrar através de um método específico
				if($context == "custom" && !$this->parseMethod($value, true, $route))
					return new Fault($this->write("Custom method filter blocked route execution"), "route_filter_custom_method",  $route->getName(), 403);

				//Normalizar valor para interpretar como array múltiplo
				if(!is_array($value))
					$value = [$value];

				//Filtrar por método
				if($context == "method" && !in_array($this->getMethod(), $value))
					return new Fault($this->write("Current method does not match this route's accepted methods"), "route_filter_method",  $route->getName(), 405);

				//Filtrar por método negativo
				if($context == "not_method" && in_array($this->getMethod(), $value))
					return new Fault($this->write("Current method does not match this route's accepted methods"), "route_filter_not_method",  $route->getName(), 405);

				//Filtrar por domínio
				if($context == "domain" && !in_array($this->getDomain(), $value))
					return new Fault($this->write("Current domain does not match this route's domain accepted domains"), "route_filter_domain",  $route->getName(), 404);

				//Filtrar por domínio negativo
				if($context == "not_domain" && in_array($this->getDomain(), $value))
					return new Fault($this->write("Current domain does not match this route's accepted domains"), "route_filter_not_domain",  $route->getName(), 404);
				
				//Filtrar por IP
				if($context == "ip" && !in_array($this->getIp(), $value))
					return new Fault($this->write("Client IP does not match this route's accepted IP"), "route_filter_ip",  $route->getName(), 403);

				//Filtrar por IP negativo
				if($context == "not_ip" && in_array($this->getIp(), $value))
					return new Fault($this->write("Client IP does not match this route's accepted IP"), "route_filter_not_ip",  $route->getName(), 403);

				//Filtrar por idioma
				if($context == "language" && !in_array($this->currentLanguage(), $value))
					return new Fault($this->write("Client language does not match this route's accepted language"), "	route_filter_language",  $route->getName(), 403);

				//Filtrar por idioma negativo
				if($context == "not_language" && in_array($this->currentLanguage(), $value))
					return new Fault($this->write("Client language does not match this route's accepted language"), "route_filter_not_language",  $route->getName(), 403);

				//Filtrar por cultura
				if($context == "culture" && !in_array($this->currentCulture(), $value))
					return new Fault($this->write("Client culture does not match this route's accepted culture"), "route_filter_culture",  $route->getName(), 403);

				//Filtrar por cultura negativa
				if($context == "not_culture" && in_array($this->currentCulture(), $value))
					return new Fault($this->write("Client culture does not match this route's accepted culture"), "route_filter_not_language",  $route->getName(), 403);

			}

			return true;
		}

		/**
		 * Exibe o conteúdo padrão de uma página de erro
		 * 
		 * @param Page $page Objeto de página
		 * 
		 * @return null
		 */
		public function showErrorPage (Page $page)
		{
			$errorObject = $page->variables['errorObject'];
			Error($this->write('Page not found'));
		}

		/**
		 * Retorna uma página de erro (se houver personalizada ou padrão)
		 * 
		 * @param int|string $errorCode Código do erro
		 * @param Fault $errorObject Objeto do erro
		 * 
		 * @return Page
		 */
		public function getErrorPage ($errorCode, Fault $errorObject = null)
		{
			$this->setStatus($errorCode);

			$variables = [
				'errorCode' => $errorCode,
				'errorObject' => $errorObject
			];

			$routeName = '_error_' . $errorCode;
			$route = $this->getRoute('_error_' . $errorCode);

			if($route) return $this->getBlock($routeName, $variables);

			return new Page($routeName, $variables, [
					'method' => 'Navigation\\Navigation::showErrorPage'
				]);
		}

		/**
		* Encontrar página/bloco através da URL e retorna o bloco
		*
		* @param string $uri URI a ser processada
		* 
		* @return array
		*/
		public function getRouteByURI ($uri)
		{
			trace('Interpreting URI', 'Navigation\Navigation', $uri);

			//Remover barra do final
			$uri = preg_replace("/\/$/", "", $uri);

			//Remover informações de query
			$uri = preg_replace("/\?[\s\S]{0,}$/", "", $uri);


			//Página encontrada (para o caso de localizar uma página, mas a mesma não ser aceita no contexto atual devido aos filtros)
			$foundRouteName = null;

			//Conferir cada página
			foreach( $this->_pagesData() as $routeName => $page )
			{
				//Se houver um requerimento de "match", verificar se a URL combina
				if( isset($page['match']) && preg_match_all($page['match'], $uri, $matches) )
				{
					$foundRouteName = $this->validateRouteContext($routeName);

					//Verificar se a página é valida no contexto atual, senão, tentar a próxima
					if($foundRouteName instanceof Fault)
						continue;

					//Processar variáveis passadas
					return $this->getBlock($routeName, $this->_processVariables($page, $matches));
				}

				//Se houver um requerimento de "pattern", verificar se a URL combina
				else if( isset($page['pattern']) )
				{
					//Dividir todas as variáveis solicitadas
					$splittedPattern = explode("/", $page['pattern']);

					foreach ($splittedPattern as $index => &$urlBlock)
					{						
						//Exigência para número
						if($urlBlock == "#")
							$urlBlock = "([0-9]+)";

						//Exigência para número com quantidade de caractéres
						if( preg_match("/\#([\s\S]+)/", $urlBlock, $match))
							$urlBlock = "([0-9]{" . $match[1] . "})";

						//Qualquer valor opcional que seja o último
						//if($urlBlock == "?" && $index == count($splittedPattern)-1 )
						//	$urlBlock = "{0,1}([\s\S]{0,})";

						//Qualquer valor opcional
						if($urlBlock == "?") //&& $index < count($splittedPattern)-1
							$urlBlock = "{0,1}(\/{1}[^\/]+?){0,1}";
							//$urlBlock = "{0,1}([^\/]{0,})";

						//Exigência para qualquer valor
						if($urlBlock == "$")
							$urlBlock = "([^\/]+)";

						//Exigência para qualquer valor com quantidade de caractéres
						if( preg_match("/\$([\s\S]+)/", $urlBlock, $match))
							$urlBlock = "([^\/]{" . $match[1] . "})";

						//Exigência para conjunto específico de caractéres
						if( preg_match("/\%([\s\S]+)/", $urlBlock, $match))
							$urlBlock = "([" . $match[1] . "]+)";
					}

					//Montar a expressão regular desse padrão (pattern)
					$matchPattern = "/^" . implode("\/", $splittedPattern) . "$/";

					//Verificar se a expressão combina
					if(preg_match_all($matchPattern, $uri, $matches))
					{
						$foundRouteName = $this->validateRouteContext($routeName);

						//Verificar se a página é valida no contexto atual, senão, tentar a próxima
						if($foundRouteName instanceof Fault)
							continue;

						//Processar variáveis passadas
						return $this->getBlock($routeName, $this->_processVariables($page, $matches));
					}
				}

				//Se houver um requerimento de "static", verificar se a URL combina
				else if(isset($page['static']))
				{
					//Retornar dados da página caso a página seja a solicitada
					if($uri == $page['static'])
					{
						$foundRouteName = $this->validateRouteContext($routeName);

						//Verificar se a página é valida no contexto atual, senão, tentar a próxima
						if($foundRouteName instanceof Fault)
							continue;

						return $this->getBlock($routeName);
					}
				}
			}

			/*-------- A esse ponto, nenhuma rota foi retornada, portanto considerar como página não encontrada */

			//Verificar se alguma rota foi encontrada, mas estava inválida no contexto atual
			if(!empty($foundRouteName))
			{
				//Retornar a página de erro correspondente
				return $this->getErrorPage($foundRouteName->getDetails(), $foundRouteName);
			}

			//Retornar a página de erro correspondente
			return $this->getErrorPage(404);
		}
		
		/**
		* Resgatar IP do usuário atual
		*
		* @return string
		*/
		static public function getIP ()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP']))
			    return $_SERVER['HTTP_CLIENT_IP'];
			elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			    return $_SERVER['HTTP_X_FORWARDED_FOR'];
			elseif(!empty($_SERVER['REMOTE_ADDR']))
			    return $_SERVER['REMOTE_ADDR'];
			else
				return 'undefined';
		}

		/**
		 * Resgata um bloco pronto
		 * 
		 * @param string $routeName Nome da rota/bloco
		 * @param array $variables (Opcional) Variáveis dessa rota/bloco
		 * 
		 * @return Page
		 */
		public function getBlock ($routeName, $variables = [])
		{
			return new Page($routeName, $variables);
		}
	}