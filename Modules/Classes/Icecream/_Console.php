<?php
/**
* Objeto de funções do console
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \PDO;
	use \Fault;

	trait _Console
	{
		public function generateModels ($console)
		{
			//Remover todos os arquivos da pasta Models
			foreach (scandir($this->getPath('Models')) as $file)
			{
				if(preg_match("/Model\\.php$/", $file))
					unlink($this->getPath('Models/'. $file));
			}

			//Resgatar configurações de banco de dados
			$dbConfig = $this->_config('db');

			foreach ($dbConfig['servers'] as $serverId => $server)
			{
				//Database atual
				$database = $server['database'];

				$console->output($this->write("Searching tables in database '%s' from server '%s'", 'icecream', [$database, $serverId]));

				//Montar Query
				$query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = " . $this->_setParam('database', $database);

				//Executar
				$query = $this->_exec($query);

				if($query instanceof Fault)
				{
					$console->output($query, true);
					continue;
				}

				//Tabelas
				$tables = $query->fetchAll(PDO::FETCH_ASSOC);
				$console->output("	" . $this->write("'%s' tables found", 'icecream', count($tables)));

				foreach ($tables as $table)
				{
					//Resgatar nome da tabela e converter para Camel Case
					$tableName = $table['TABLE_NAME'];
					$modelName = Icecream::convertToCamelCase($tableName);

					//Caso o arquivo já exista, incluir nome do servidor
					if(file_exists($this->getPath("Models/" . $modelName . "Model.php")))
						$modelName .= ucfirst($serverId);

					//Incluir palavra "Model" no final do nome
					$modelName .= "Model";

					//Informar sobre o novo nome
					$console->output("	" . $this->sprintWrite("Generating model for table '%s' as '%s'", 'icecream', 
						[$console->effect($tableName, 'underline'), $console->effect($modelName, 'green')]));

					//Criar conteúdo do arquivo
					$content = '<?php' . PHP_EOL .
								'/**' . PHP_EOL .
								'*' . $this->write("Do not change this file. It was automatically generated and can be deleted in future", 'icecream') . PHP_EOL .
								'*' . $this->write("Auto generated Model from server '%s' database '%s' for table '%s'", 'icecream', [$serverId, $database, $tableName]) . PHP_EOL .
								'*'  . PHP_EOL .
								'* @package	Models'  . PHP_EOL .
								'*/'  . PHP_EOL .  PHP_EOL .
									
								'	use Icecream\IcecreamModel;' . PHP_EOL .  PHP_EOL .

								'	class ' . $modelName . ' extends IcecreamModel' . PHP_EOL .
								'	{' . PHP_EOL .
								'		const TABLE = "'.$tableName.'";' . PHP_EOL . PHP_EOL .
								'		public function __construct ()' . PHP_EOL .
								'		{' . PHP_EOL .
								'			$this->setServer("'.$serverId.'");' . PHP_EOL .
								'			parent::__construct();' . PHP_EOL .
								'		}' . PHP_EOL .
								'	}';

					//Salvar arquivo
					file_put_contents($this->getPath("Models/" . $modelName . ".php"), $content);
				}
			}
		}
	}