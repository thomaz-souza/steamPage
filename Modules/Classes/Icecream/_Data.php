<?php
/**
* Objeto de funções de inserção e atualização de dados
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \Fault;

	trait _Data
	{
		/**
		* Realiza a separação dos valores a serem inseridos
		*
		* @param string $type Tipo da operação
		* @param array $values Valores
		* @param array $map Mapa de valores
		*
		* @return array
		*/
		private function _parseValues ($type, $values, $map = null)
		{
			//Array de valores
			$valuesArray = array();

			//Selecionar o mapa de dados
			$dataMap = is_array($map) ? $map : $values;

			foreach ($dataMap as $column => $value)
			{
				//Pular se o campo não deve ser inserido/atualizado
				if( is_array($map) && isset($value[$type]) && $value[$type] === false)
					continue;

				//Se foi enviado um mapa que exige o campo, verifica se o mesmo existe
				if( is_array($map) && isset($value['mandatory']) && !isset($values[$column]))
					return false;

				$columnName = $column;

				//Se foi solicitado um novo nome de coluna
				if( is_array($map) && isset($value['columnName']))
					$columnName = $value['columnName'];

				//Se o campo não existe, usar o valor padrão
				if( (!isset($values[$column]) && !is_null($values[$column])) && isset($value['defaultValue']) )
					$content = $value['defaultValue'];

				//Se estiver atualizando, campo não existe e não há valor padrão, ignorá-lo
				else if($type=="update" && (!isset($values[$column]) && !is_null($values[$column])))
					continue;

				//Se o campo não existe e não há valor padrão, incluí-lo como null
				else if( !isset($values[$column]) )
					$content = null;

				else
					$content = $values[$column];

				//Incluir o valor
				$valuesArray[$columnName] = $this->_setParam($columnName, $content);
			}

			return $valuesArray;
		}

		/**
		* Realiza a inserção de dados
		*
		* @param array $values Valores a serem inseridos
		* @param array $map Mapa de valores
		*
		* @return object
		*/
		public function insert ($values, $map = null)
		{
			//Se for uma outra tabela, recuperar resultados
			if($values instanceof Icecream)
				$values = $values->resultList();
			

			//Se for um array associativo, transformar em um array sequencial
			if($this->is_associative($values))
				$values = [$values];

			//Valores a serem inseridos
			$queryValues = [];			

			foreach ($values as $value)
			{
				$valuesArray = $this->_parseValues('insert', $value, $map);
				$queryValues[] = "(" . implode(",", $valuesArray) . ")";
			}

			//IDs inseridos
			$inserted = [];

			foreach (array_chunk($queryValues, $this->_getConnectionConfig()['query_limit']) as $chunkValues)
			{
				//Criar query
				$queryString = "INSERT INTO " . $this->getTable() . " (" . implode(',', array_keys($valuesArray)) . ") VALUES " . implode(",", $chunkValues);

				//Executar inserção
				$res = $this->_exec($queryString);

				$inserted[] = $this->connection->lastInsertId();
			}
			return count($inserted)==1 ? end($inserted) : $inserted;
		}

		/**
		* Realiza a atualização de dados
		*
		* @param array $values Valores a serem atualizados
		* @param array $map Mapa de valores
		*
		* @return object
		*/
		public function update ($values, $map = null)
		{
			//Se não houver condições, interromper
			if(empty($this->_assembleWhere()))
				return new Fault($this->write('For security reasons you need to declare conditions with \'where\' clause for this operation', 'icecream'), 'icecream_security_where');

			//Se for uma outra tabela, recuperar resultados
			if($values instanceof Icecream)
				$values = $values->resultList();
			
			//Se NÃO for um array associativo, interromper
			if(!$this->is_associative($values))
				return new Fault($this->write('It is not possible to update multiple values', 'icecream'), 'icecream_multiple_values');			

			//Parsear os valores recebidos
			$valuesArray = $this->_parseValues('update', $values, $map);			
			
			//Criar os valores
			array_walk($valuesArray,
				function(&$a, $b) { $a = "$b=$a"; }
			);

			//Criar query
			$query = [
				"UPDATE " . $this->getTable() . " SET " . implode(',', $valuesArray),
				$this->_assembleWhere(),
				$this->_assembleLimit()
			];

			//Criar string de query
			$queryString = implode(" ", $query);

			//Executar inserção
			$result = $this->_exec($queryString);

			if($result instanceof Fault)
				return $result;

			return $result->rowCount();
		}

		/**
		* Realiza a atualização de um ou mais campos como a data
		*
		* @param array $columns Uma ou mais colunas a serem atualizadas
		* @param array $date (opcional) Data, se não enviado utiliza a data atual do sistema
		*
		* @return object
		*/
		public function updateDate ($columns, $date = null)
		{
			//Se o campo data for vazio, incluir data atual
			if(empty($date))
				$date = date('Y-m-d H:i:s');

			//Normalizar colunas como arrays
			if(!is_array($columns))
				$columns = [$columns];

			//Lista de atualização
			$update = [];

			//Atribuir a data às colunas
			foreach ($columns as $column)
				$update[$column] = $date;

			//Realizar o update
			return $this->update($update);
		}

		/**
		* Se não houver o registro, cria, se houver, atualiza os dados
		*
		* @param array $values Valores a serem atualizados
		* @param array $map Mapa de valores
		* @param boolean $keepConditions Manter condições após inserção
		*
		* @return object
		*/
		public function place ($values, $map = null, $keepConditions = true, $keepParams = false)
		{
			//Se não houver condições, interromper
			if(empty($this->_assembleWhere()))
				return new Fault($this->write('For security reasons you need to declare conditions with \'where\' clause for this operation', 'icecream'), 'icecream_security_where');

			//Impedir que os parâmetros sejam excluídos
			$this->_allowEraseParams(false);

			//Consultar se o registro em questão existe
			$exist = $this->exist();

			//Se a consulta retornou um erro
			if($exist instanceof Fault)
				return $exist;

			//Se existir, atualizar
			if($exist)
				return $this->update($values, $map);

			//Caso não deseje manter as condições, apagá-las
			if($keepConditions === false)
				$this->_eraseConditions();
			
			//Apagar os parâmetros anteriores
			if($keepParams === false)
				$this->_eraseParam();

			//Se não existir, inserir
			return $this->insert($values, $map);
		}

		/**
		* Se não houver o registro, cria, se houver, atualiza os dados de maneira automática (sem necessidade de mapa). Sempre retorna o ID
		*
		* @param array $data Valores a serem atualizados/criados
		* @param string $keyColumn Nome da coluna de ID
		* @param mixed $keyValue (opcional) Valor do ID
		* @param array $ignore (opcional) Lista de campos a serem ignorados
		*
		* @return string
		*/
		public function put ($data, $keyColumn, $keyValue = null, $ignore = [])
		{
			//Abre a tabela com a chave escolhida
			$this->fetch($keyColumn, $keyValue);

			//Pegar colunas carregadas
			$columns = array_keys($this->getColumns());

			foreach ($data as $column => $value)
			{	
				//Pular campo caso ele esteja na lista para ser ignorado
				if(in_array($column, $ignore))
					continue;

				//Alterar cada valor se existir
				if(in_array($column, $columns))
					$this->$column = $value;
			}

			//Salvar alterações na tabela
			$this->save();

			//Retornar o valor da coluna chave
			return $this->$keyColumn;
		}


	}