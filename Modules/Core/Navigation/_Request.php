<?php
/**
* Funções de web
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;

	use CMS\CMS;

	trait _Request
	{
		/**
		 * Definir status
		 * 
		 * @param int $status Status a ser inserido
		 * 
		 * @return Page
		 */
		public function setStatus ($status)
		{
			http_response_code($status);
		}

		/**
		 * Resgata o status dessa página
		 * 
		 * @return int
		 */
		public function getStatus ()
		{
			return http_response_code();
		}

		/**
		* Alterar o contentType da página
		*
		* @param string $contentType Mime do conteúdo de resposta
		* 
		* @return null
		*/
		public function setContentType ($contentType)
		{
			$this->includeHeader("Content-type: " . $contentType);
		}

		/**
		 * Resgata método atual
		 * 
		 * @return string
		 */
		public function getMethod ()
		{
			return $_SERVER['REQUEST_METHOD'];
		}

		/**
		* Resgata os cabeçalhos dessa conexão
		*
		* @return array
		*/
		public function getHeaders ()
		{
			$headers = [];
			foreach ($_SERVER as $name => $value)
				if (substr($name, 0, 5) == 'HTTP_')	 
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;

			return $headers;
		}

		/**
		 * Resgata um valor de cabeçalho enviado
		 * 
		 * @param string $name Name of the variable in header
		 * 
		 * @return string
		 */
		public function getHeader ($name)
		{
			$headerName = 'HTTP_' . strtoupper($name);
			return isset($_SERVER[$headerName]) ? $_SERVER[$headerName] : null;
		}

		/**
		 * Adiciona um valor ao header
		 * 
		 * @param type $header 
		 * 
		 * @return type
		 */
		public function includeHeader ($header)
		{
			header($header);
		}

		/**
		* Inclui na resposta os cabeçalhos enviados
		*
		* @param array $headers Recebe o conjunto de cabeçalhos a serem enviados
		* 
		* @return null
		*/
		protected function includeHeadersArray ($headers)
		{
			foreach($headers as $header)
				$this->includeHeader($header);
		}

		/**
		 * Resgata dados da conexão
		 * 
		 * @param string|bool $source (Opcional) Fonte de dados (POST, GET, JSON) ou "true" para retornar dados raw
		 * 
		 * @return mixed
		 */
		public function getData ($source = null)
		{
			//Buscar dados de requisição de acordo com o solicitado
			$inputContent = file_get_contents('php://input');
			$decodedContent = json_decode($inputContent, true);

			if($source === true)
				return $inputContent;

			else if(($source === null && json_last_error() == 0) || $source === "JSON")
				return $decodedContent;
			
			else if($source === "POST" || count($_POST) > 0)
				return array_merge_recursive($_POST, $_FILES);

			else if($source === "GET" || count($_GET) > 0)
				return array_merge_recursive($_GET, $_FILES);

			else
				return $inputContent;		
		}

		/**
		* Redirecionar a página através do cabeçalho location
		*
		* @param string $url URL para a qual deve ser redirecionada
		* @param integer $code Código do redirecionamento (301, 302, etc.)
		*
		* @return null
		*/
		public function redirectTo ($url, $code = 302)
		{
			trace("Redirecting connection to '$url'", "Navigation\\Navigation", $code);
			
			//Substituir ~ pelo endereço do site, se houver
			$url = preg_replace("/^\~[\/]{0,1}/", $this->getUrl() . "/", $url);

			//Redirecionar e parar o código
			header('Location: ' . $url, true, $code);
			exit();
		}

		/**
		* Checar a condição de um cookie
		*
		* @param string $cookieName Nome do Cookie
		* @param mixed $cookieContent Conteudo esperado do Cookie.
		* @param string $cookieMatch Expressão regular a ser checada.
		*
		* @return mixed
		*/
		public function checkCookie ($cookieName, $cookieContent = null, $cookieMatch = null)
		{
			//Se cookieContent for apenas um valor booleano
			if(isset($cookieContent) && !isset($cookieMatch) && gettype($cookieContent) == "boolean")
			{	
				//Verificar se a existência do cookie corresponde com o esperado
				if( isset($_COOKIE[$cookieName]) !== $cookieContent )
					return 'Cookie existence does not match with expected';
			}
			else
			{
				//Verificar se o cookie existe
				if(!isset($_COOKIE[$cookieName]))
					return 'Cookie not set';
				
				//Caso tenha sido definido o $cookieMatch e o $cookieContent, avaliar se o o match corresponde ao esperado em $cookieContent
				else if(isset($cookieMatch) && isset($cookieContent) && preg_match($cookieMatch, $_COOKIE[$cookieName]) != $cookieContent)
					return 'Cookie value does not match as expected';

				//Caso tenha sido definido apenas o $cookieMatch, avaliar se o conteúdo do cookie corresponde à expressão regular
				else if(isset($cookieMatch) && !isset($cookieContent) && !preg_match($cookieMatch, $_COOKIE[$cookieName]))
					return 'Cookie value does not match';

				//Caso tenha sido definido apenas o $cookieContent, avaliar se o conteúdo do cookie corresponde ao valor de $cookieContent
				else if(isset($cookieContent) && !isset($cookieMatch) && $_COOKIE[$cookieName] != $cookieContent)
					return 'Cookie does not correspond to required value';
			}

			//Caso nenhum erro tenha sido apresentado, retornar true
			return true;
		}

		/**
		* Redirecionar a página caso um cookie não satisfaça as necessidades passadas
		*
		* @param string $url URL para a qual deve ser redirecionada
		* @param string $cookieName Nome do Cookie
		* @param mixed $cookieContent Conteudo esperado do Cookie.
		* @param string $cookieMatch Expressão regular a ser checada.
		*
		* @return mixed
		*/
		public function redirectOnCookie ($url, $cookieName, $cookieContent = null, $cookieMatch = null)
		{
			//Se o checkCookie não retornar true, redirecionar a página para a URL desejada
			if($this->checkCookie($cookieName, $cookieContent, $cookieMatch) !== true)
				$this->redirectTo($url, 302);
		}

		/**
		* Forçar a conexão
		*
		* @param $type string Tipo de redirecionamento. Pode ser "domain", "protocol" ou "url"
		*
		* @return null
		*/
		protected function _forceConnection ($type)
		{
			switch ($type)
			{
				//Caso o tipo for "domain", verificar se o domínio é o mesmo. Se não for, redirecionar
				case 'domain':
					if( $this->getDomain() != $this->_navigationData()['domain'] )
					{
						$url = $this->getProtocol() . "://" . $this->formatURL([$this->_navigationData()['domain'], $this->getURI()]);
						$this->redirectTo($url, 302);
					}

				break;

				//Caso o tipo for "protocol", verificar se o protocol é o mesmo. Se não for, redirecionar
				case 'protocol':

					if( $this->getProtocol() != $this->_navigationData()['protocol'] )
					{
						$url = $this->_navigationData()['protocol'] . "://" . $this->formatURL([$this->getDomain(), $this->getURI()]);
						$this->redirectTo($url, 302);
					}

				break;

				//Caso o tipo for "url", verificar se toda a URL está válida (protocolo e domínio). Se não for, redirecionar
				case 'url':
					if( $this->getProtocol() != $this->_navigationData()['protocol'] || preg_replace("/\\//", "", $this->getDomain()) != preg_replace("/\\//", "", $this->_navigationData()['domain']) )
					{
						$url = $this->_navigationData()['protocol'] . "://" . $this->formatURL([$this->_navigationData()['domain'],  $this->getURI()]);
						$this->redirectTo($url, 302);
					}
				break;
			}
		}

		/**
		* Exige que a requisição venha do admin
		*
		* @param bool $die Se true, interrompe a execução, se não, retorna o status
		* 
		* @return bool
		*/
		public function requireAdmin ($die = false)
		{
			if(CMS::validateSession() !== true)
			{
				http_response_code(403);
				if($die) die();
				return false;
			}
			return true;
		}

		/**
		* Exige que o admin possua determinadas permissões
		*
		* @param string|array $permissions Lista de permissões exigidas
		* @param bool $die Se true, interrompe a execução, se não, retorna o status
		* 
		* @return bool
		*/
		public function requireAdminPermission ($permissions, $die = false)
		{
			if(!$this->requireAdmin($die))
				return false;

			$cms = new CMS();
			$userPermissions = $cms->getUserPermissions();

			//Normalizar permissões
			if(!is_array($permissions))
				$permissions = [$permissions];

			//Para cada permissão solicitada
			foreach ($permissions as $permission)
			{	
				//Verificar se a mesma existe
				if(!in_array($permission, $userPermissions))
				{
					//Se não existir, retornar erro
					http_response_code(403);
					if($die) die();
					return false;
				}
			}

			return true;			
		}
	}