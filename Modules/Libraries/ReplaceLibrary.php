<?php
/**
* Biblioteca de Replaces
*
* @package	Library
* @author 	Lucas/Postali
*/

	class ReplaceLibrary extends _Core
	{
		/**
		* Formata a string para apenas números 0-9
		*
		* @param string $string Recebe a string a ser formatada
		* @return string
		*/
		static public function onlyNumbers ($string)
		{
			return preg_replace("/[^\d]+/", "", $string);
		}

		/**
		* Formata a string para apenas valores: números de 0 a 9, pontos (.) e vírgulas (,)
		*
		* @param string $string Recebe a string a ser formatada
		* @return string
		*/
		static public function onlyValue ($string)
		{
			return preg_replace("/[^\d\.\,]+/", "", $string);
		}

		/**
		* Formata a string para nome de arquivo e URL, tornando tudo minúsculo, sem espaços e sem caracteres
		*
		* @param string $string Recebe a string a ser formatada
		* @return string
		*/
		static public function fileWebSafe ($string)
		{
			//Tornar letras minúsculas
			$string = mb_strtolower($string, 'UTF-8');

			//Trocar espaços por underline (_)
			$string = preg_replace("/[\s]+/", "_", $string);

			//Retirar tudo o que for diferente de letras (a-z), números (0-9), ponto (.), traço (-) e underline (_)
			$string = preg_replace("/[^0-9a-z-_\.]+/", "", $string);
			return $string;
		}

		/**
		* Formata a string retirando os espaços
		*
		* @param string $string Recebe a string a ser formatada
		* @return string
		*/
		static public function noSpaces ($string)
		{
			return preg_replace("/[\s]+/", "", $string);
		}

		/**
		* Formata a string retirando os espaços
		*
		* @param string $string Recebe a string a ser formatada
		* @return string
		*/
		static public function brazilDate ($string)
		{
			return DateTime::createFromFormat('d/m/Y H:i', $string)->format('Y-m-d H:i:s');
		}

		/**
		* Retirar tags HTML
		*
		* @param string $string Recebe a string a ser formatada
		* @return string
		*/
		static public function removeHTML ($string)
		{
			$string = preg_replace("/\<[^\>]+\>[^\<]+\<\\/[^\>]+\>/", "", $string);
			$string = preg_replace("/\<[^\>]+\>/", "", $string);
			$string = preg_replace("/\>/", "&gt;", $string);
			$string = preg_replace("/\</", "&lt;", $string);

			return $string;
		}

		/**
		* Transformar em senha
		*
		* @param string $string Recebe a string a ser formatada
		* @return string
		*/
		static public function userPassword ($string)
		{
			return password_hash($string, PASSWORD_DEFAULT);
		}

		/**
		* Transforma em Array valores divididos por vírugla
		*
		* @param string $string Recebe a string a ser formatada
		*
		* @return string
		*/
		static public function explodeComma ($string)
		{
			return explode(",", $string);
		}

	}

?>