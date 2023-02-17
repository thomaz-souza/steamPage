<?php
/**
* Objecto de funções where
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Where
	{
		private $_where = array();

		/**
		* Monta a cláusula de condição (where)
		*
		* @param boolean $includeWhere Se false, não inclui a clausula WHERE
		* @param boolean $parseCol Se false, não registra nome da tabela 
		*
		* @return string
		*/
		protected function _assembleWhere ($includeWhere = true, $parseCol = true)
		{
			$query = [];

			//Iterar as condições
			foreach ($this->_where as $condition)
			{
				if($parseCol)
					$condition['column'] = $this->_parseCol($condition['column']);

				//Condição temporária
				$q = "";

				//Se não for a primeira operação, incluir união (OR, AND)
				if(count($query) > 0)
					$q .= $condition['union'] . " ";

				//Se não houver operador, incluir apenas a coluna
				if(is_null($condition['operator']))
					$q .= $condition['column'];

				else
					$q .= $condition['column'] . " " . $condition['operator'] . " " . $condition['value'];
	
				//Incluir na lista de condições				
				$query[] = $q;
			}

			//Incluir WHERE caso solicitado
			if($includeWhere === true && count($query) > 0)
				$query = "WHERE " . implode(" ", $query);

			else
				$query = implode(" ", $query);

			return $query;
		}

		/**
		* Criar operação de condição
		*
		* @param string $union Tipo de união (OR, AND)
		* @param string $column Nome da coluna
		* @param string $operator Operador de comparação
		* @param string $value Valor a ser comparado
		* @param string $raw Permite que o valor seja inserido direto, sem ser vinculado posteriormente
		*
		* @return string
		*/
		private function _setWhere ($union, $column, $operator = false, $value = null, $raw = false)
		{
			if($raw === false && ($operator) !== false)
				$value = $this->_setParam($column, $value);

			$this->_where[] = [
				'union' => $union,
				'operator' => $operator,
				'column' => $column,
				'value' => $value
			];

			//Apagar resultados já existentes
			$this->eraseResults();
		}

		/**
		* Zerar todas as condições
		*
		* @return null
		*/
		private function _eraseConditions ()
		{
			$this->_where = [];
		}

		/**
		* Incluir condição como AND criada pelo objeto Condition
		*
		* @param object $condition Objeto condition ou função
		*
		* @return object
		*/
		public function condition ($condition = null)
		{
			if(!$condition instanceof Condition)
			{
				$subCondition = new Condition();
				$condition($subCondition);
				$condition = $subCondition;
			}

			$assembled = $condition->assembleWhere();

			if(!empty($assembled))
			{
				$this->_setWhere('AND', "(" . $condition->assembleWhere() . ")");
				$this->_mergeParam($condition->getParam());
			}
			return $this;
		}

		/**
		* Incluir condição como OU criada pelo objeto Condition
		*
		* @param object $condition Objeto condition
		*
		* @return object
		*/
		public function orCondition ($condition = null)
		{
			if(!$condition instanceof Condition)
			{
				$subCondition = new Condition();
				$condition($subCondition);
				$condition = $subCondition;
			}

			$assembled = $condition->assembleWhere();

			if(!empty($assembled))
			{
				$this->_setWhere('OR', "(" . $condition->assembleWhere() . ")");
				$this->_mergeParam($condition->getParam());
			}
			return $this;
		}

		/**
		* Cria uma condição comum AND
		*
		* @param string $column Nome da Coluna
		* @param string $operator Operador ou valor (criando uma operação =)
		* @param string $value Valor (caso definido um operador anteriormente)
		*
		* @return object
		*/
		public function where ($column, $operator = false, $value = false)
		{
			//Se houver apenas um valor em "$column" interpretá-lo como uma declaração pura
			if($operator === false)
			{
				$this->_setWhere('AND', $column, null, null, true);
				return $this;
			}

			if($value === false)
			{
				$value = $operator;
				$operator = "=";
			}
			$this->_setWhere('AND', $column, $operator, $value);
			return $this;
		}

		/**
		* Cria uma condição comum OR
		*
		* @param string $column Nome da Coluna
		* @param string $operator Operador ou valor (criando uma operação =)
		* @param string $value Valor (caso definido um operador anteriormente)
		*
		* @return object
		*/
		public function orWhere ($column, $operator = false, $value = false)
		{	
			//Se houver apenas um valor em "$column" interpretá-lo como uma declaração pura
			if($operator === false)
			{
				$this->_setWhere('OR', $column, null, null, true);
				return $this;
			}

			if($value === false)
			{
				$value = $operator;
				$operator = "=";
			}
			$this->_setWhere('OR', $column, $operator, $value);
			return $this;
		}

	

		/**
		* Cria uma condição AND onde a coluna possui um conteúdo diferente do valor
		*
		* @param string $column Nome da Coluna
		* @param string $value Valor a ser comparado
		*
		* @return object
		*/
		public function whereNot ($column, $value = false)
		{
			$this->_setWhere('AND', $column, "!=", $value);
			return $this;
		}

		/**
		* Cria uma condição OR onde a coluna possui um conteúdo diferente do valor
		*
		* @param string $column Nome da Coluna
		* @param string $value Valor a ser comparado
		*
		* @return object
		*/
		public function orWhereNot ($column, $value = false)
		{
			$this->_setWhere('OR', $column, "!=", $value);
			return $this;
		}

		/**
		* Cria uma condição AND de intervalo
		*
		* @param string $column Nome da Coluna
		* @param string $valueFrom Deste valor
		* @param string $valueTo A este valor
		*
		* @return object
		*/
		public function whereBetween ($column, $valueFrom, $valueTo)
		{
			$this->_setWhere('AND', $column, "BETWEEN", "'$valueFrom' AND '$valueTo'", true);
			return $this;
		}

		/**
		* Cria uma condição OR de intervalo
		*
		* @param string $column Nome da Coluna
		* @param string $valueFrom Deste valor
		* @param string $valueTo A este valor
		*
		* @return object
		*/
		public function orWhereBetween ($column, $valueFrom, $valueTo)
		{
			$this->_setWhere('OR', $column, "BETWEEN", "'$valueFrom' AND '$valueTo'", true);
			return $this;
		}

		/**
		* Cria uma condição AND onde uma coluna tem valor nulo ou vazio ""
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function whereEmpty ($column)
		{
			$this->condition(function($query) use($column){

				$query->whereNull($column);
				$query->orWhere($column, "");

			});
			return $this;
		}

		/**
		* Cria uma condição OR onde uma coluna tem valor nulo ou vazio ""
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function orWhereEmpty ($column)
		{
			$this->orCondition(function($query) use($column){

				$query->whereNull($column);
				$query->orWhere($column, "");

			});
			return $this;
		}

		/**
		* Cria uma condição AND onde uma coluna NÃO valor nulo NEM vazio ""
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function whereNotEmpty ($column)
		{
			$this->condition(function($query) use($column){

				$query->whereNotNull($column);
				$query->whereNot($column, "");

			});
			return $this;
		}

		/**
		* Cria uma condição AND onde uma coluna NÃO valor nulo NEM vazio ""
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function orWhereNotEmpty ($column)
		{
			$this->orCondition(function($query) use($column){

				$query->whereNotNull($column);
				$query->whereNot($column, "");

			});
			return $this;
		}

		/**
		* Cria uma condição AND onde uma coluna tem valor nulo
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function whereNull ($column)
		{
			$this->_setWhere('AND', $column, "IS", "NULL", true);
			return $this;
		}

		/**
		* Cria uma condição OR onde uma coluna tem valor nulo
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function orWhereNull ($column)
		{
			$this->_setWhere('OR', $column, "IS", "NULL", true);
			return $this;
		}

		/**
		* Cria uma condição AND onde uma coluna NÃO tem valor nulo
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function whereNotNull ($column)
		{
			$this->_setWhere('AND', $column, "IS NOT", "NULL", true);
			return $this;
		}

		/**
		* Cria uma condição OR onde uma coluna NÃO tem valor nulo
		*
		* @param string $column Nome da Coluna
		*
		* @return object
		*/
		public function orWhereNotNull ($column)
		{
			$this->_setWhere('OR', $column, "IS NOT", "NULL", true);
			return $this;
		}

		/**
		* Cria uma condição AND onde a coluna tem um valor parecido/próximo
		*
		* @param string $column Nome da Coluna
		* @param string $statement Declaração de comparação
		*
		* @return object
		*/
		public function whereLike ($column, $statement)
		{
			$this->_setWhere('AND', $column, "LIKE", $statement);
			return $this;
		}

		/**
		* Cria uma condição OR onde a coluna tem um valor parecido/próximo
		*
		* @param string $column Nome da Coluna
		* @param string $statement Declaração de comparação
		*
		* @return object
		*/
		public function orWhereLike ($column, $statement)
		{
			$this->_setWhere('OR', $column, "LIKE", $statement);
			return $this;
		}

		/**
		* Cria uma condição AND onde a coluna NÃO tem um valor parecido/próximo
		*
		* @param string $column Nome da Coluna
		* @param string $statement Declaração de comparação
		*
		* @return object
		*/
		public function whereNotLike ($column, $statement)
		{
			$this->_setWhere('AND', $column, "NOT LIKE", $statement);
			return $this;
		}

		/**
		* Cria uma condição OR onde a coluna NÃO tem um valor parecido/próximo
		*
		* @param string $column Nome da Coluna
		* @param string $statement Declaração de comparação
		*
		* @return object
		*/
		public function orWhereNotLike ($column, $statement)
		{
			$this->_setWhere('OR', $column, "NOT LIKE", $statement);
			return $this;
		}

		/**
		* Gera uma condição de valores dentro de uma lista
		*
		* @param string $column Nome da Coluna
		* @param string $statement Declaração de comparação
		*
		* @return null
		*/
		private function _createWhereIn ($union, $column, $operator, $list)
		{
			//Caso a lista seja uma instância de banco de dados
			if($list instanceof Icecream)
			{	
				//Se houver resultados nessa instância
				if($list->hasResults())
				{	
					//Resgatar resultados e verificar se são válidos
					$results = $list->resultList();
					if(count($results) < 1)
						return;

					//Resgatar nome da primeira chave
					$keyName = array_keys($results[0])[0];

					//Montar listagem de dados apenas com os resultados em questão
					$list = $list->resultList(null, $keyName);
				}
				else
				{
					//Se não houver resultados, incluir parâmetros da instância passada e incluir a string da query
					$this->_mergeParam($list->getParam());
					$list = "(" . $list->selectQueryString() . ")";
				}
			}

			//Caso seja um scalar
			else if(is_scalar($list))
			{
				$list = !empty($list)
					? $this->_pdoConnection()->quote($list, is_string($list) ? \PDO::PARAM_STR : \PDO::PARAM_INT)
					: "''";

				$list = "(" . $list . ")";
			}

			//Serializar array, se for array
			if(is_array($list))
			{
				//Se for vazio
				if(empty($list))
				{
					//Se for 'não está na lista', ignorar, porque é vazio
					if($operator == 'NOT IN')
						return;

					//Senão, incluir uma string vazia
					$list = [''];
				}				

				//Sanitizar
				foreach($list as &$item)
					$item = $this->_pdoConnection()->quote($item, is_string($item) ? \PDO::PARAM_STR : \PDO::PARAM_INT);

				//Serializar array
				$list = "(" . implode(",", $list) . ")";
			}

			//Incluir na lista de condições
			$this->_setWhere($union, $column, $operator, $list, true);
		}

		/**
		* Determina condição AND onde valores estiverem dentro de uma lista
		*
		* @param string $column Nome da Coluna
		* @param mixed $list Lista em array ou uma instância de Icecream
		*
		* @return object
		*/
		public function whereIn ($column, $list)
		{
			$this->_createWhereIn("AND", $column, "IN", $list);
			return $this;
		}

		/**
		* Determina condição OR onde valores estiverem dentro de uma lista
		*
		* @param string $column Nome da Coluna
		* @param mixed $list Lista em array ou uma instância de Icecream
		*
		* @return object
		*/
		public function orWhereIn ($column, $list)
		{
			$this->_createWhereIn("OR", $column, "IN", $list);
			return $this;
		}

		/**
		* Determina condição AND onde valores NÃO estiverem dentro de uma lista
		*
		* @param string $column Nome da Coluna
		* @param mixed $list Lista em array ou uma instância de Icecream
		*
		* @return object
		*/
		public function whereNotIn ($column, $list)
		{
			$this->_createWhereIn("AND", $column, "NOT IN", $list);
			return $this;
		}

		/**
		* Determina condição OR onde valores NÃO estiverem dentro de uma lista
		*
		* @param string $column Nome da Coluna
		* @param mixed $list Lista em array ou uma instância de Icecream
		*
		* @return object
		*/
		public function orWhereNotIn ($column, $list)
		{
			$this->_createWhereIn("OR", $column, "NOT IN", $list);
			return $this;
		}

	}