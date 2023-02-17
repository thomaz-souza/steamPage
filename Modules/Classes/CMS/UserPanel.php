<?php
/**
* Módulo de usuários no menu do CMS
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMS;

	class UserPanel extends \_Core
	{
		public function sideMenu (InterfaceCMS $interface)
		{
			$options = [
				"icon" => "fas fa-user-alt",
				"title" => $this->write("Users", 'admin'),
				"action" => array("CMS","UserPanel")
			];

			$interface->addSideMenu($options, 'cms-user-panel');
		}
	}