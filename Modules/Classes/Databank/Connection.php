<?php
/**
* Classe responsável por se conectar ao banco de dados e abrir uma conexão SQL ou PDO
*
* @package		Classes
* @subpackage	sql
* @author 		Lucas/Postali
*/

	Namespace Databank;
	
	class Connection extends \Core
	{
		
		// Configurações de conexão com o banco de dados
		private $dbConfig;

		//Charset padrão da conexão, usado se não definido no arquivo de configuração
		private $defaultCharset = "utf8";
		
		/**
		* Instancia a função
		*
		* @return null
		*/
		function __construct($server = null)
		{
			//Inicia a função pai (_core)
			parent::__construct();

			//Definir servidor
			$this->setServer($server);
		}

		protected $currentServer = 'default';

		/**
		* Define o servidor a ser trabalhado
		*
		* @return null
		*/
		public function setServer ($server = null)
		{
			if($server === null)
				$server = 'default';

			$this->currentServer = $server;

			return $this;
		}

		/**
		* Retorna as configurações de conexão
		*
		* @return array
		*/
		protected function _getConnectionConfig ($server = null)
		{
			$dbConfig = $this->_config('db');

			if($server === null)
			{
				if($this->currentServer && isset($dbConfig['servers'][$this->currentServer]))
					$server = $this->currentServer;
				else if(isset($dbConfig['servers']))
					$server = array_key_first($dbConfig['servers']);

				$this->setServer($server);
			}

			$config = $dbConfig['servers'][$server];
			$config['server'] = $server;
			return $config;
		}

		// Variável de conexão SQL que só será acessada pelas classes filhas
		protected $sqlConnection = array();

		/**
		* Conecta-se ao banco de dados através do SQLI usando as configurações carregadas
		*
		* @return null
		*/
		protected function _sqlConnect ($server = null)
		{	
			$config = $this->_getConnectionConfig($server);

			//Cria a conexão
			$this->sqlConnection[$config['server']] = mysqli_connect($config['host'], $config['user'], $config['password']);
			
			//Define a database
			$this->_sqlDatabase($config['database']);

			//Define o charset da conexão
			$charset = isset($config['charset']) ? $config['charset'] : $this->defaultCharset;
			mysqli_set_charset($this->_sqlConnection($server), $charset);
			$this->_sqlQuery("SET NAMES " . $charset);
		}

		/**
		* Retorna a conexão do banco de dados
		*
		* @return object
		*/
		protected function _sqlConnection ($server = null)
		{
			$config = $this->_getConnectionConfig($server);
			return $this->sqlConnection[$config['server']];
		}

		// Variável de conexão PDO que só será acessada pelas classes filhas
		protected $pdoConnection = null;

		// Driver padrão para conexões PDO
		private $defaultDriver = 'mysql';

		/**
		* Conecta-se ao banco de dados através do PDO usando as configurações carregadas
		*
		* @return null
		*/
		protected function _pdoConnection ($server = null)
		{
			$config = $this->_getConnectionConfig($server);

			//Se não há uma conexão ativa, conectar
			if(!isset($this->pdoConnection[$config['server']."_pdo"]))
			{
				//Se foi definida uma strind de conexão, utilizá-la
				if( isset($config['pdo_connection_string']) )
				{
					$connectionString = $config['pdo_connection_string'];
				}
				else
				{	
					//Se não houver uma string definida, buscar o driver e montar a string
					$driver = isset($config['pdo_drive']) ? $config['pdo_drive'] : $this->defaultDriver;
					$connectionString = $driver . ':host=' . $config['host'] . ';dbname=' . $config['database'];
				}

				//Criar conexão
				try{
				$this->pdoConnection[$config['server']."_pdo"] = new \PDO($connectionString, $config['user'], $config['password']);
				}
				catch(\PDOException $e)
				{
					$this->Error($e->getMessage(), true, $this->write('It was not possible to connect to the databank','validation'));
				}

				// Define o charset da conexão
				$charset = isset($config['charset']) ? $config['charset'] : $this->defaultCharset;
				$this->pdoConnection[$config['server']."_pdo"]->exec("SET NAMES $charset");
			}
			return $this->pdoConnection[$config['server']."_pdo"];
		}

	}