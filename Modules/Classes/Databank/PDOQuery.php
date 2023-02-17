<?php
/**
* Classe responsável por executar automatizar Queries PDO
*
* @package		Classes
* @author 		Lucas/Postali
*/
	
	Namespace Databank;
	use \PDO;

	class PDOQuery extends Connection
	{

		/**
		* Buscar dados no banco
		*
		* @param string $tableName Nome da tabela
		* @param mixed $fields Array ou String com valores que o usuário deseja buscar
		* @param array $conditions Array associativo com condições para realizar a busca, cláusula WHERE
		* @return array
		*/
		protected function _pdoSelect($tableName, $fields = '*', $conditions = array())
		{
			//Verificar se $fields tem um valor inválido
			if($fields == '' || $fields === null || $fields === false) $fields = '*';

			//Verificar se há campos em $fields
			if(gettype($fields) == 'array' && count($fields) == 0) $fields = '*';

			//Se os campos enviados forem string, transformar em array
			if(gettype($fields) == "string") $fields = array($fields);

			//Serializar os campos a consultar
			$implodedFields = implode(",", $fields);

			//Serializar condições, caso enviado
			$implodedConditions = count($conditions)>0 ? 'WHERE ' . implode(' ', $conditions) : '';

			//Criar e executar query
			$query = "SELECT $implodedFields FROM $tableName $implodedConditions";
			$result = $this->_pdoConnection()->prepare($query);
			$result->execute();

			//Retornar resultados
			return $result->fetchAll(PDO::FETCH_ASSOC);
		}

		/**
		* Atualizar dados no banco de maneira simplificada
		*
		* @param string $tableName Nome da tabela
		* @param array $values Array com valores da tabela (coluna => valor)
		* @param array $conditions Condições para realizar a atualização, cláusula WHERE (valor)
		* @param array $tableColumns Array mapeando a tabela permitindo mais controle sobre os dados inseridos
		* @param array $limit Limite de linhas a serem excluídas
		* @return boolean
		*/
		protected function _pdoUpdate($tableName, $values, $conditions, $tableColumns = null, $limit = 1)
		{
			//Array de valores
			$valuesArray = array();

			//Selecionar o mapa de dados
			$dataMap = gettype($tableColumns) == 'array' ? $tableColumns : $values;

			foreach ($dataMap as $column => &$value) {
				
				//Pular se o campo não deve ser atualizado
				if( gettype($tableColumns) == 'array' && isset($value['update']) && $value['update'] === false)
					continue;

				//Se foi enviado um mapa que exige o campo, verifica se o mesmo existe
				if( gettype($tableColumns) == 'array' && isset($value['mandatory']) && !isset($values[$column]))
					return false;

				//Se o valor estiver no mapa, mas não foi enviado e não o houver valor padrão, ignorar
				if( !isset($values[$column]) && (gettype($tableColumns) == 'array' && !isset($val['defaultValue']) ) )
					continue;

				$columnName = $column;

				//Se foi solicitado um novo nome de coluna
				if( gettype($tableColumns) == 'array' && isset($value['columnName']))
					$columnName = $value['columnName'];

				//Incluir o valor em questão no array
				$valuesArray[] = $columnName . "=" . ":$column";
			}

			//Definir limite de linhas a serem atualizadas
			$limit = $limit === false ? '' : " LIMIT $limit";

			//Criar e executar query
			$query = "UPDATE $tableName SET " . implode(',', $valuesArray) . " WHERE " . implode(' AND ', $conditions) . $limit;
			$result = $this->_pdoConnection()->prepare($query);

			//Atribuir valores aos campos
			foreach ($dataMap as $column => $val)
			{
				//Pular se o campo não deve ser atualizado
				if( gettype($tableColumns) == 'array' && isset($val['update']) && $val['update'] === false)
					continue;

				//Se o campo não existe, usar o valor padrão
				if( !isset($values[$column]) && isset($val['defaultValue']) )
					$values[$column] = $val['defaultValue'];

				//Se o campo não existe e não há valor padrão, pular
				else if( !isset($values[$column]) )
					continue;

				$result->bindValue(":$column", $values[$column]);
			}
			
			return $result->execute();
		}

		/**
		* Inserir dados no banco de maneira simplificada
		*
		* @param string $tableName Nome da tabela
		* @param array $values Array com valores da tabela (coluna => valor)
		* @param array $tableColumns Array mapeando a tabela permitindo mais controle sobre os dados inseridos
		* @return mixed
		*/
		protected function _pdoInsert($tableName, $values, $tableColumns = null)
		{
			//Array de valores
			$valuesArray = array();

			//Array de colunas
			$columnsArray = array();

			//Selecionar o mapa de dados
			$dataMap = gettype($tableColumns) == 'array' ? $tableColumns : $values;

			foreach ($dataMap as $column => $value) {
				
				//Pular se o campo não deve ser inserido
				if( gettype($tableColumns) == 'array' && isset($value['insert']) && $value['insert'] === false)
					continue;

				//Se foi enviado um mapa que exige o campo, verifica se o mesmo existe
				if( gettype($tableColumns) == 'array' && isset($value['mandatory']) && !isset($values[$column]))
					return false;

				$columnName = $column;

				//Se foi solicitado um novo nome de coluna
				if( gettype($tableColumns) == 'array' && isset($value['columnName']))
					$columnName = $value['columnName'];

				//Incluir o valor
				$valuesArray[] = $column;

				//Incluir a coluna
				$columnsArray[] = $columnName;
			}

			//Criar e executar query
			$query = "INSERT INTO $tableName (" . implode(',', $columnsArray) . ") VALUES (:" . implode(',:', $valuesArray) . ")";

			$result = $this->_pdoConnection()->prepare($query);

			$a = [];

			//Atribuir valores aos campos
			foreach ($dataMap as $column => $val)
			{
				//Pular se o campo não deve ser inserido
				if( gettype($tableColumns) == 'array' && isset($val['insert']) && $val['insert'] === false)
					continue;

				//Se o campo não existe, usar o valor padrão
				if( !isset($values[$column]) && isset($val['defaultValue']) )
					$content = $val['defaultValue'];

				//Se o campo não existe e não há valor padrão, incluí-lo como null
				else if( !isset($values[$column]) )
					$content = null;

				else
					$content = $values[$column];

				$a[":".$column] = $content;

				$result->bindValue(":".$column, $content);
			}
			
			//return $a;
			//Executar e retornar Id
			$result->execute();
			
			return $this->_pdoConnection()->lastInsertId();
		}

		/**
		* Checa quantos campos existem
		*
		* @param string $tableName Nome da tabela
		* @param array $conditions Array com condições para checagem
		* @return integer
		*/
		protected function _pdoCount($tableName, $conditions = array())
		{
			$conditions = count($conditions)>0 ? " WHERE " . implode(" AND ", $conditions) : '';

			//Criar e executar query
			$query = "SELECT COUNT(*) AS c FROM $tableName" . $conditions;
			$result = $this->_pdoConnection()->prepare($query);

			$result->execute();
			
			//Retornar resultados
			return (int) $result->fetch(PDO::FETCH_ASSOC)['c'];
		}

		/**
		* Deletar dados no banco de maneira simplificada
		*
		* @param string $tableName Nome da tabela
		* @param array $conditions Condições para realizar a atualização, cláusula WHERE (valor)
		* @param array $limit Limite de linhas a serem excluídas
		* @return boolean
		*/
		protected function _pdoDelete($tableName, $conditions, $limit = 1)
		{
			//Definir limite de linhas a serem excluídas
			$limit = $limit === false ? '' : " LIMIT $limit";

			//Criar e executar query
			$query = "DELETE FROM $tableName WHERE " . implode(' AND ', $conditions) . $limit;
			$result = $this->_pdoConnection()->prepare($query);
			return $result->execute();
		}

		private $_loadedTables = array();

		public function __call($name, $arguments)
	    {
	    	//BUSCAR: Executar chamado para modelo: tabelaSelect ($colunas)
	        if(preg_match("/^([\s\S]+?)Select$/", $name, $m))
	        	return $this->_pdoSelect($m[1], isset($arguments[0]) ? $arguments[0] : '*', isset($arguments[1]) ? $arguments[1] : [] );
	        
	        //BUSCAR: Executar chamado para modelo: tabelaSelectByCampo ($valorDoCampo, $colunas)
	        if(preg_match("/^([\s\S]+?)SelectBy([\s\S]+)$/", $name, $m))
	        	return $this->_pdoSelect($m[1], isset($arguments[1]) ? $arguments[1] : '*', array("$m[2]='$arguments[0]'"));

	        //DELETAR: Executar chamado para tabelaDeleteByCampo ($valorDoCampo, $limit)
	        if(preg_match("/^([\s\S]+?)DeleteBy([\s\S]+)$/", $name, $m))
	        	return $this->_pdoDelete($m[1], array("$m[2]='$arguments[0]'"), isset($arguments[1]) ? $arguments[1] : 1 );

	        //ATUALIZAR: Executar chamado para tabelaUpdateByCampo ($valorDoCampo, $valores, $map, $limit)
	        if(preg_match("/^([\s\S]+?)UpdateBy([\s\S]+)$/", $name, $m))
	        	return $this->_pdoUpdate($m[1], $arguments[1], array("$m[2]='$arguments[0]'"), isset($arguments[2]) ? $arguments[2] : null, isset($arguments[3]) ? $arguments[3] : 1);

	        //INSERIR : Executar chamado para tabelaInsert ($valores, $map)
	        if(preg_match("/^([\s\S]+?)Insert$/", $name, $m))
	        	return $this->_pdoInsert($m[1], $arguments[0], isset($arguments[1]) ? $arguments[1] : null);

	        //CONTAR/VERIFICAR: Executar chamado para tabelaCountBycampo($valorDoCampo, $where)
	        if(preg_match("/^([\s\S]+?)CountBy([\s\S]+)$/", $name, $m))
	        	return $this->_pdoCount($m[1], isset($arguments[0]) ? array("$m[2]='$arguments[0]'") : array());

	        //CONTAR/VERIFICAR: Executar chamado para tabelaCount()
	        if(preg_match("/^([\s\S]+?)Count$/", $name, $m))
	        	return $this->_pdoCount($m[1], array());

	        //INSERIR OU DAR UPDATE: Executar chamado para tabelaInsertOrUpdateBycampo ($valores, $map, $limit, $soft)
	        if(preg_match("/^([\s\S]+?)UpdateOrInsertBy([\s\S]+)$/", $name, $m))
	        {

	        	$table = $m[1];
	        	$field = $m[2];

	        	if(isset($arguments[3]) && $arguments[3] === true)
	        	{
					//Se a tabela ainda não está na lista de carregadas, incluir
					if(!isset($this->_loadedTables[$table]))
						$this->_loadedTables[$table] = [];

					//Se a tabela existe mas não foi carregada, carregar
					if(!isset($this->_loadedTables[$table][$field]))
					{
						$list = $this->_pdoSelect($table, array($field));
						foreach ($list as $val)
							$this->_loadedTables[$table][$field][] = $val;
					}
				}

	        	$value = $arguments[0][$field];

	        	//Se já houver um item com esse código, atualizar

	        	if(
	        		(isset($arguments[3]) && $arguments[3] === true && in_array($value, $this->_loadedTables[$table][$field])) ||
	        		( (!isset($arguments[3]) || $arguments[3] !== true)  && $this->_pdoCount($table, array("$field='$value'")) > 0 )
	        	)
	        	{
	        		return $this->_pdoUpdate($table, $arguments[0], array("$field='$value'"), isset($arguments[1]) ? $arguments[1] : null, isset($arguments[2]) ? $arguments[2] : 1);
	        	
	        	//Se não houver um item com esse código, criar
	        	}else{
	        		$this->_loadedTables[$table][$field][] = $value;
	        		return $this->_pdoInsert($table, $arguments[0], isset($arguments[1]) ? $arguments[1] : null);
	        	}
	        	
	        }
	    }
	}

?>