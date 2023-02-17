<?php
/**
* Objeto de funções de resultados e queries
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \PDO;
	use \Fault;

	trait _Results
	{
		private $_result;
		private $_query;

		/**
		* Verifica se há resultados prontos
		*
		* @return boolean
		*/
		public function hasResults ()
		{
			return is_null($this->_result) ? false : true;
		}

		/**
		* Apaga os resultados anteriores
		*
		* @return null
		*/
		public function eraseResults ()
		{
			$this->_result = null;
		}

		private $_throwError = false;

		/**
		 * Informa se o erro deve ser exibido de maneira simples, com um Fault (false), ou de maneira severa (true)
		 * 
		 * @param  boolean $condition Condição
		 * 
		 * @return Icecream
		 */
		public function throwError ($condition = true)
		{
			if($condition === false || $condition === true)
				$this->_throwError = $condition;

			return $this;
		}

		/**
		 * Informa se o erro deve ser exibido de maneira simples, com um Fault (false), ou de maneira severa (true) para todas as instâncias do Icecream
		 * 
		 * @param  boolean $condition Condição
		 * 
		 * @return null
		 */
		static public function alwaysThrowError ($condition = true)
		{
			if($condition === false || $condition === true)
				$GLOBALS['icecream_throw_error'] = $condition;
		}

		/**
		* Cria um objeto de Fault de acordo com o erro do PDO
		*
		* @param object $object Objeto PDO
		*
		* @return object
		*/
		private function _parseError ($object)
		{
			//Resgatar detalhes do erro
			$error = $object->errorInfo();

			$description = is_null($error[2]) ? 'Unknown error' : $error[2];

			//Se permitido que o erro seja enviado
			if($this->_throwError)
				Error($description, false);

			//Retornar um objeto Fault com detalhes do erro
			else
				return new Fault($description, $error[0], $this);

		}

		public $connection;

		/**
		* Executa um comando do PDO
		*
		* @param string $queryString String da query
		* @param boolean $bind Permite o vínculo de parâmetros
		*
		* @return mixed
		*/
		private function _exec ($queryString, $bind = true)
		{
			if(self::isTraceEnabled())
				trace("Executing query", 'Icecream', ['query' => $queryString, 'params' => $this->getParam() ]);

			//Abre a conexão
			try
			{
				$this->connection = $this->_pdoConnection();

				//Permitir que os valores retornem tipados (inteiros e floats)
				if(substr($queryString, 0, 6) == "SELECT")
				{
					$this->connection->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
					$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES , false);
				}
			}
			catch(PDOException $e)
			{
				return $this->_parseError($this->connection);
			}

			//Prepara a query
			try
			{
				$query = $this->connection->prepare($queryString);
			}
			catch(PDOException $e)
			{
				return $this->_parseError($query);
			}

			//Caso a query tenha algum erro, retornar erro
			if(!$query)
				return $this->_parseError($this->connection);

			//Incluir valores
			if($bind === true)
				$this->_bindParam($query);

			//Executar query
			$result = $query->execute();

			//Verificar se houve erros
			if(!$result)
				return $this->_parseError($query);

			return $query;
		}
	}