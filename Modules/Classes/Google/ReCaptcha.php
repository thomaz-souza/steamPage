<?php
/**
* Funções do ReCaptcha do Google
*
* @package	Google
* @author 	Lucas/Postali
*/	
	
	namespace Google;

	class ReCaptcha extends _Google
	{	
		//Página atual
		private $currentPage;

		function __construct (\Navigation\Page $page = null)
		{
			//Construir a classe acima
			parent::__construct();

			//Incluir Tag na página
			if($page !== null)
			{
				$this->currentPage = $page;
				$this->_addScriptTag($page);
			}
		}

		/**
		* Valida se o Recaptcha está configurado
		*
		* @return bool
		*/
		public function isAvailable ()
		{
			//Buscar configurações do ReCaptcha
			 $config = $this->_config('google');
			
			//Verificar se os dados de configuração estão presentes
			if(!isset($config['recaptcha']) || empty($config['recaptcha']['public']) || empty($config['recaptcha']['secret']))
				return false;

			return true;
		}

		/**
		* Adicionar o link do script do google
		*
		* @param resource $page Recebe a página atual
		*
		* @return null
		*/
		private function _addScriptTag (\Navigation\Page $page)
		{
			//Criar e incluir a Tag de Script
			$scriptTagParams = array(
				'src' => "https://www.google.com/recaptcha/api.js",
				'async' => null,
				'defer' => ''
			);
			$page->addTagJS(null, null, $scriptTagParams);
		}

		/**
		* Retorna o código checkbox padrão do ReCaptcha
		*
		* @return string
		*/
		public function checkbox ()
		{
			//Buscar configurações do ReCaptcha
			$config = $this->_config('google');
			
			//Verificar se os dados de configuração estão presentes
			if(!$this->isAvailable())
				return new \Fault('Configuration of Google ReCaptcha is missing', 'google-recaptcha-config-missing');

			//Criar e retornar a div do recaptcha
			$tagParams = array(
				'class' => "g-recaptcha",
				'data-sitekey' => $config['recaptcha']['public']
			);			
			return $this->currentPage->createTag('div', $tagParams, '')->mount();
		}

		/**
		* Retorna o código do ReCaptcha invisível para botão
		*
		* @param string $tagName Recebe o nome da Tag
		* @param string $content Conteúdo da Tag
		* @param string $tagParams Parâmetros da Tag
		*
		* @return string
		*/
		public function invisibleButton ($tagName, $content, $tagParams = array())
		{
			//Verificar se foi enviado um data-callback
			if(!isset($tagParams['data-callback']) || $tagParams['data-callback'] == "")
				return new \Fault('Google ReCaptcha Invisible Button requires \'data-calback\' parameter.', 'google-recaptcha-invisible-callback', $this);

			//Buscar configurações do ReCaptcha
			 $config = $this->_config('google');
			
			//Verificar se os dados de configuração estão presentes
			if(!$this->isAvailable())
				return new \Fault('Configuration of Google ReCaptcha is missing', 'google-recaptcha-config-missing');

			//Adicionar classe do reCaptcha
			if(isset($tagParams['class']))
				$tagParams['class'] .= " g-recaptcha";
			else
				$tagParams['class'] = "g-recaptcha";

			//Adicionar Sitekey
			$tagParams['data-sitekey'] = $config['recaptcha']['public'];

			//Retornar botão
			return $this->currentPage->createTag($tagName, $tagParams, $content)->mount();
		}

		/**
		* Valida se o código está correto
		*
		* @param string $response Código de resposta enviado pelo usuário
		* @param string $returnComplete Se TRUE retorna o conteúdo completo da resposta, senão, retorna apenas TRUE ou FALSE
		*
		* @return mixed
		*/
		public function validate ($response, $returnComplete = false)
		{
			//Buscar configurações do ReCaptcha
			$config = $this->_config('google');
			
			//Verificar se os dados de configuração estão presentes
			if(!$this->isAvailable())
				return new \Fault('Configuration of Google ReCaptcha is missing', 'google-recaptcha-config-missing');
			
			//Definir parâmetros a serem enviados
			$params = array(
				'response'	=> $response,						//Resposta do usuário
				'secret' 	=> $config['recaptcha']['secret'],	//Segredo
				'remoteip' 	=> $this->getIP()					//IP do usuário requisitando a informação
			);

			//Consultar resposta
			$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?" . http_build_query($params));

			//Decodificar a resposta
			$response = json_decode($response, true);

			//Se foi solicitada a resposta completa, retornar
			if($returnComplete === true)
				return $response;

			//Retornar resposta de sucesso
			return $response['success'];
		}
	}