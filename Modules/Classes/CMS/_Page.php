<?php
/**
* Trait com as funções de página
*
* @package	Admin
* @author 	Lucas/Postali
*/
	namespace CMS;

	use \Navigation\Page;
	
	trait _Page
	{
		/**
		* Iniciar página
		*
		* @param object $page Instância da página
		*
		* @return null
		*/
		protected function _pageStart (Page $page)
		{
			//Incluir título
			$page->variables['pageTitle'] = $this->write("Administration", 'admin') .
				( isset($this->variables['pageTitle']) ? " | " . $this->variables['pageTitle'] : '');

			//Incluir dados
			$this->_pageIncludeFiles($page);
		}

		private $_CSS = 
		[			
			//"login-4.min.css"
			//"style.bundle.min.css",
			//"assets/plugins/global/plugins.bundle.css",
			//'plugins/custom/prismjs/prismjs.bundle.css',
		];

		private $_JS = 
		[
			"knockout-min.js",
			//"assets/plugins/global/plugins.bundle.js",
			//"js-cookie/src/js.cookie.js",
			//"scripts.bundle.js",
			//'plugins/custom/datatables/datatables.bundle.js',
		];


		private function _iterateIncludeJS ($folder, Page $page)
		{
			foreach(scandir($this->getPath('public/' . $folder)) as $item)
			{
				if($item == "." || $item == "..") continue;

				if(is_dir($this->getPath('public/' . $folder . "/" . $item)))
				{
					$this->_iterateIncludeJS($folder . "/" . $item, $page);
				}
				else if(preg_match("/\.js$/i", $item))
				{
					$page->addTagHeadJS($folder . "/". $item);
					trace('Including JS', 'CMS', $folder . "/". $item);
				}
			}

		}

		private function _iterateIncludeCSS ($folder, Page $page)
		{
			foreach(scandir($this->getPath('public/' . $folder)) as $item)
			{
				if($item == "." || $item == "..") continue;

				if(is_dir($this->getPath('public/' . $folder . "/" . $item)))
				{
					$this->_iterateIncludeCSS($folder . "/" . $item, $page);
				}
				else if(preg_match("/\.css$/i", $item))
				{
					$page->addTagHeadCSS($folder . "/". $item);
					trace('Including CSS', 'CMS', $folder . "/". $item);
				}
			}

		}

		/**
		* Incluir dependências
		*
		* @param object $page Instância da página
		*
		* @return null
		*/
		private function _pageIncludeFiles (Page $page)
		{
			foreach ($this->_CSS as $file)
				$page->addTagHeadCSS("cms/$file");

			foreach ($this->_JS as $file)
				$page->addTagHeadJS("cms/$file");
			
			$this->_iterateIncludeJS('cms/custom', $page);

			$this->_iterateIncludeCSS('cms/custom', $page);
		}

		/**
		* Retorna caminho do logo do CMS
		*
		* @return string
		*/
		public function getLogoImage ()
		{
			return self::IMG_LOGO_PATH;
		}

		/**
		* Retorna caminho da imagem de carregamento do CMS
		*
		* @return string
		*/
		public function getLoadingImage ()
		{
			return self::IMG_LOADING_PATH;
		}

		/**
		* Retorna caminho de fundo do login
		*
		* @return string
		*/
		public function getLoginImage ()
		{
			return self::IMG_LOGIN_PATH;
		}

		public function getStyle ($key)
		{
			$config = $this->_config('cms');

			if(!isset($config['style'][$key]) || empty($config['style'][$key]))
				return '-';

			return $config['style'][$key];
		}
	}