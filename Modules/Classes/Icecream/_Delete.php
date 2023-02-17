<?php
/**
* Funções de exclusão
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;
	
	use \Fault;

	trait _Delete
	{
		/**
		* Realiza a exclusão de dados
		*
		* @return object
		*/
		public function delete ()
		{
			//Se não houver condições, interromper
			if(empty($this->_assembleWhere()))
				return new Fault($this->write('For security reasons you need to declare conditions with \'where\' clause for this operation', 'icecream'), 'icecream_security_where');

			//Criar query
			$query = [
				"DELETE FROM " . $this->getTable(),
				$this->_assembleWhere(),
				$this->_assembleLimit()
			];

			//Criar string de query
			$queryString = implode(" ", $query);

			//Executar inserção
			return $this->_exec($queryString);
		}

		/**
		* Zera a tabela
		*
		* @return object
		*/
		public function truncate ()
		{
			$queryString = "TRUNCATE TABLE " . $this->getTable();

			//Executar a exclusão
			return $this->_exec($queryString);
		}
	}