<?php
/**
* Classe para exibição de páginas e navegação
*
* @package		Databank
* @author 		Lucas/Postali
*/
	namespace DataBank;

	class CMS extends \Core
	{
		public function sideMenu (\CMS\InterfaceCMS $interface)
		{
			$interface->addSideMenu(array(
				"icon" => "fas fa-database",
				"title" => $this->write("Databank", "admin"),
				"action" => array("Databank","Databank")
			), 'databank');
			
		}
	}

?>