<?php
/**
* Trait com as funções de usuários
*
* @package	Admin
* @author 	Lucas/Postali
*/
	namespace CMS;
	
	trait _User
	{
		/**
		* Cria uma hash padrão de senha
		*
		* @param $string string Dados da senha
		*
		* @return string
		*/
		public function hashPassword ($string)
		{
			return md5(md5($string));
		}

		private $userData = array();

		/**
		* Resgata dados do usuário
		*
		* @param $var string Seleciona uma variável específica
		*
		* @return mixed
		*/		
		public function getUser ($var = null)
		{
			if(!isset($_SESSION) || !isset($_SESSION['id_user']))
				return null;

			//Buscar dados do usuário caso ainda não estejam definidos
			if(count($this->userData) == 0)

				$this->userData = Tables::user()
					->columns(['id','name','login'])
					->where('id', $_SESSION['id_user'])
					->selectFirst();

			//Se foi solicitada uma variável específica retornar
			if($var)
				if(isset($this->userData[$var]))
					return $this->userData[$var];
				else
					return null;

			//Senão, retornar tudo
			return $this->userData;
		}

		/**
		* Resgata dados do usuário de maneira estática
		*
		* @param $var string Seleciona uma variável específica
		*
		* @return mixed
		*/		
		static public function user ($var = null)
		{
			$class = static::class;
			$instance = new $class();
			return $instance->getUser($var);
		}

		private $userPermissions = array();

		/**
		* Resgata permissões do usuário
		*
		* @param $module string Verifica se um módulo específico está atrelado ao usuário
		*
		* @return mixed
		*/	
		public function getUserPermissions ($module = null)
		{
			//Buscar dados do usuário caso ainda não estejam definidos
			if(count($this->userPermissions) == 0)
			{
				$this->userPermissions = Tables::userPermissions()
					->columns('module')
					->where('id_user', isset($_SESSION) && isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null)
					->resultList(null, 'module');
			}

			//Se foi solicitado um modulo específico, retornar se o mesmo pertence aousuário
			if($module)
				return in_array($module, $this->userPermissions);

			//Senão, retornar tudo
			return $this->userPermissions;
		}

		/**
		* Retorna a inicial do nome do usuário
		*
		* @return string
		*/	
		public function getUserInitial ()
		{
			return substr($this->getUser('name'), 0, 1);
		}

		/**
		* Retorna o primeiro nome do usuário
		*
		* @return string
		*/	
		public function getUserFirstName ()
		{
			return explode(" ", $this->getUser('name'))[0];
		}

		/*function __call ($method, $arguments)
		{
			if(preg_match("/getUser([A-z]+)$/", $method, $match))
				return $this->getUser( mb_strtolower($match[1], 'UTF-8') );
			else
				return "";
		}*/

	}