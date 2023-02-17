<?php
/**
* Objecto de guardar parâmetros
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Params
	{
		private $_params = array();		

		/**
		* Define um parâmetro e retorna o id único criado para a coluna
		*
		* @param mixed $column Nome da coluna
		* @param mixed $column Nome da valor
		*
		* @return string
		*/
		private function _setParam ($column, $value)
		{
			if($value instanceof Raw)
				return $value->getValue();

			//preg_replace("/[^a-z0-9_]+/", "", $column) . 
			$id = ":" . uniqid();
			$this->_params[$id] = $value;
			return $id;
		}

		/**
		* Resgata os parâmetros
		*
		* @return string
		*/
		private function _getParam ()
		{
			return $this->_params;
		}

		/**
		* Alias para resgatar os parâmetros
		*
		* @return string
		*/
		public function getParam ()
		{
			return $this->_getParam();
		}

		/**
		* Remove um parâmetro
		*
		* @param string $id ID do parâmetro
		*
		* @return string
		*/
		public function removeParam ($id)
		{
			if(!isset($this->_params[$id]))
				return false;
			unset($this->_params[$id]);
		}

		/**
		* Remove todos os parâmetros
		*
		* @return null
		*/
		private function _eraseParam ()
		{
			$this->_params = [];
		}

		/**
		* Realiza a vínculação dos parâmetros
		*
		* @param object $query Objeto da query para vínculação dos parâmetros
		*
		* @return string
		*/
		private function _bindParam (&$query)
		{
			foreach ($this->_getParam() as $column => $value)
			{
				if($value === null)
					$query->bindValue($column, null, \PDO::PARAM_INT);
				else
					$query->bindValue($column, $value);
			}
			
			if($this->_allowEraseParams === true)
				$this->_eraseParam();

			$this->_allowEraseParams = true;
			
			return $query;
		}

		/**
		* Realiza a vínculação dos parâmetros
		*
		* @param object $query Objeto da query para vínculação dos parâmetros
		*
		* @return string
		*/
		private function _bindRawParam (&$query)
		{
			foreach ($this->_getParam() as $column => $value)
			{
				$query = str_replace($column, "'".addslashes($value)."'", $query);
			}
			
			if($this->_allowEraseParams === true)
				$this->_eraseParam();

			$this->_allowEraseParams = true;
			
			return $query;
		}

		/**
		* Insere um parâmetro ou mais dentro da lista
		*
		* @param array $values Valores
		*
		* @return string
		*/
		private function _mergeParam ($values)
		{
			$this->_params = array_merge($this->_params, $values);
		}

		private $_allowEraseParams = true;

		private function _allowEraseParams ($type)
		{
			$this->_allowEraseParams = $type;
		}
	}