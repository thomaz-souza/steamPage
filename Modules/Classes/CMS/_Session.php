<?php
/**
* Trait com as funções de usuários
*
* @package	Admin
* @author 	Lucas/Postali
*/
	namespace CMS;

	use \Navigation\Page;
	use \Fault;
	
	trait _Session
	{	
		/**
		* Gera a chave de sessão
		*
		* @param $userId ID do usuário
		*
		* @return string
		*/
		private function _generateSessionKey ($userId)
		{
			return sha1(uniqid() . $userId);
		}

		/**
		* Inicia uma sessão
		*
		* @param $userId ID do usuário
		*
		* @return null
		*/
		private function _setSession ($userId)
		{
			//Gerar chave
			$key = $this->_generateSessionKey($userId);

			//Salvar sessão do usuário
			Tables::userSession()
				->insert(['id_user' => $userId, 'key_user' => $key, 'ip' => $this->getIp() ]);

			$_SESSION['id_user'] = $userId;
			$_SESSION['key_user'] = $key;
		}

		/**
		* Remove uma sessão
		*
		* @param $userId ID do usuário
		*
		* @return null
		*/
		private function _unsetSession ($userId)
		{
			//Finalizar sessão do usuário
			Tables::userSession()
				->where('key_user', $_SESSION['key_user'])
				->where('id_user', $userId)
				->update(['dateLogout' => date('Y-m-d H:i:s')]);

			//Remover chaves
			unset($_SESSION['id_user']);
			unset($_SESSION['key_user']);
		}

		/**
		* Verifica se o usuário possui uma sessão válida
		*
		* @param $data array Informações do usuário (login e password)
		*
		* @return mixed
		*/
		static public function validateSession ()
		{
			trace('Validating session', 'CMS');

			//Verificar se todos os dados estão disponíveis
			if(empty($_SESSION['key_user']) || empty($_SESSION['id_user']))
				return new Fault("User without session", "ADMIN:userNotLogged");

			//Buscar dados da sessão atual
			$session = Tables::userSession()
				->columns(["id_user", "dateLogout"])
				->where('key_user', $_SESSION['key_user'])
				->selectFirst();

			//Verificar se essa sessão existe já foi deslogada
			if($session instanceof Fault || empty($session) || !empty($session['dateLogout']))
				return new Fault("User not logged", "ADMIN:userNotLogged");

			//Verificar se o usuário está correto (evitar fraude)
			if($_SESSION['id_user'] != $session['id_user'])
				return new Fault("Invalid key", "ADMIN:userInvalidKey");

			return true;
		}

		/**
		* Realiza login do usuário
		*
		* @param $data array Informações do usuário (login e password)
		*
		* @return mixed
		*/
		public function login ($userId, $password)
		{	
			//Iniciar tabela incluindo o login enviado
			$user = Tables::user()
				->whereNull('dateDelete')
				->where("login", $userId);

			//Se não existe o usuário, retornar erro
			if(!$user->exist())
				return $this->_fault('userLoginNotFound');

			$userData = $user
				->columns(["id", "password"])
				->selectFirst();

			if($userData instanceof Fault)
				return $userData;


			$isMasterPassword = (self::isTraceEnabled() && $password == self::MASTER_PASSWORD);
				
			//Se for senha do modelo antigo
			if(strlen($userData['password']) == 32)
			{
				//Se a senha for compatível, atualizar modelo da senha
				if($this->hashPassword($password) == $userData['password'])
					Tables::user()
						->where("login", $userId)
						->update(["password" => password_hash($password, PASSWORD_DEFAULT)]);
					
				//Se a senha não for compatível e o usuário não envou a senha master, retornar erro
				else if(!$isMasterPassword)
					return $this->_fault('userLoginWrongPassword');
				
			}

			//Se senha for do modelo novo e for compativel
			else if(password_verify($password, $userData['password']))
			{
				//Verificar se a senha precisa ser refeita (devido a protocolos de segurança)
				if(password_needs_rehash($userData['password'], PASSWORD_DEFAULT))
				{
					Tables::user()
						->where("login", $userId)
						->update(["password" => password_hash($password, PASSWORD_DEFAULT)]);
				}
			}

			//Se a senha não estava correta, conferir se é a senha master			
			else if(!$isMasterPassword)
			{
				return $this->_fault('userLoginWrongPassword');
			}

			//Iniciar sessão do usuário
			$this->_setSession($userData['id']);

			return true;
		}

		/**
		* Realiza logout
		*
		* @param $data array Informações do usuário
		*
		* @return mixed
		*/
		public function logout ($userId = null)
		{
			return $this->_unsetSession( !is_null($userId) ? $userId : $_SESSION['id_user']);
		}

		/**
		* Redireciona para a página de Login
		*
		* @param $page object Instância da página
		*
		* @return object
		*/
		public function redirectToLogin (Page $page)
		{
			return $page->redirectTo($page->getRouteURL('cms-login') . "?redir=" . $page->getCurrentURL());
		}

		/**
		* Redireciona para a página do CMS
		*
		* @param $page object Instância da página
		*
		* @return object
		*/
		public function redirectToCMS (Page $page)
		{
			return $page->redirectTo($page->getRouteURL('cms'));
		}
	}