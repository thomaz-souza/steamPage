<?php
/**
* Função responsável por gerenciar formulários automaticamente
*
* @package	Storm
* @author 	Lucas/Postali
*/
	namespace Storm;

	use \_Core;
	use \Icecream\Icecream;

	class Storm extends _Core
	{
		function __construct ($params = null)
		{
			parent::__construct();
			if($params)
				$this->setParams($params);
		}

		/**
		* Insere na página
		*
		* @return null
		*/
		static public function addTag (\Navigation\Page $page)
		{
			$page->addTagHeadJS('assets/storm/storm.min.js');
			$page->addTagHeadJS('assets/picupload/picupload.min.js');
			$page->addTagCSS('assets/picupload/picupload.min.css');
		}

		private $_values;
		private $_params;

		/**
		* Insere os parâmetros enviados pelo Dynamic
		*
		* @param array $params Recebe resultados a serem reescritos
		*
		* @return array
		*/
		public function setParams ($params)
		{
			$paramTypes = ['_key', '_action', '_filters'];

			foreach ($params as $key => $value)
				if(in_array($key, $paramTypes))
					$this->_params[$key] = $value;
				else
					$this->_values[$key] = $value;
		}

		/**
		* Resgatar valores
		*
		* @param string $values (Opcional) Chave a ser recuperada
		*
		* @return mixed
		*/
		public function getValues ($value = null)
		{
			if($value)
				if(isset($this->_values[$value]))
					return $this->_values[$value];
				else
					return false;
			
			return $this->_values;
		}

		/**
		* Seta valor
		*
		* @param string $key Chave do valor
		* @param string $value Valor
		*
		* @return null
		*/
		public function setValue ($key, $value)
		{
			$this->_values[$key] = $value;
		}

		/**
		* Resgatar parâmetros
		*
		* @param string $param (Opcional) Chave a ser recuperada
		*
		* @return mixed
		*/
		public function getParams ($param = null)
		{
			if($param)
				if(isset($this->_params[$param]))
					return $this->_params[$param];
				else
					return false;
			
			return $this->_params;
		}

		/**
		* Resgatar a ação a ser realizada
		*
		* @return string
		*/
		public function getAction ()
		{
			return $this->getParams('_action');
		}

		/**
		* Resgatar chave ID
		*
		* @return string
		*/
		public function getKey ()
		{
			return $this->getParams('_key');
		}

		/**
		* Resgatar o conteúdo da chave ID
		*
		* @return string
		*/
		public function getKeyValue ()
		{
			$key = $this->getParams('_key');
			return isset($this->getValues()[$key]) ? $this->getValues()[$key] : false;
		}

		/**
		* Resgatar filtros
		*
		* @param string $values (Opcional) Chave a ser recuperada
		*
		* @return mixed
		*/
		public function getFilters ($value = null)
		{	
			if($value)
				if(isset($this->_params['_filters'][$value]))
					return $this->_params['_filters'][$value];
				else
					return false;
			
			return $this->_params['_filters'];
		}

		protected $_source;

		/**
		* Insere uma fonte de dados
		*
		* @return null
		*/
		public function setSource (Icecream $source)
		{
			$this->_source = $source;
		}

		/**
		* Resgata a fonte de dados
		*
		* @return null
		*/
		public function getSource ()
		{
			return $this->_source;
		}

		/**
		* Resgata o resultado do processamento
		*
		* @return null
		*/
		public function getResult ()
		{
			return $this->_result;
		}

		/**
		* Insere um resultado
		*
		* @param array $result Resultados a serem inseridos
		*
		* @return null
		*/
		public function setResults ($result)
		{
			$this->_result = $result;
			return $this;
		}

		protected $_result = null;

		protected function _insert ($map = null)
		{
			$this->_result = $this->getSource()
				->insert($this->getValues(), $map);
		}

		protected function _update ($map = null)
		{
			$this->_result = $this->getSource()
				->where($this->getKey(), $this->getKeyValue())
				->update($this->getValues(), $map);			
		}

		protected function _select ()
		{
			$this->_result = $this->getSource()
				->where($this->getKey(), $this->getKeyValue())
				->selectFirst();
		}

		protected function _delete ()
		{
			$this->_result = $this->getSource()
				->where($this->getKey(), $this->getKeyValue())
				->delete();
		}

		public function process ($map = null)
		{
			switch ($this->getAction())
			{
				case 'insert':
					$this->_insert($map);
					$this->setValue($this->getKey(), $this->getResult());
				break;

				case 'update':
					$this->_update($map);
				break;
				
				case 'select':
					$this->_select();
				break;

				case 'delete':
					$this->_delete();
				break;
			}

			return $this->getSource();
		}

		public function getStorm ($map = null)
		{
			if($this->_result === null)
				$this->process($map);

			return $this->_result;
		}

	}