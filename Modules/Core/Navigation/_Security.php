<?php
/**
* Funções de segurança de navegação
*
* @package	Traits
* @author 	Lucas/Postali
*/
	namespace Navigation;

	trait _Security
	{
		static public function SanitizeURL (&$item)
		{
			//Remover HTML
			$item = preg_replace("/<[^\s]{1}[^>]+>/", "", $item);

			//Remover caracteres especiais
			$item = preg_replace("/[\!\@\#\%\&\*\~]+/", "", $item);

			//Adicionar barras
			$item = addslashes($item);

			return $item;
		}

		static public function SanitizeGetParams ()
		{
			foreach ($_GET as $key => &$value)
			{
				if(gettype($value) != "string")
					continue;

				$value = self::SanitizeURL($value);
			}
		}

	}