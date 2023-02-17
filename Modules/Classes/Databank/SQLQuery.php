<?php
/**
* Classe responsável por queries SQL
*
* @package		Classes
* @author 		Lucas/Postali
*/
	
	Namespace Databank;

	class SQLQuery extends Connection
	{
		/**
		* Conecta-se a uma database
		*
		* @param string $databaseName Recebe o nome da database
		* @return null
		*/
		protected function _sqlDatabase ($databaseName, $server = null)
		{
			mysqli_select_db($this->_sqlConnection($server), $databaseName);
		}
		
		/**
		* Executa uma consulta ao banco de dados
		*
		* @param string $query A query a ser executada
		* @return object
		*/
		protected function _sqlQuery ($query, $server = null)
		{
			$config = $this->_getConnectionConfig($server);

			//Conectar-se ao banco caso a conexão não tenha sido feita
			if(!isset($this->sqlConnection[$config['server']]))
				$this->_sqlConnect($server);

			return mysqli_query($this->sqlConnection[$config['server']], $query);
		}

		/**
		* Parsear dados do banco em um array.
		*
		* @param object $query Resultado da query realizada
		* @param string $id Servirá como chave de cada valor do array
		* @param string $value Único valor a ser resgatado da consulta
		* @return array
		*/
		protected function _sqlToArray ($query, $id = null, $value = null)
		{
			$arr = array();

			while( $values = mysqli_fetch_assoc($query) )
			{
				$val = $value ? $values[$value] : $values;

				if($id)
					$arr[$values[$id]] = $val;
				else
					$arr[] = $val;
			}
			return $arr;
		}
	}