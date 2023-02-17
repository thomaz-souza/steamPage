<?php
/**
* Objecto de funções de tabela
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \Fault;
	
	trait _Table
	{
		protected $_table;
		protected $_alias;

		/**
		* Define a tabela a ser consultada
		*
		* @param string $table Nome da tabela
		*
		* @return object
		*/
		public function table ($table)
		{
			//Apagar resultados já existentes
			$this->eraseResults();

			//Caso a tabela seja uma instância de banco de dados
			if($table instanceof Icecream)
			{	
				//Incluir parâmetros da instância passada e incluir a string da query
				$this->_mergeParam($table->getParam());
				$this->alias($table->getAlias());
				$table = "(" . $table->_selectQueryString() . ")";

			}

			$this->_table = $table;
			return $this;
		}

		/**
		* Resgata nome da tabela
		*
		* @return object
		*/
		public function getTable ()
		{
			return $this->_table;
		}

		/**
		* Define o alias da tabela
		*
		* @param string $alias Alias da coluna
		*
		* @return object
		*/
		public function alias ($alias)
		{
			//Apagar resultados já existentes
			$this->eraseResults();
			
			$this->_alias = $alias;
			return $this;
		}

		/**
		* Resgata o alias da tabela
		*
		* @return string
		*/
		public function getAlias ()
		{
			return is_null($this->_alias) ? $this->_table : $this->_alias;
		}

		/**
		* Seleciona o nome da tabela juntamente com seu alias
		*
		* @return string
		*/
		public function getTableAlias ()
		{
			return is_null($this->_alias) ? $this->_table : $this->_table . " AS " . $this->_alias;
		}

		/**
		* Criar tabela
		*
		* @param $columns array Array de colunas
		*
		* @return string
		*/
		public function create ($columns)
		{
			$query = "CREATE TABLE " . $this->getTable() . " (";

			$columnsList = [];

			$keyList = [];

			foreach ($columns as $columnName => $column)
			{
				$columnSet = $columnName;

				$columnSet .= " " . strtoupper($column['type']);

				if(isset($column['length']))
					$columnSet .= "(" . $column['length'] . ")";

				if(isset($column['default']))
					$columnSet .= " " . $column['default'];

				$columnsList[] = $columnSet;

				if(isset($column['key']))

					if($column['key'] == "primary")
						$keyList[] = "PRIMARY KEY ($columnName)";

					else if($column['key'] == "unique")
						$keyList[] = "UNIQUE INDEX $columnName ($columnName)";

					else if($column['key'] == "index")
						$keyList[] = "INDEX $columnName ($columnName) USING BTREE";
				
			}

			$columnsList = array_merge($columnsList, $keyList);

			$query .= implode(",", $columnsList) . ")";

			return $this->_exec($query);
		}

		/**
		* Excluir tabela
		*
		* @return mixed
		*/
		public function drop ()
		{
			$query = "DROP TABLE " . $this->getTable();
			return $this->_exec($query);
		}

		/**
		* Verifica se tabela existe
		*
		* @return bool
		*/
		public function existTable ()
		{
			$query = "SHOW TABLES LIKE " . $this->_setParam('table', $this->getTable());
			$result = $this->_exec($query);

			//Retornar erro se houver
			if($result instanceof Fault)
				return $result;

			return $result->fetch(\PDO::FETCH_ASSOC) === false ? false : true;
		}

		/**
		* Resgata as colunas da tabela
		*
		* @return mixed
		*/
		public function getTableColumns ($force = false)
		{
			if($force || !isset($GLOBALS['ICECREAM_TABLE']) || !isset($GLOBALS['ICECREAM_TABLE'][$this->getTable()]))
			{
				$query = "SHOW COLUMNS FROM " . $this->getTable();
				$result = $this->_exec($query);

				//Retornar erro se houver
				if($result instanceof Fault)
					return $result;

				$GLOBALS['ICECREAM_TABLE'][$this->getTable()] = $result->fetchAll(\PDO::FETCH_ASSOC);
			}
			
			return $GLOBALS['ICECREAM_TABLE'][$this->getTable()];			
		}
	}