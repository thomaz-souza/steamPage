<?php
/**
* Objecto de funções Ordenação (Order By)
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Order
	{
		private $_order = array();

		/**
		* Monta a cláusula de ordem
		*
		* @return string
		*/
		protected function _assembleOrder ()
		{
			$order = array_map(function($col){ return $this->_parseCol($col); }, $this->_order); 

			return count($this->_order) > 0 ? "ORDER BY " . implode(",", $order) : '';
		}

		/**
		* Determina a ordenação dos resultados
		*
		* @param string $column Nome da coluna
		* @param string $sort Tipo de ordenação
		*
		* @return string
		*/
		public function orderBy ($column, $sort = null)
		{
			//Apagar resultados já existentes
			$this->eraseResults();
			
			$this->_order[] = $column . (!is_null($sort) ? ' ' . $sort : '');
			return $this;
		}
	}