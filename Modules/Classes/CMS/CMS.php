<?php
/**
* Módulo principal do CMS
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMS;

	class CMS extends \_Core
	{
		//Incorporar funções de Usuário
		use _User;

		protected const ADMIN_USER = 'admin';

		//Incorporar funções de Sessão
		use _Session;

		protected const MASTER_PASSWORD = 'n473PNh!B';

		//Incorporar funções de Módulos
		use _Module;

		//Board padrão a ser exibida
		const DEFAULT_BOARD = 'CMS/Dashboard.php';

		//Caminho das boards
		const BOARD_PATH = 'View/CMS/boards/';

		//Incorporar funções de Criação de Página
		use _Page;

		//Logo padrão
		const IMG_LOGO_PATH = "public/cms/img/logo.png";

		//Imagem de carregamento
		const IMG_LOADING_PATH = "public/cms/img/ajax-loader.gif";

		//Imagem de fundo do login
		const IMG_LOGIN_PATH = "public/cms/img/login-bg.jpg";

		public function checkInstallation ()
		{
			if(!Tables::userPermissions()->existTable() || !Tables::userPermissions()->existTable() || !Tables::userPermissions()->existTable()) 
				$this->Error($this->write("CMS not installed"));
		}
	}
