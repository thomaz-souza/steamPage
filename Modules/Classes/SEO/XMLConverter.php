<?php
/**
* Conversor de XML
*
* @package	SEO
* @author 	Lucas/Postali
*/

namespace SEO;

Trait XMLConverter
{
	/**
	* Analisar parâmetors de XML
	*
	* @param $xml resource Recebe o recurso de XML
	* @param $array array Array de dados
	*
	* @return resource
	**/
	static public function _parseArrayXML ($xml, $array)
	{
		//Verificar se há parâmetros declarados
		$params = isset($array['_params']) ? $array['_params'] : null;

		//Para cada posição do array
		foreach($array as $key => $value)
		{
			//Se a chave em questão for _params, ignorar
			if($key === "_params")
				continue;

			//Se a chave for um inteiro, se trata de um array numérico. O bloco é reprocessado e o restante da função é ignorada
			if(is_integer($key))
			{
				$xml = self::_parseArrayXML($xml, $value);
				continue;
			}
			//Iniciar elemento
			xmlwriter_start_element($xml, $key);

			//Se houver parâmetros
			if($params)
			{	
				//Para cada parâmetro
				foreach ($params as $paramKey => $param)
				{	
					//Se houver um atributo, escrevê-lo
					if($paramKey === "attribute")
					{
						foreach ($param as $paramName => $paramValue)
							xmlwriter_write_attribute($xml, $paramName, $paramValue);						
					}

					//Se houver um atributo com namespace, escrevê-lo
					if($paramKey === "attribute_ns")
					{
						foreach ($param as $paramName => $paramValue)
						{
							xmlwriter_write_attribute_ns($xml, $paramValue['prefix'], $paramName, $paramValue['uri'], $paramValue['value']);
						}
					}
				}
			}
			
			//Se o valor em questão for um Array, o bloco é reprocessado
			if(is_array($value))
				$xml = self::_parseArrayXML($xml, $value);

			//Caso haja exigência de cdata
			else if($params && isset($params['cdata']) && $params['cdata'] === true)
				xmlwriter_write_cdata($xml, $value);

			//Senão, escrever texto normalmente
			else
				xmlwriter_text($xml, $value);
				

			//Finalizar elemento
			xmlwriter_end_element($xml);
		}

		return $xml;
	}

	/**
	* Converter Array para XML 
	*
	* @param $array array Array de dados
	*
	* @return string
	**/
	static public function arrayToXMLConverter ($array, $ident = true)
	{
		//Iniciar XML
		$xml = xmlwriter_open_memory();

		//Permitir identação (pular linhas)
		xmlwriter_set_indent($xml, $ident);

		//Iniciar documento com versão e charset
		xmlwriter_start_document($xml, '1.0', 'UTF-8');	

		//Parsear dados enviados
		$xml = self::_parseArrayXML($xml, $array);

		//Finalizar e retornar
		xmlwriter_end_document($xml);
		return xmlwriter_output_memory($xml);
	}
}