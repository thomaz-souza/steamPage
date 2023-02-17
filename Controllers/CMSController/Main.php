<?php
/**
* Métodos do CMS
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMSController;

	use \CMS\CMS;
	use \CMS\InterfaceCMS;
	use \Navigation\Page;
	use \Navigation\JsonTransaction;

	class Main extends CMS
	{
		public function __construct (Page $page = null)
		{
			parent::__construct();
			if($page) $this->pageStart($page);
		}

		protected function pageStart ($page)
		{
			//Verificar se o usuário está logado, senão, redirecioná-lo para a página de LOGIN
			if(self::validateSession() !== true)
				$this->redirectToLogin($page);

			$this->_pageStart($page);

			//Iniciar nova interface
			$this->Interface = new InterfaceCMS;

			//Incluir todos os módulos na interface
			$this->instanceModules($this->Interface);
		}

		public function doLogout ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdmin();

			//Executar logout
			return $this->logout();
		}
		
	}
