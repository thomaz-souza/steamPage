<?php
/**
* Classe para página de Admin
*
* @package	Core
* @author 	Lucas/Postali
*/
	namespace CMS;
	
	class Forms extends \_Core
	{
		static public function intoOptions ($values)
		{
			$html = '';

			foreach ($values as $id => $value)
			{
				if(is_array($value) && isset($value['id']))
					$html .= "<option value=\"$value[id]\">$value[text]</option>";

				else if(is_array($value))
					$html .= "<option value=\"$value[text]\">$value[text]</option>";

				else 
					$html .= "<option value=\"$value\">$value</option>";				
			}

			return $html;
		}

	}

?>