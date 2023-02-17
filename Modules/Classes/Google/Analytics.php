<?php
/**
* Funções do Analytics do Google
*
* @package	Google
* @author 	Lucas/Postali
*/	
	
	namespace Google;

	class Analytics extends _Google
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
				$this->addScriptTag($page);
			}
		}

		/**
		* Adicionar o link de acompanhamento do Google
		*
		* @param resource $page Recebe a página atual
		* @param string $code Código do ID de acompanhamento
		*
		* @return null
		*/
		public function addScriptTag (\Navigation\Page $page, $code = null)
		{	
			//Buscar configurações do Analytics
			$config = $this->_config('google');

			//Verificar se os dados de configuração estão presentes
			if(!isset($config['analytics']) || count($config['analytics']) == 0)
				return new \Fault('Configuration of Google Analytics is missing', 'google-analytics-config-missing', $this);

			//Verifica se o código solicitado existe
			if( $code !== null && ( !isset($config['analytics'][$code])))
				return new \Fault('Analytics selected code is missing', 'google-analytics-code-missing', $this);

			//Se foi solicitado um código específico, resgatá-lo
			if($code !== null)
				$id = $config['analytics'][$code];

			//Se não, resgatar a primeira posição
			else
				$id = $config['analytics'][array_keys($config['analytics'])[0]];

			//Verificar ID
			if(!preg_match("/^[\s\S]{2}\-[0-9]+?\-[\s\S]{1,}$/", $id))
				return new \Fault('Analytics ID is malformed', 'google-analytics-id-no-match', $this);

			//Criar e incluir a Tag de Script
			$scriptTagParams = array(
				'src' => "https://www.googletagmanager.com/gtag/js", //?id=" . $id,
				'async' => null,
			);
			$page->addTagHeadJS(null, null, $scriptTagParams);

			//Criar e incluir a Tag de Script
			$content = "window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '$id');";
			$page->addTagHeadJS(null, null, null, $content);
		}

	}

?>