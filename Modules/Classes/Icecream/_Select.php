<?php
/**
* Objecto de funções select
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \PDO;
	use \Fault;
	
	trait _Select
	{
		private $_select = array();

		private $_distinct = false;

		/**
		 * Realiza o parsing de colunas com elemento &&
		 * 
		 * @param  string $col Nome da coluna
		 * 
		 * @return string
		 */
		protected function _parseCol ($col)
		{
			return preg_replace("/\&\&\./", $this->getAlias() . ".", $col);
		}

		/**
		* Monta a cláusula de select
		*
		* @return string
		*/
		protected function _assembleSelect ()
		{
			//Iniciar Select
			$select = "SELECT ";

			//Adicionar Distinct caso habilitado
			$select .= ($this->_distinct === true) ? "DISTINCT " : "";

			//Se não houver colunas selecionadas, retornar
			if(empty($this->_select))
				return $select . "* FROM";
		
			$columns = array_map(function($col){ return $this->_parseCol($col); }, $this->_select);

			//Incluir campos
			$select .= implode(",", $columns);
	
			return $select . " FROM";
		}

		/**
		* Manipula a instrução DISTINCT no início da consulta
		*
		* @param boolean $condition Se true, inclue, se false retira a instrução
		*
		* @return object
		*/
		public function distinct ($condition = true)
		{
			$this->_distinct = $condition;
			return $this;
		}

		/**
		* Determina as colunas a serem buscadas.
		*
		* @param mixed $values Colunas. Pode ser uma string ou array
		* @param bool $replace Se true, apaga as colunas anteriormente selecionadas
		*
		* @return object
		*/
		public function columns ($values, $replace = false)
		{
			//Apagar resultados já existentes
			$this->eraseResults();
			
			if(!is_array($values))
				$values = explode(",", $values);

			if($replace === true)
				$this->_select = [];

			foreach ($values as $k => $value)
				if(in_array($value, $this->_select))
					unset($values[$k]);
			
			$this->_select = array_merge($this->_select, $values);
			return $this;
		}

		/**
		 * Retorna o nome da coluna junto ao nome da tabela
		 * 
		 * @param  string $column Nome da coluna
		 * 
		 * @return string
		 */
		public function col ($column)
		{
			return $this->getAlias() . "." . $column;
		}

		/**
		* Monta a string de consulta
		*
		* @return object
		*/
		private function _selectQueryString ()
		{
			$query = [
				$this->_assembleSelect(),
				$this->getTableAlias(),
				$this->_assembleJoin(),
				$this->_assembleWhere(),
				$this->_assembleGroup(),
				$this->_assembleOrder(),
				$this->_assembleLimit(),
				$this->_assembleUnion(),
			];
			
			return implode(" ", $query);
		}

		/**
		* Alias para a string de consulta
		*
		* @return object
		*/
		public function selectQueryString ()
		{
			return $this->_selectQueryString();
		}

		/**
		* Prepara a query
		*
		* @return object
		*/
		private function _selectQuery ()
		{
			$queryString = $this->_selectQueryString();	
			$query = $this->_exec($queryString);
			
			if($query instanceof Fault)
				return $query;

			$this->_query = $query;
			return $this->_query;
		}

		/**
		* Alias para a preparação da query
		*
		* @return object
		*/
		public function selectQuery ()
		{
			return $this->_selectQuery();
		}

		/**
		* Resgata
		*
		* @param mixed $columns Colunas a serem selecionadas. Pode ser uma string ou array.
		*
		* @return array
		*/
		public function select ($columns = null)
		{
			//Se foram enviadas colunas para essa consulta, registrar
			if(!is_null($columns))
				$this->columns($columns);

			//Executar query e verificar se houve erros
			$query = $this->_selectQuery();
			if($query instanceof Fault)
				return $query;

			//Retornar todos os resultados
			$this->_result = $query->fetchAll(PDO::FETCH_ASSOC);
			$this->_result = $this->_append($this->_result);
			return $this->_result;
		}

		/**
		* Resgata apenas o primeiro resultado
		*
		* @param mixed $columns Colunas a serem selecionadas. Pode ser uma string ou array.
		*
		* @return array
		*/
		public function selectFirst ($columns = null)
		{
			//Se foram enviadas colunas para essa consulta, registrar
			if(!is_null($columns))
				$this->columns($columns);

			//Executar query e verificar se houve erros
			$query = $this->_selectQuery();
			if($query instanceof Fault)
				return $query;

			//Retornar todos os resultados
			$this->_result = $query->fetch(PDO::FETCH_ASSOC);
			$this->_result = $this->_append($this->_result);
			return $this->_result;
		}

		/**
		* Resgata apenas o último resultado
		*
		* @param mixed $columns Colunas a serem selecionadas. Pode ser uma string ou array.
		*
		* @return array
		*/
		public function selectLast ($columns = null)
		{
			//Se foram enviadas colunas para essa consulta, registrar
			if(!is_null($columns))
				$this->columns($columns);

			//Executar query e verificar se houve erros
			$query = $this->_selectQuery();
			if($query instanceof Fault)
				return $query;

			//Retornar os últimos resultados
			$this->_result = $query->fetchAll(PDO::FETCH_ASSOC);
			$this->_result = end($this->_result);
			$this->_result = $this->_append($this->_result);
			return $this->_result;
		}

		/**
		* Monta a requisição de seleção especial
		*
		* @param string $type Tipo de seleção
		* @param string $column Nome da coluna a ser selecionada
		*
		* @return mixed
		*/
		private function _selectStrictOperation ($type, $column)
		{
			$queryString = "SELECT " . $type . "(d." . $column . ") AS q FROM (" . $this->_selectQueryString() . ") AS d";
			$query = $this->_exec($queryString);

			//Executar query e verificar se houve erros
			if($query instanceof Fault)
				return $query;

			$result = $query->fetch(PDO::FETCH_ASSOC);

			return $result['q'];
		}

		/**
		* Seleciona o valor máximo de uma coluna
		*
		* @param string $column Nome da coluna
		*
		* @return mixed
		*/
		public function selectMax ($column)
		{
			return $this->_selectStrictOperation('MAX', $column);
		}

		/**
		* Seleciona o valor mínimo de uma coluna
		*
		* @param string $column Nome da coluna
		*
		* @return mixed
		*/
		public function selectMin ($column)
		{
			return $this->_selectStrictOperation('MIN', $column);
		}

		/**
		* Seleciona a contagem de uma coluna
		*
		* @param string $column Nome da coluna
		*
		* @return mixed
		*/
		public function selectCount ($column)
		{
			return $this->_selectStrictOperation('COUNT', $column);	
		}

		/**
		* Seleciona a soma de uma coluna
		*
		* @param string $column Nome da coluna
		*
		* @return mixed
		*/
		public function selectSum ($column)
		{
			return $this->_selectStrictOperation('SUM', $column);	
		}

		/**
		* Resgata os resultados e os filtra de acordo com a chave (coluna) e valor
		*
		* @param string $column Nome da coluna a se tornar a chave do array associativo
		* @param string $value Valor a ser preservado
		* @param string $merge Caso haja valores com a mesma chave (coluna), fundí-los
		*
		* @return mixed
		*/
		public function resultList ($column = null, $value = null, $merge = true)
		{
			//Se não houver resultados, buscar
			if(!$this->hasResults())
			{
				$select = $this->select();
				
				//Verificar se é uma instância de erro
				if($select instanceof Fault)
					return $select;
			}

			//Se não há declarado coluna ou valor, retornar apenas os resultados
			if(!is_string($column) && !is_string($value))
				return $this->_result;

			$result = [];

			$results = $this->_result;

			//Se não houver resultados, ignorar
			if(empty($results))
				return $results;

			//Caso seja um array associativo (no caso de selectFirst ou selectLast, por exemplo), transformá-lo numa array sequencial temporariamente
			$associative = false;
			if($this->is_associative($results))
			{
				$associative = true;
				$results = [$results];
			}

			//Retirar alias da tabela, se houver
			if(is_string($column))
				$column = preg_replace('/^[\s\S]+?\./i', "", $column);

			foreach ($results as $resultValue)
			{
				//Se foi solicitado um determinado valor, utilizá-lo, senão utilizar valor da resposta
				$content = is_string($value) && array_key_exists($value, $resultValue) ? $resultValue[$value] : $resultValue;

				//Se existe uma coluna solicitada
				if(is_string($column))
				{
					if(!array_key_exists($column, $resultValue))
						return new Fault($this->write("Column '%s' is not part of table '%s'", 'icecream', [$column, $this->getTable()]), 'icecream_result_list_unknown_column');

					//Resgatar o valor da coluna atual
					$columnValue = $resultValue[$column];

					//Se já existe um valor nessa coluna
					if(array_key_exists($columnValue, $result))
					{	
						//Se ainda não for array, transformar em array
						if($merge === true && (!is_array($result[$columnValue]) || $this->is_associative($result[$columnValue])))
						{
							$result[$columnValue] = [$result[$columnValue]];
							continue;
						}

						//Se já é um array, adicionar
						if(is_array($result[$columnValue]) && !$this->is_associative($result[$columnValue]))
						{
							$result[$columnValue][] = $content;
							continue;
						}
					}
					if($merge === true)
						$result[$columnValue] = [$content];

					else 
						$result[$columnValue] = $content;

				}
				//Se não existe uma coluna solicitada, apenas incluir o valor
				else
				{
					$result[] = $content;
				}
			}

			//Se o array era associativo, retorná-lo a ser associativo
			if($associative)
				$results = $results[0];

			return $result;
		}

		/**
		 * Resgata uma lista de valores
		 * 
		 * @param type $value Nome da coluna com valor a ser resgatado
		 * @param string $merge Caso haja valores com a mesma chave (coluna), fundí-los
		 * 
		 * @return array
		 */
		public function list ($value, $merge = true)
		{
			return $this->resultList(null, $value, $merge);
		}

		/**
		 * Resgata o resultado indexado a partir do nome de uma coluna
		 * 
		 * @param string $column Nome da coluna a se tornar a chave do array associativo
		 * @param string $merge Caso haja valores com a mesma chave (coluna), fundí-los
		 * 
		 * @return array
		 */
		public function index ($column, $merge = true)
		{
			return $this->resultList($column, null, $merge);
		}

		/**
		 * Resgata um único valor de uma consulta
		 * 
		 * @param string $column Nome da coluna a ser resgatada
		 * 
		 * @return mixed
		 */
		public function pick ($column)
		{
			$result = $this->selectFirst();
			return !isset($result[$column]) ? null : $result[$column];
		}

		protected $_appendList = [];

		/**
		* Anexa dados de outra tabela de acordo com um valor em comum
		*
		* @param mixed $table Tabela que será anexada. Preferencialmente uma instância de Icecream
		* @param string $tableField Campo a tabela a ser anexada
		* @param string $currentField Campo da tabela atual a ser vinculado
		* @param string $tableValue (Opcional) Único valor a ser retornado
		*
		* @return object
		*/
		public function append ($table, $tableField, $currentField, $tableValue = null, $columnValue = null, $aggregate = false)
		{
			//Se não for uma instância, instanciar.
			if(gettype($table) == "string")
				$table = new Icecream($table);

			$this->_appendList[] = [
				'table' => $table,
				'tableField' => $tableField,
				'currentField' => $currentField,
				'tableValue' => $tableValue,
				'columnValue' => $columnValue,
				'aggregate' => $aggregate
			];
			return $this;
		}

		/**
		* Anexa dados de outra tabela de acordo com um valor em comum com um nome específico
		*
		* @param mixed $name Nome da tabela
		* @param mixed $table Tabela que será anexada. Preferencialmente uma instância de Icecream
		* @param string $tableField Campo a tabela a ser anexada
		* @param string $currentField Campo da tabela atual a ser vinculado
		* @param string $tableValue (Opcional) Único valor a ser retornado
		*
		* @return object
		*/
		public function appendAs ($name, $table, $tableField, $currentField, $tableValue = null, $columnValue = null, $aggregate = false)
		{
			//Se não for uma instância, instanciar.
			if(gettype($table) == "string")
				$table = new Icecream($table);

			$this->_appendList[] = [
				'name' => $name,
				'table' => $table,
				'tableField' => $tableField,
				'currentField' => $currentField,
				'tableValue' => $tableValue,
				'columnValue' => $columnValue,
				'aggregate' => $aggregate
			];
			return $this;
		}

		/**
		* Anexa um dado de outra tabela de acordo com um valor em comum com um nome específico
		*
		* @param mixed $table Tabela que será anexada. Preferencialmente uma instância de Icecream
		* @param string $tableField Campo a tabela a ser anexada
		* @param string $currentField Campo da tabela atual a ser vinculado
		* @param string $tableValue (Opcional) Único valor a ser retornado
		*
		* @return object
		*/
		public function appendSingle ($table, $tableField, $currentField, $tableValue = null)
		{
			//Se não for uma instância, instanciar.
			if(is_string($table))
				$table = new Icecream($table);

			return $this->appendSingleAs($table->getAlias(), $table, $tableField, $currentField, $tableValue);
		}

		/**
		* Anexa um dado de outra tabela de acordo com um valor em comum com um nome específico
		*
		* @param mixed $name Nome da tabela
		* @param mixed $table Tabela que será anexada. Preferencialmente uma instância de Icecream
		* @param string $tableField Campo a tabela a ser anexada
		* @param string $currentField Campo da tabela atual a ser vinculado
		* @param string $tableValue (Opcional) Único valor a ser retornado
		*
		* @return object
		*/
		public function appendSingleAs ($name, $table, $tableField, $currentField, $tableValue = null)
		{
			//Se não for uma instância, instanciar.
			if(is_string($table))
				$table = new Icecream($table);
				
			$this->_appendList[] = [
				'name' => $name,
				'table' => $table,
				'tableField' => $tableField,
				'currentField' => $currentField,
				'tableValue' => $tableValue,
				'columnValue' => null,
				'aggregate' => false,
				'single' => true
			];

			return $this;
		}

		/**
		* Anexa colunas de uma outra tabela
		*
		* @param mixed $table Tabela que será anexada. Preferencialmente uma instância de Icecream
		* @param string $tableField Campo a tabela a ser anexada
		* @param string $currentField Campo da tabela atual a ser vinculado
		* @param array $fields Campos a serem mesclados
		*
		* @return object
		*/
		public function attach ($table, $tableField, $currentField, $fields)
		{
			if(!is_array($fields))
				$fields = [$fields];

			$this->_appendList[] = [
				'name' => '_',
				'table' => $table,
				'tableField' => $tableField,
				'currentField' => $currentField,
				'tableValue' => null,
				'columnValue' => null,
				'aggregate' => false,
				'single' => true,
				'mapValue' => $fields
			];

			return $this;
		}

		/**
		* Realiza a inserção dos anexos
		*
		* @param string $results Array de resultados
		*
		* @return mixed
		*/
		protected function _append ($results)
		{
			//Conferir se os resultados enviados são um erro
			if($results instanceof Fault)
				return $results;

			//Lista de valores
			$valuesList = [];

			//Se não houver resultados, ignorar
			if(!$results || count($results) == 0)
				return [];

			//Para cada tabela a ser anexada
			foreach ($this->_appendList as $append)
			{

				//Nome a ser inserido na coleção de dados
				$tableName = !empty($append['name']) ? $append['name'] : $append['table']->getAlias();

				//Buscar IDs da tabela atual
				if(!isset($valuesList[$append['currentField']]))
					$valuesList[$append['currentField']] = $this->resultList(null, $append['currentField']);

				$values = $valuesList[$append['currentField']];

				//Buscar IDs na tabela a ser anexada
				$append['table']->whereIn($append['tableField'], $values);

				//Resgatar valores
				$tableResults = $append['table']->resultList($append['tableField'], is_null($append['columnValue']) ? $append['tableValue'] : null, true);
				
				if($tableResults instanceof Fault)
					return $tableResults;
				
				//Caso seja um array associativo (no caso de selectFirst ou selectLast, por exemplo), transformá-lo numa array sequencial temporariamente
				$associative = false;
				if($this->is_associative($results))
				{
					$associative = true;
					$results = [$results];
				}

				//Incluir dados 
				foreach ($results as &$value)
				{
					//Chave atual
					$currentFieldValue = $value[$append['currentField']];

					//Resgatar todos os resultados
					$contentResults = $tableResults[$currentFieldValue] ?? [];
					
					if(!empty($append['single']))
					{
						if(!empty($append['tableValue']))
							$contentResults = $contentResults[0] ?? null;
						else
							$contentResults = $contentResults[0] ?? new \ArrayObject();
						
						if(isset($append['mapValue']))
						{
							foreach($append['mapValue'] as $key => $field)
							{
								$key = is_numeric($key) ? $field : $key;
								$value[$field] = $contentResults[$key] ?? null;
							}
							continue;
						}
					}

					//Se foi pedido uma coluna específica como chave
					else if(!is_null($append['columnValue']))
					{
						//Criar nova lista e buscar cada um dos resultados da lista
						$list = [];
						foreach ($contentResults as $content)
						{
							if(!isset($content[$append['columnValue']]))
								return new Fault("Column '" . $append['columnValue'] . "' cannot be key for this table as it doesn't exist. Available columns: " . implode(", ", array_keys($content)), "icecream_append_inexistent_key_column", $this);

							$currentColumnValue = $content[$append['columnValue']];
							
							//Se esse índice já existir e for associativo ou não for array , transformar em Array
							if(isset($list[$currentColumnValue]) && (!is_array($list[$currentColumnValue]) || $this->is_associative($list[$currentColumnValue])))

								$list[$currentColumnValue] = [$list[$currentColumnValue]];

							//Se foi solicitado um valor único, pegá-lo
							if(!is_null($append['tableValue']))
								$content = $content[$append['tableValue']];	
							
							//Se já existe, incluir como array, senão, adicionar
							if(isset($list[$currentColumnValue]) || $append['aggregate'])
								$list[$currentColumnValue][] = $content;

							else
								$list[$currentColumnValue] = $content;
						}
						$contentResults = $list;
					}
					
					$value[$tableName] = $contentResults;					
				}
				//Se o array era associativo, retorná-lo a ser associativo
				if($associative)
					$results = $results[0];
			}
			return $results;
		}
	}