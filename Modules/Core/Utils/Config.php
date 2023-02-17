<?php
/**
* Utilidades simples de arquivos de configurações
*
* @package	Core/Utils
* @author 	Lucas/Postali
*/
	namespace Utils;
	
	trait Config
	{
		/**
		* Lê o conteúdo de um arquivo de configuração e parseia o JSON em Array
		*
		* @param string $key Recebe o nome do conjunto de configurações (arquivo sem a extensão)
		*
		* @return array
		*/
		private function _readConfigFile ($key)
		{
			//Cria o nome do arquivo
			$file = MAIN_FOLDER . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . $key . '.config';

			//Se o arquivo não existir, retornar um erro
			if(!file_exists($file)){
				$errorMessage = $this->write("The requested config file '%s' is missing.", "validation", array($key, json_last_error_msg()));
				$this->Error($errorMessage);
			}
			
			//Ler o arquivo
			$config =  json_decode(file_get_contents($file), true);

			//Se não for possível parsear o arquivo, retornar um array vazio
			if(json_last_error() > 0){
				$errorMessage = $this->write("Could not load the config file '%s'. Parsing error: '%s'", "validation", array($key, json_last_error_msg()));
				$this->Error($errorMessage);
			}

			//Incluir os dados na variável de configurações e retornar
			$GLOBALS['ConfigContent'][$key] = $config;
			return $config;
		}

		/**
		* Busca os dados de um arquivo de configuração.
		*
		* @param string|array $key Recebe o nome do conjunto de configurações (arquivo sem a extensão) ou um caminho
		* @param mixed $default (Opcional) Valor a ser retornado por padrão caso não seja encontrado o caminho solicitado
		* 
		* @return array
		*/
		protected function _config ($key, $default = null)
		{
			if(!is_array($key))
				$key = explode("/", $key);

			$file = array_shift($key);

			//Se o conjunto de configurações já foi carregado, retorná-lo, senão, carregá-los
			$config = isset($GLOBALS['ConfigContent'][$file]) ? 
				$GLOBALS['ConfigContent'][$file] : 
				$this->_readConfigFile($file);

			if(!empty($key))
				$config = $this->_loadPath($config, $key);

			if($config === null)
				return $default;

			return $config;
		}

		/**
		* Carrega um caminho
		*
		* @param array $array conteúdo do config
		* @param array $paths caminhos
		*
		* @return mixed
		*/
		private function _loadPath ($array, $paths)
		{
			$path = array_shift($paths);

			if(!isset($array[$path]))
				return null;

			$newArray = $array[$path];
			if(!empty($paths))
				return $this->_loadPath($newArray, $paths);
			return $newArray;
		}

		/**
		* Resgata dados de um arquivo de configuração
		*
		* @param string|array $key Recebe o nome do conjunto de configurações (arquivo sem a extensão) ou um caminho
		* @param mixed $default (Opcional) Valor a ser retornado por padrão caso não seja encontrado o caminho solicitado
		*
		* @return array
		*/
		protected function _getConfig ($key, $default = null)
		{
			$config = $this->_config($key, $default);

			if(isset($config['_config']))
				unset($config['_config']);

			return $config;
		}

		/**
		* Função recursiva para inserir valor num array. Retorna o array inteiro com a chave alterada
		*
		* @param array $array Conteudo do array
		* @param array $paths Caminhos
		* @param mixed $value Valor a ser inserido
		* @param bool $remove Se true, a chave é removida
		*
		* @return array
		*/
		private function _setOnPath ($array, $paths, $value, $remove = false)
		{
			$path = array_shift($paths);

			if(!empty($paths))
				$array[$path] = $this->_setOnPath($array[$path], $paths, $value, $remove);

			else if($remove === true)
				unset($array[$path]);

			else if(!empty($path))
				$array[$path] = $value;

			else
				$array = array_merge($array, $value);

			return $array;
		}

		/**
		* Define uma configuração em um caminho
		*
		* @param $path Caminho do arquivo
		* @param $value Valor a ser inseridor
		* @param $key Chave a ser alterada
		* @param $remove Se true, a chave é removida
		*
		* @return array
		*/
		protected function _setConfig ($path, $value, $key, $remove = false)
		{
			$path = explode("/", $path);
			if(!empty($key))
				$path[] = $key;

			$module = array_shift($path);
			$config = $this->_config($module);

			$newConfig = $this->_setOnPath($config, $path, $value, $remove);

			//Cria o nome do arquivo
			$file = MAIN_FOLDER . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . $module . '.config';

			file_put_contents($file, json_encode($newConfig, JSON_PRETTY_PRINT));

			return $GLOBALS['ConfigContent'][$module] = $newConfig;
		}

		/**
		* Retorna lista de configurações extras
		*
		* @return array
		*/
		protected function _listConfig ($key)
		{
			//Obter lista de módulos
			$configFileList = scandir($this->getPath('Config'));

			//Módulos
			$extra = array();

			foreach ($configFileList as $configFile)
			{
				//Pular arquivos que não seja .config
				if(!preg_match("/^([\s\S]+?)\.config$/", $configFile, $file))
					continue;

				//Obter conteúdo do módulo
				$configContent = $this->_config($file[1]);

				//Caso tenha havido algum erro ou não haja a chave _config, pular
				if(!$configContent || !isset($configContent['_config']))
					continue;

				//Obter dados de configurações
				$config = $configContent['_config']; 

				//Se a chave em questão não existe
				if(!isset($config[$key]))
					continue;

				//foreach ($config[$key] as $button)
				$extra = array_merge($extra, $config[$key]);
			}

			return $extra;
		}
	}