<?php
/**
* Trait com as funções de módulo
*
* @package	Admin
* @author 	Lucas/Postali
*/
	namespace CMS;

	trait _Module
	{
		/**
		* Carregar caminho do um módulo
		*
		* @param string $variables Variáveis de conexão
		*
		* @return array
		*/
		public function getContentBlock ($variables)
		{
			$blockPath = "";
			
			//Se houver um módulo e um bloco, carregar o caminho
			if(isset($variables['module']) && $variables['module'] != "" && isset($variables['block']) && $variables['block'] !="")
				$blockPath = $this->getPath(self::BOARD_PATH . $variables['module'] . '/' . preg_replace("/\\$/", "", $variables['block']) . '.php');
			
			//Caso não houver módulo e bloco, carregar o módulo padrão
			if(!file_exists($blockPath))
				$blockPath = $this->getPath(self::BOARD_PATH . self::DEFAULT_BOARD);

			//Se o bloco padrão não existir
			if(!file_exists($blockPath))
				$this->Error($this->write("Default page doesn't exist", 'admin'), true);

			return $blockPath;
		}

		/**
		* Carregar JSON de configuração
		*
		* @param string $filename Nome do arquivo de configuração
		*
		* @return array
		*/
		public function loadConfigJson ($filename)
		{
			//Normalizar caminho
			$path = $this->getPath('Config' . '/' . preg_replace("/\\/|\\\\/", "", $filename));

			//Verificar se arquivo existe
			if(!file_exists($path))
				return false;

			//Resgatar conteúdo do arquivo
			$fileContent = @file_get_contents($path);

			//Verificar se o conteúdo é válido
			if(!$fileContent)
				return false;

			//Decodificar conteúdo
			$decodedContent = json_decode($fileContent, true);

			//Verificar se o conteúdo foi parseado corretamente
			if(json_last_error() > 0)
				return false;

			return $decodedContent;			
		}

		/**
		* Instanciar módulos dentro de uma interface
		*
		* @return object
		*/
		public function instanceModules (InterfaceCMS &$interface, $skipPermissions = false)
		{
			$modules = $this->_listConfig('admin');

			//Para cada módulo
			foreach ($modules as $moduleId => $values)
			{
				$values['module'] = "\\" . $values['module'];
				$module = new $values['module']();
				$module->{$values['method']}($interface);
				unset($module);
			}
			return $interface;
		}

	}

?>