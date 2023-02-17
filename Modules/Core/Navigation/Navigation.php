<?php
/**
* Funções úteis de navegação
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;
	
	class Navigation extends \Core
	{
		use _Security;
		
		use _Request;

		use _Output;

		use Routes;

		//Funções de exportação
		use \ExportTrait;

		function __construct ()
		{
			//Definir zona do tempo padrão
			trace('Setting Navigation default timezone', 'Navigation\Navigation', $this->setSystemTimezone());

			//Definir a língua padrão do sistema
			trace('Setting Navigation default language', 'Navigation\Navigation', $this->setSystemLanguage());

			self::SanitizeGetParams();
			
			//Carregar as configurações de navegação e verificar se há força de URL
			if(isset($this->_navigationData()['force']))
				$this->_forceConnection($this->_navigationData()['force']);	

			//Iniciar sessão
			if (session_id() == '' || session_status() == PHP_SESSION_NONE)
				session_start();
		}

		/**
		 * Gera um novo token de transação
		 * 
		 * @return string
		 */
		public function transactionToken ()
		{
			//Gerar token
			$token = md5($this->getIP());

			//Atribuir se não existir
			if(!isset($_SESSION['transaction_token']))
				$_SESSION['transaction_token'] = $token;

			//Retornar o token
			return $_SESSION['transaction_token'];
		}

		/**
		 * Resgata o token de transação gerado
		 * 
		 * @return string
		 */
		public function getTransactionToken ()
		{
			//Retornar FALSE se não existir
			if(!isset($_SESSION['transaction_token']))
				return false;

			//Retornar o token
			return $_SESSION['transaction_token'];
		}
		
	}