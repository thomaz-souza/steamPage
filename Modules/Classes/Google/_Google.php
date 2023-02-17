<?php
/**
* Objeto principal do Google
*
* @package	Google
* @author 	Lucas/Postali
*/	
	
	namespace Google;

	abstract class _Google extends \Core
	{
		/**
		* Codificar em Base64 para URL
		*
		* @param string $data dados
		* @return string
		*/
		protected function _base64url_encode($data)
		{
		  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
		}

		/**
		* Decodificar de Base64 para URL
		*
		* @param string $data dados
		* @return string
		*/
		protected function _base64url_decode($data)
		{
		  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
		}

		/**
		* Assinar a URL
		*
		* @param string $endpoint Endpoint da API
		* @param array $params Parâmetros da URL
		* @param string $keySignature Código de assinatura
		*
		* @return null
		*/
		protected function _signURL ($endpoint, &$params, $keySignature)
		{
			//Criar URL
			$url = $endpoint . '?' . http_build_query($params);

			//Gerar hash
			$signature = hash_hmac('sha1', $url, $this->_base64url_decode($keySignature), true);

			//Atribuir assinatura
			$params['signature'] = $this->_base64url_encode($signature);
		}
	}