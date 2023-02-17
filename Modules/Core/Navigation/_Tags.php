<?php
/**
* Classe para Criação de Tags Automáticas
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;

	Trait _Tags
	{
		/**
		 * @var array Tags a serem carregados
		 */
		private $_tags = [];

		/**
		* Cria uma Tag
		*
		* @param string $tagName Recebe o nome da Tag
		* @param array $tagParam Recebe os parâmetros da Tag
		* @param string $content Recebe o conteúdo da Tag
		*
		* @return string
		*/		
		static public function createTag ($tagName, $properties = [], $content = null)
		{
			return new Tag($tagName, $properties, $content);
		}

		/**
		* Adiciona uma Tag ao contexto
		*
		* @param string $tagName Recebe o nome da Tag
		* @param string $placement Local onde será colocado
		* @param string $content Recebe o conteúdo da Tag
		* @param array $tagParam Recebe os parâmetros da Tag
		* @param bool $allowRepeat Se TRUE permite que a mesma Tag seja inserida mais de uma vez
		*
		* @return mixed
		*/
		public function addTag ($tagName, $placement, $content = false, $properties = array(), $allowRepeat = false)
		{
			//Criar a Tag ou utilizar a Tag passada
			$tag = !($tagName instanceof Tag) ? self::createTag($tagName, $properties, $content) : $tagName;

			//Definir localização
			$tag->setPlacement($placement);

			//Verificar se já existe o local de colocação
			if(!isset($this->_tags[$placement]))
				$this->_tags[$placement] = [];

			//Se essa tag não já foi criada ou for forçada a inserção repetida, inserir e retornar o conteúdo da Tag
			if(!in_array($tag, $this->_tags[$placement]) || $allowRepeat === true)
			{
				//Incluir Tag e retornar seu conteúdo
				$this->_tags[$placement][] = $tag;
				return $tag;
			}

			return $tag;
		}

		/**
		* Resgata as Tags de um determinado local
		*
		* @param string $placement Local onde será colocado
		* @param bool $returnAsArray Se TRUE retorna as Tags em um Array
		*
		* @return mixed
		*/
		public function getTags ($placement, $returnAsArray = false)
		{
			//Verificar se esse local foi definido
			if(!isset($this->_tags[$placement]))
				return false;

			//Se foi solicitado um array, retorná-lo
			if($returnAsArray === true)
				return $this->_tags[$placement];

			//Retornar todos as Tags
			$content = array_map(function($tag){ return $tag->mount(); }, $this->_tags[$placement]);

			return implode(PHP_EOL, $content);
		}

		private function _addingTagParse ($tagName, $place, $pathProperty, $path, $publicPath = true, $options = array(), $content = null)
		{
			//if($publicPath === true && !file_exists($this->getPath('public/'.$path)))
			//	return $this->_fault('addTagFile', $tagName);

			//Caso publicPath seja true, incluir o caminho do site na URL
			$path = ($publicPath === true) ? $this->getPublicURL($path) : $path;

			if(is_null($options))
				$options = array();

			//Unir as opções enviadas
			if(!is_null($path))
				$options = array_merge($options, array($pathProperty => $path));

			//Incluir a tag
			return $this->addTag($tagName, $place, is_null($content) ? '' : $content, $options);
		}

		public function addTagJS ($path, $publicPath = true, $options = array(), $content = null)
		{
			return $this->_addingTagParse('script', 'Footer', 'src', $path, $publicPath, $options, $content);
		}

		public function addTagHeadJS ($path, $publicPath = true, $options = array(), $content = null)
		{
			return $this->_addingTagParse('script', 'Header', 'src', $path, $publicPath, $options, $content);
		}

		public function addTagCSS ($path, $publicPath = true, $options = array(), $content = null)
		{
			//Unir as opções enviadas
			$options = array_merge($options, array('type' => 'text/css', 'rel' => 'stylesheet'));

			return $this->_addingTagParse('link', 'Footer', 'href', $path, $publicPath, $options, $content);
		}

		public function addTagHeadCSS ($path, $publicPath = true, $options = array(), $content = null)
		{
			//Unir as opções enviadas
			$options = array_merge($options, array('type' => 'text/css', 'rel' => 'stylesheet'));

			return $this->_addingTagParse('link', 'Header', 'href', $path, $publicPath, $options, $content);
		}

		/**
		 * Adicionar um asset completo
		 * 
		 * @param string $asset Nome do asset
		 * @param bool $head Deve incluir no Head? (se false será incluído no Footer)
		 * @param bool $nocache Se true (ou ignorado), irá criar um cache próprio baseado no conteúdo do arquivo
		 * 
		 * @return null
		 */
		public function addAsset ($asset, $head = true, $nocache = true)
		{
			$folder = $this->getPath("public/assets/$asset");

			if(!file_exists($folder))
			{
				trace("Asset doesn't exist", "Navigation\\Tags", $asset);
				return false;
			}

			trace("Including asset", "Navigation\\Tags", $asset);


			$assetFiles = scandir($folder);

			foreach ($assetFiles as $file)
			{
				$ext = substr($file, -3);
				
				if($ext == ".js")
				{
					//Se houver um 'bundle' dentro, e não for esse, ignorar
					if(in_array("bundle.min.js", $assetFiles) && $file != "bundle.min.js")
						continue;

					//Ignorar esse arquivo se houver uma versão minificada
					if(in_array(substr($file, 0, strlen($file)-3) . ".min.js", $assetFiles))
						continue;

					$prefix = $nocache === true ? '?ver=' . md5_file($this->getPath("public/assets/$asset/" . $file)) : '';
					$path = "assets/$asset/" . $file . $prefix;

					if($head)
						$this->addTagHeadJS($path);
					else
						$this->addTagJS($path);
				}
				else if($ext == "css")
				{
					//Se houver um 'bundle' dentro, e não for esse, ignorar
					if(in_array("bundle.min.css", $assetFiles) && $file != "bundle.min.css")
						continue;

					//Ignorar esse arquivo se houver uma versão minificada
					if(in_array(substr($file, 0, strlen($file)-3) . "min.css", $assetFiles))
						continue;

					$prefix = $nocache === true ? '?ver=' . md5_file($this->getPath("public/assets/$asset/" . $file)) : '';
					$path = "assets/$asset/" . $file . $prefix;
					
					if($head)
						$this->addTagHeadCSS($path);
					else
						$this->addTagCSS($path);
				}

			}

		}
	}

?>