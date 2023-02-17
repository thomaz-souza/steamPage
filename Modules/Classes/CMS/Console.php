<?php
/**
* Funções de Console
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMS;

	use \Fault;

	class Console extends CMS
	{		
		public function install (\Console\Console $console)
		{	
			//Caso haja o argumento "clear", remover todas as tabelas
			if($console->getArguments(0) == "clean")
				$this->_dropAllTables($console);

			$console->output($this->write("Creating tables", "admin"));

			//Criar tabelas
			$this->_createTableUser($console);
			$this->_createTableUserSession($console);
			$this->_createTableUserPermissions($console);

			//Criar uma senha
			$password = substr(md5(uniqid()), 0, 6);

			//Criar o usuário padrão
			$this->_createUser($console, self::ADMIN_USER, $password, $this->write("Admin User", "admin"));
		}

		/**
		* Criar um usuário, recebendo login e senha
		*
		* @return null
		*/
		public function createUser (\Console\Console $console)
		{
			//Caso não haja login ou senha, retornar erro
			if(!$console->getArguments(0) || !$console->getArguments(1))
				$console->output($this->write("You must send a login and password", "admin"), true);				

			//Resgatar variáveis e retirar aspas
			$user = preg_replace("/\"/", "", $console->getArguments(0));
			$password = preg_replace("/\"/", "", $console->getArguments(1));
			$userName = preg_replace("/\"/", "", ($console->getArguments(2)) ? $console->getArguments(2) : $this->write("User Name", "admin"));

			//Criar o usuário
			$this->_createUser($console, $user, $password, $userName);
		}

		/**
		* Remove uma tabela
		*
		* @return null
		*/
		private function _dropTable ($console, $table)
		{
			$result = $table->drop();

			//Exibir erro se houver
			if($result instanceof Fault)
				return $console->output($result, true);

			//Exibir criação
			$console->output($console->effect('[' . $this->write("DROPPED", "admin") . ']', 'yellow') . " " . $this->write("Table '%s'", "admin", $table->getTable()));
		}

		/**
		* Remove as tabelas que existirem
		*
		* @return null
		*/
		private function _dropAllTables ($console)
		{
			if(Tables::user()->existTable())
				$this->_dropTable($console, Tables::user());

			if(Tables::userSession()->existTable())
				$this->_dropTable($console, Tables::userSession());

			if(Tables::userPermissions()->existTable())
				$this->_dropTable($console, Tables::userPermissions());
		}

		/**
		* Cria uma nova tabela
		*
		* @return null
		*/
		private function _createTable ($console, $table, $columns)
		{	
			//Verifica se uma tabela existe
			if($table->existTable())
				return $console->output($this->write("Table '%s' already exists. Skipping.", "admin", $table->getTable()));

			//Criar tabela
			$result = $table->create($columns);

			//Exibir erro se houver
			if($result instanceof Fault)
				return $console->output($result, true);

			//Exibir criação
			$console->output($console->effect('[' . $this->write("CREATED", "admin") . ']', 'green') . " " . $this->write("Table '%s'", "admin", $table->getTable()));
		}

		/**
		* Cria a tabela de Usuário
		*
		* @return null
		*/
		private function _createTableUser ($console)
		{
			//Instância da tabela
			$table = Tables::user();

			//Colunas da tabela
			$columns = [
				'id' => [
					'type' => 'int',
					'key' => 'primary',
					'default' => 'NOT NULL AUTO_INCREMENT'
				],
				'login' => [
					'type' => 'varchar',
					'length' => 255
				],
				'password' => [
					'type' => 'varchar',
					'length' => 255
				],
				'name' => [
					'type' => 'varchar',
					'length' => 255
				],
				'dateCreate' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP'
				],
				'dateUpdate' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
				],
				'dateDelete' => [
					'type' => 'datetime',
					'default' => 'NULL'
				]
			];

			//Criar
			$this->_createTable($console, $table, $columns);			
		}

		/**
		* Cria a tabela de Sessões
		*
		* @return null
		*/
		private function _createTableUserSession ($console)
		{
			//Instância da tabela
			$table = Tables::userSession();

			//Colunas da tabela
			$columns = [
				'id' => [
					'type' => 'int',
					'key' => 'primary',
					'default' => 'NOT NULL AUTO_INCREMENT'
				],
				'id_user' => [
					'type' => 'int'
				],
				'key_user' => [
					'type' => 'varchar',
					'length' => 40
				],
				'ip' => [
					'type' => 'varchar',
					'length' => 40
				],
				'dateCreate' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP'
				],
				'dateUpdate' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
				],
				'dateLogout' => [
					'type' => 'datetime',
					'default' => 'NULL'
				],
			];

			//Criar
			$this->_createTable($console, $table, $columns);
		}

		/**
		* Cria a tabela de Permissões
		*
		* @return null
		*/
		private function _createTableUserPermissions ($console)
		{
			//Instância da tabela
			$table = Tables::userPermissions();

			//Colunas da tabela
			$columns = [
				'id' => [
					'type' => 'int',
					'key' => 'primary',
					'default' => 'NOT NULL AUTO_INCREMENT'
				],
				'id_user' => [
					'type' => 'int'
				],
				'module' => [
					'type' => 'varchar',
					'length' => 255
				],
				'dateCreate' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP'
				],
				'dateUpdate' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
				]
			];

			//Criar
			$this->_createTable($console, $table, $columns);
		}

		/**
		* Cria um usuário
		*
		* @return null
		*/
		private function _createUser ($console, $user, $password, $userName)
		{
			$userData = [
				'login' => $user,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'name' => $userName
			];

			$result = Tables::user()->insert($userData);

			//Exibir erro se houver
			if($result instanceof Fault)
				return $console->output($result, true);

			//Exibir criação
			$console->output($console->effect('[' . $this->write("USER", "admin") . ']', 'cyan') . " " . $this->write("User '%s' created. Password: %s", "admin", [$userData['login'], $password]));

			//Incluir todos os módulos para o usuário
			$this->_assignModulesPermissions($console, $user);
		}

		/**
		* Atribui ao usuário permissão a todos os módulos
		*
		* @return null
		*/
		private function _assignModulesPermissions ($console, $user)
		{
			//Instanciar uma nova interface
			$interface = new InterfaceCMS();

			//Instanciar módulos e resgatar IDs
			$modules = $this
				->instanceModules($interface)
				->getAllModulesIds();

			//Resgatar ID do usuário
			$user = Tables::user()
				->columns('id')
				->where('login', $user)
				->selectFirst();

			//Inserir cada um dos módulos
			foreach (array_keys($modules) as $module)
			{
				$result = Tables::userPermissions()
					->insert([
						'module' => $module,
						'id_user' => $user['id']
					]);

				//Retornar caso haja erro
				if($result instanceof Fault)
					return $console->output($result, true);
			}

			$console->output($this->write("Permissions added for all modules", "admin"));
		}

	}