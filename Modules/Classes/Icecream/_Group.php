<?php
/**
* Objeto de funções de Agrupamento (Group BY)
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Group
	{
		private $_group = array();

		private $_having = null;

		/**
		* Monta a cláusula de grupo
		*
		* @return string
		*/
		protected function _assembleGroup ()
		{
			$group = array_map(function($col){ return $this->_parseCol($col); }, $this->_group); 

			$query = count($group) > 0 ? "GROUP BY " . implode(",", $group) : '';
			$query .= !empty($this->_having) ? " HAVING " . $this->_parseCol($this->_having) : '';

			return $query;
		}

		/**
		* Realiza o agrupamento de colunas selecionadas
		*
		* @param mixed $column Nome da coluna a ser agrupada (pode ser enviado uma array com as colunas)
		*
		* @return object
		*/
		public function groupBy ($column)
		{
			if(is_array($column))
				$this->_group = array_merge($this->_group, $column);
			else
				$this->_group[] = $column;

			//Apagar resultados já existentes
			$this->eraseResults();

			return $this;
		}

		/**
		* Cria uma condição HAVING
		*
		* @param string $column Nome da Coluna
		* @param string $operator Operador ou valor (criando uma operação =)
		* @param string $value Valor (caso definido um operador anteriormente)
		*
		* @return object
		*/
		public function having ($column, $operator = false, $value = false)
		{
			//Se houver apenas um valor em "$column" interpretá-lo como uma declaração pura
			if($operator === false && $value === false)
			{
				$this->_having = $column;
				return $this;				
			}

			if($value === false)
			{
				$value = $operator;
				$operator = "=";
			}

			$this->_having = $column . " " . $operator . " " . $this->_setParam('having', $value);
			return $this;
		}
	}