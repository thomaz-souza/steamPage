<?php
/**
* Adição de conteúdo extra nas páginas
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;

	Trait _PageExtraContent
	{
		/**
		 * @var array Assets a serem carregados
		 */
		private $_assets = ['header' => [], 'footer' => []];

		/**
		 * Resgata os assets a serem incorporadas nessa página
		 * 
		 * @return array
		 */
		public function getHeaderAssets ()
		{
			if(empty($this->_assets['header']))
				$this->_assets['header'] = $this->getStructure('headerAssets', []);

			return $this->_assets['header'];
		}

		/**
		 * Resgata os assets a serem incorporadas no footer dessa página
		 * 
		 * @return array
		 */
		public function getFooterAssets ()
		{
			if(empty($this->_assets['footer']))
				$this->_assets['footer'] = $this->getStructure('footerAssets', []);

			return $this->_assets['footer'];
		}

		/**
		 * @var array Classes sendo incorporadas
		 */
		private $_usingClasses = [];

		/**
		 * Resgatar classes sendo utilizadas
		 * 
		 * @return array
		 */
		public function getUsingClasses ()
		{
			return $this->_usingClasses;
		}

		/**
		* Instanciar uma classe dentro do contexto e incluir no JavaScript (Caso haja funções que recebem as variáveis $data e $transaction)
		*
		* @param string $class Classe a ser instanciada
		* @param bool $reload (Opcional) Permite que a classe seja recarregada 
		*
		* @return null
		*/
		public function using ($class, $reload = false)
		{
			if(in_array($class, $this->_usingClasses) && !$reload)
				return trace("Won't re-instance using class '$class'", 'Navigation\Page', $class, TRACE_WARNING);

			//Verifica se a classe passada existe
			if(class_exists($class))
			{
				if(!in_array($class, $this->_usingClasses))
					$this->_usingClasses[] = $class;

				$classFile = preg_replace("/\\\\|\\//", DIRECTORY_SEPARATOR, $class);

			 	if(file_exists($this->getPath("Controllers/$classFile.php")))
				{
					//Retirar barras (caso haja namespace) do nome da classe
					$classVar = explode("\\", $class);
					$classVar = end($classVar);

					//Ignorar caso a classe já tenha sido carregada
					if(isset($this->$classVar))
					{
						if(!$reload)
							return trace("Won't re-instance using class '$class'", 'Navigation\Page', $class, TRACE_WARNING);
						trace("Re-instancing using class '$class'", 'Navigation\Page', $class, TRACE_WARNING);
					}else
					{
						trace("Instancing using class '$class'", 'Navigation\Page', $class);
					}

					//Instanciar um ReflectionClass e pegar a lista de métodos públicos/disponíveis
					$instance = new \ReflectionClass($class);

					$methodsList = $instance->getMethods(\ReflectionMethod::IS_PUBLIC);

					//Nova lista vazia que receberá os métodos a serem incorporados
					$availableMethods = array();

					//Para cada método público da classe
					foreach ( $methodsList as $method )
					{
						//Caso o método seja da classe em si (e não do parent)
						if( $method->class == $class )
						{
							//Resgatar os parâmetros dessa classe
							$parameters = $instance->getMethod($method->name)->getParameters();

							//Verificar se há uma função de construção que recebe um objeto do tipo Page
							if($method->name == "__construct" && count($parameters) > 0)
							{
								//Instanciar classe dentro do contexto
								if($parameters[0]->getType()->getName() == "Navigation\\Page")
								{
									eval('$this->' . $classVar . ' = new \\' . $class . '($this);');
								}
							}
							
							//Se os parâmetros forem $data e $transaction, a função está pronta para receber requisições
							if(count($parameters) > 1)
							{
								if($parameters[1]->getType() && $parameters[1]->getType()->getName() == "Navigation\\JsonTransaction")
								{
									//Resgatar o nome do mapa
									$mapName = $method->name."_map";

									//Se houver um mapa, incorporá-lo
									$map = $instance->hasProperty($mapName) ? $instance->getProperty($mapName)->getValue(new $class()) : [];

									//Adicionar essa função à lista
									$availableMethods[$method->name] = $map;
								}
							}
						}
					}

					//Caso a classe não tenha sido instanciada, instanciá-la sem a página
					if(!isset($this->{$classVar}))
						$this->{$classVar} = new $class();

					//Se não houver métodos transacionais disponíveis, não incluir no JavaScript
					if(count($availableMethods) == 0)
					{
						trace("Using class '$class' won't be incorporated as it has no available transactional functions", 'Navigation\Page', $class);
						return;
					}

					trace("Incorporating transactional functions from class '$class'", 'Navigation\Page', array_keys($availableMethods));

					//Iniciar a criação do objeto JavaScript
					$script = "var $classVar={";
					
					//Para cada método disponível, criar uma função em JavaScript
					foreach ( $availableMethods as $methodName => $methodMap )
					{
						//Adaptar campos se necessário
						foreach ($methodMap as &$rules)
						{
							//Se foi criado um nome para o campo, traduzí-lo
							if(isset($rules['name']))
								$rules['name'] = $this->write($rules['name'], "validation");
						}					

						//Mapa de validação
						$jsonMap = json_encode($methodMap);

						//Adaptar tipos para Javascript
						$replaceFrom = array('"type":"integer"','"type":"array"','"type":"float"');
						$replaceTo = array('"type":"number"','"type":"object"','"type":"number"');
						$jsonMap = str_replace($replaceFrom, $replaceTo, $jsonMap);

						//Adaptar expressões regulares para Javascript
						$jsonMap = preg_replace_callback("/\"([not]{0,}match)\":\"\\\\\/([\s\S]+?)\\\\\/\"/", function ($m)
						{
							$m[2] = preg_replace("/\\\\\\\\/", "\\", $m[2]);
							return "\"$m[1]\":/$m[2]/gi";
						},
						$jsonMap);

						$classNameEmbed = preg_replace("/\\\\|\\//", "/", $class);

						//Incluir metodo no JavaScript
						$script .= "$methodName:function(d,c,w,u,o){let e='$classNameEmbed',m='{$methodName}',a=" . $jsonMap . ";return d==null&&c==null?new request.TM(e,m,a):request.tran(e,m,a,d,c,w,u,o)},";
					}

					//Fechar o objeto JavaScript
					$script .= "};";
				}
			}
			else
			{	
				//Caso não tenha sido possível incorporar, incluir uma mensagem de erro
				$script = "console.error(\"" . $this->write("Class '%s' could not be incorporated because it was not found", "validation", $class) . "\");";		
			}
			$this->addTagHeadJS(null, null, null, $script);
		}

		/**
		 * Adicionar JS padrão para requisições
		 * 
		 * @return null
		 */
		private function _addUsingJS ()
		{
			//Incluir o arquivo JS
			$this->addAsset('request');

			//Listar todos os erros
			$validateFaults = array();
			foreach( \FaultTrait::$faultList as $code => $message)
				$validateFaults[$code] = $this->write($message, null, "%s");

			//Incluir URL e lista de erros no corpo do Javascript
			$tagContent = "request.u=\"" . $this->getURL() ."\";request.errList=" . json_encode($validateFaults) . ";request.tk=\"" . $this->transactionToken() . "\";";

			$this->addTagHeadJS(null, null, null, $tagContent);
		}

		/**
		 * Processar linkds dinâmicos
		 * 
		 * @param string $content Conteúdo do bloco
		 * 
		 * @return string
		 */
		private function processLinks ($content)
		{
			//Ativar caminhos absolutos em URL relativas
			$content = preg_replace_callback("/((action|href|src|srcset)[\s]{0,}=[\s]{0,}[\"\']+)\~([\@]{0,1})\\/{0,1}([^\"]{0,}+)/", function ($m)
				{
					//Se houver @ após o ~, interpretar como bloco/rota
					if($m[3] == '@')
					{
						//Caso não tenha nenhuma rota solicitada, resgatar a própria página
						if(empty($m[4])) return $m[1] . $this->getPageURL();

						//Caso haja nome do bloco, incluir bloco e suas variáveis
						$uri = explode("/", $m[4]);
						$routeName = array_shift($uri);

						preg_match('/^([A-z_-]+)([\s\S]{0,})/i', $routeName, $addMatch);
						$routeName = $addMatch[1];

						$res = $m[1] . $this->getRouteURL($routeName, $uri);

						if(!empty($addMatch[2]))
							$res .= $addMatch[2];

						return $res;
					}

					//Retornar URL formatada
					return $m[1] . $this->formatURL($this->getURL(), $m[4]);
				},
			$content);

			//Ativar caminhos absolutos em URL relativas do tipo url
			$content = preg_replace_callback("/(url[\s]{0,})\(\~\\/{0,1}/", function ($m)
				{
					return $m[1]. "(" . $this->getURL() . "/";
				},
			$content);

			return $content;
		}

		/**
		 * Executar renderizadores
		 * 
		 * @param string $content Conteúdo atual da página
		 * @param string $type Tipo de renderizador
		 * 
		 * @return string
		 */
		protected function _executeRenderers ($content, $type)
		{
			//Executar funcionalidades extras de alteração de página
			foreach($this->_listConfig($type) as $renderers)
			{
				if(!is_array($renderers)) $renderers = [$renderers];
				
				foreach ($renderers as $renderer)
				{
					trace("Including page renderer", "Navigation\\Page", $renderer);

					$return = $this->parseMethod($renderer, true, $this, $content);
					if(!empty($return))
						$content = $return;
				}
			}

			return $content;
		}

		/**
		* Incluir conteúdo extra
		*
		* @param string $content Conteúdo do bloco
		* 
		* @return string
		*/
		private function _includeExtraContent ($content)
		{	
			//Incluir as TAGs de request
			$this->_addUsingJS();
			
			//Executar renderizadores externos anteriores
			$content = $this->_executeRenderers($content, 'before_page_render');

			//Incluir super tags
			$content = $this->includeSuperTags($content);

			//Processar links
			$content = $this->processLinks($content);

			//Executar renderizadores externos posteriores
			$content = $this->_executeRenderers($content, 'after_page_render');

			return $content;
		}
	}