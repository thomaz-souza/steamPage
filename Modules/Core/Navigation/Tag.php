<?php
/**
* Objeto de Tag
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;

	class Tag extends \Core
	{	
		/**
		 * Nome da Tag
		 * @var string
		 */
		private $_tagName = "";

		/**
		 * Resgata o nome da tag
		 * 
		 * @return string
		 */
		public function getTagName ()
		{
			return $this->_tagName;
		}

		/**
		 * Local de inserção da tag
		 * @var string
		 */
		private $_placement = "";

		/**
		 * Resgata o local de inserção da tag
		 * 
		 * @return string
		 */
		public function getPlacement ()
		{
			return $this->_placement;
		}

		/**
		 * Define o local de inserção da tag
		 * 
		 * @return Tag
		 */
		public function setPlacement ($placement)
		{
			if(is_string($placement))
				$this->_placement = $placement;

			return $this; 
		}

		/**
		 * Propriedades da TAG
		 * @var array
		 */
		private $_properties = [];

		/**
		 * Resgata propriedades
		 * 
		 * @param  string $key (Opcional) Se passado, retorna o conteúdo dessa propriedade
		 * @return array|string
		 */
		public function getProperty ($key = null)
		{
			if($key !== null)
				return !isset($this->_properties[$key]) ? null : $this->_properties[$key];

			return $this->_properties;
		}

		/**
		 * Define o valor de uma propriedade
		 * 
		 * @param string|array $name Nome da propriedade ou array com várias propriedades
		 * @param string $value (Opcional) valor da propriedade
		 * @return Tag
		 */
		public function setProperty ($name, $value = true)
		{
			if(is_array($name))
			{
				foreach ($name as $property => $value)
					$this->setProperty($name, $value);
			}
			else if(is_string($name)) {
				$this->_properties[mb_strtolower($name, 'UTF-8')] = $value;
			}
			

			return $this;
		}

		/**
		 * Remova uma proriedade
		 * 
		 * @param  string $name Nome da propriedade a ser removida
		 * @return Tag
		 */
		public function removeProperty ($name)
		{
			if(isset($this->_properties[$name]))
				unset($this->_properties[$name]);

			return $this;
		}

		/**
		 * Conteúdo do bloco
		 * @var string
		 */
		private $_content = "";

		/**
		 * Resgata o conteúdo a ser inserido
		 * 
		 * @return string
		 */
		public function getContent ()
		{
			return $this->_content;
		}

		/**
		 * Define o conteúdo a ser inserido
		 * 
		 * @return Tag
		 */
		public function setContent ($content)
		{
			if(is_string($content))
				$this->_content = $content;

			return $this;
		}


		function __construct ($tagName, $properties = [], $content = null)
		{
			$this->_tagName = $tagName;

			if(is_string($properties))
				$this->_content = $properties;

			else if(is_array($properties))
				$this->_properties = $properties;

			if(!empty($content) && is_string($content))
				$this->_content = $content;
		}

		/**
		 * Retorna se é uma TAG de auto fechamento
		 * 
		 * @return bool
		 */
		public function selfClosing ()
		{
			return false;
		}

		/**
		 * Monta a TAG
		 * 
		 * @return string
		 */
		public function mount ()
		{
			//Gerar tag
			$tag = '<' . $this->getTagName();

			//Incluir propriedades
			foreach ($this->getProperty() as $property => $value)
				$tag .= " $property" . ($value !== true ?  "=\"$value\"" : '');

			//Se for uma tag única, retorná-la
			if($this->selfClosing())
				return $tag . '/>';

			//Incluir conteúdo e fechar a tag
			$tag .= '>';
			$tag .= $this->getContent();
			$tag .= '</' . $this->getTagName() . '>';

			return $tag;
		}


		public function __call ($tag, $arg)
		{
			if(empty($arg))
				return $this->getProperty($tag);

			$value = $arg[0];

			$tag = preg_replace_callback("/[A-Z]/", function($m){ return "-" . mb_strtolower($m[0]); }, $tag);

			if($value === false)
				return $this->removeProperty($tag);				

			else if(is_string($value) || $value === true)
				return $this->setProperty($tag, $value);
		}


	}