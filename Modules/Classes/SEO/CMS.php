<?php
/**
* Inclusão do painel de SEO
*
* @package	SEO
* @author 	Lucas/Postali
*/	
	namespace SEO;

	use \CMS\InterfaceCMS;

	class CMS extends \Core
	{
		public function sideMenu (InterfaceCMS $interface)
		{
			$interface->addSideMenu([
				"icon" => 'fas fa-search-location',
				"title" => "SEO",
				"child" => [
					"meta-tags" => [
						"icon" => "fas fa-chalkboard",
						"title" => $this->write('Pages', 'seo'),
						"action" => ["SEO", "Pages"]
					]/*,
					"robots" => [
						"icon" => "fas fa-robot",
						"title" => $this->write('Robots', 'seo'),
						"action" => ["SEO", "Robots"]
					]*/
				]
			], 'seo');
		}
	}