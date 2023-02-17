<?php
/**
* Objecto de funções join
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Limit
	{
		private $_limit;
		private $_offset;

		/**
		* Monta a cláusula de limite e offset (se houver)
		*
		* @return string
		*/
		protected function _assembleLimit ()
		{
			$limit = !is_null($this->_limit) ? "LIMIT " . $this->_limit : '';
			$limit .= !is_null($this->_offset) ? " OFFSET " . $this->_offset : '';
			return $limit;
		}

		/**
		* Determina um limite de valores a retornar
		*
		* @param integer $limit Valor do limite
		*
		* @return object
		*/
		public function limit ($limit)
		{
			//Apagar resultados já existentes
			$this->eraseResults();

			$this->_limit = $limit;
			return $this;
		}

		/**
		* Determina um deslocamento de dados
		*
		* @param integer $offset Posição inicial para deslocamento
		*
		* @return object
		*/
		public function offset ($offset)
		{
			//Apagar resultados já existentes
			$this->eraseResults();
			
			$this->_offset = $offset;
			return $this;
		}
	}