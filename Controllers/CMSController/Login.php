<?php
/**
* Métodos do Login do CMS
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMSController;

	use \CMS\CMS;
	use \CMS\InterfaceCMS;
	use \Navigation\Page;
	use \Navigation\JsonTransaction;
	use \Google\ReCaptcha;

	class Login extends CMS
	{
		public function __construct (Page $page = null)
		{
			parent::__construct();
			if($page) $this->pageStart($page);
		}

		protected function pageStart ($page)
		{
			$this->checkInstallation();
			
			//Se o usuário está logado redirecioná-lo para a página PRINCIPAL
			if(self::validateSession() === true)
				$this->redirectToCMS($page);

			$this->_pageStart($page);
		}

		public $doLogin_map = [
			'login' => [
				'mandatory' => true,
				'type' => 'string'
			],
			'password' => [
				'mandatory' => true,
				'type' => 'string'
			],
			'g-recaptcha-response' => [
				'reCaptcha' => true
			]
		];

		public function doLogin ($data, JsonTransaction $transaction)
		{
			$captcha = new ReCaptcha();

			//Incluir recaptcha se estiver disponível e se o usuário não estiver em modo de depuração
			if(!self::isTraceEnabled() && $captcha->isAvailable())
				$this->doLogin_map['g-recaptcha-response']['mandatory'] = true;

			$transaction->validateRequest($this->doLogin_map, $data);

			//Executar login
			return $this->login($data['login'], $data['password']);
		}
		
	}