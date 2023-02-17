<?php
/**
* Objecto de funções join
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Join
	{
		private $_join = array();

		/**
		* Monta a cláusula de join
		*
		* @return string
		*/
		protected function _assembleJoin ()
		{
			return implode(" ", $this->_join);
		}

		/**
		* Cria a query de join
		*
		* @param string $type Tipo de join
		* @param string $table Nome da tabela
		* @param string $field Campo de comparação
		* @param string $operador Operador de comparação
		* @param string $field2 Segundo campo de comparação
		*
		* @return string
		*/
		private function _createJoin ($type, $table, $field, $operator, $field2 = false)
		{
			//Resgatar alias da tabela a ser unida
			$tableAlias = $table;

			//Verificar se a tabela já possui um alias
			if(is_string($table) && preg_match("/AS ([A-z_0-9]+)/", $table, $match))
				$tableAlias = $match[1];

			//Caso a tabela seja uma instância de banco de dados
			if($table instanceof Icecream)
			{	
				//Resgatar parâmetros
				$this->_mergeParam($table->getParam());

				//Resgatar alias da tabela
				$tableAlias = $table->getAlias();

				//Criar tabela de JOIN
				$table = "(" . $table->selectQueryString() . ") AS " . $table->getAlias();
			}

			$field = $this->_parseCol($field); 
			
			//Verificar se o campo passado ainda não possui um alias
			if(!strpos($field, "."))
			{
				//Incluir alias
				$field = $tableAlias . "." . $field;
			}

			//Caso não haja o segundo campo, significa que foi passado na variável $operator
			if(!$field2)
			{
				$field2 = $operator;
				$operator = "=";
			}
			
			$field2 = $this->_parseCol($field2);			

			//Incluir alias no segundo campo, caso não haja
			if(!strpos($field2, "."))
				$field2 = $this->getAlias() . "." . $field2;

			//Criar query
			$query = "$type $table ON $field $operator $field2";

			//Apagar resultados já existentes
			$this->eraseResults();

			return $query;
		}

		/**
		* Cria um join comum
		*
		* @param string $table Nome da tabela
		* @param string $field Campo de comparação
		* @param string $operador Operador de comparação
		* @param string $field2 Segundo campo de comparação
		*
		* @return object
		*/
		public function join ($table, $field, $operator, $field2 = false)
		{
			$this->_join[] = $this->_createJoin("JOIN", $table, $field, $operator, $field2);
			return $this;
		}

		/**
		* Cria um left join
		*
		* @param string $table Nome da tabela
		* @param string $field Campo de comparação
		* @param string $operador Operador de comparação
		* @param string $field2 Segundo campo de comparação
		*
		* @return object
		*/
		public function leftJoin ($table, $field, $operator, $field2 = false)
		{
			$this->_join[] = $this->_createJoin("LEFT JOIN", $table, $field, $operator, $field2);
			return $this;
		}

		/**
		* Cria um right join 
		*
		* @param string $table Nome da tabela
		* @param string $field Campo de comparação
		* @param string $operador Operador de comparação
		* @param string $field2 Segundo campo de comparação
		*
		* @return object
		*/
		public function rightJoin ($table, $field, $operator, $field2 = false)
		{
			$this->_join[] = $this->_createJoin("RIGHT JOIN", $table, $field, $operator, $field2);
			return $this;
		}

		/**
		* Cria um inner join
		*
		* @param string $table Nome da tabela
		* @param string $field Campo de comparação
		* @param string $operador Operador de comparação
		* @param string $field2 Segundo campo de comparação
		*
		* @return object
		*/
		public function innerJoin ($table, $field, $operator, $field2 = false)
		{
			$this->_join[] = $this->_createJoin("INNER JOIN", $table, $field, $operator, $field2);
			return $this;
		}

		/**
		* Cria um outer join
		*
		* @param string $table Nome da tabela
		* @param string $field Campo de comparação
		* @param string $operador Operador de comparação
		* @param string $field2 Segundo campo de comparação
		*
		* @return object
		*/
		public function outerJoin ($table, $field, $operator, $field2 = false)
		{
			$this->_join[] = $this->_createJoin("OUTER JOIN", $table, $field, $operator, $field2);
			return $this;
		}
	}