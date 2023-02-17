<?php
/**
* Objeto de Model do Icecream
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	abstract class IcecreamModel extends Icecream
	{
		/**
		* Inicializar a classe passando o parâmetro de tabela
		*
		* @return object
		*/
		function __construct ()
		{
			parent::__construct(static::TABLE);
		}

		/**
		* Instanciar a classe em questão para consultas rápidas
		*
		* @return object
		*/
		static public function open ()
		{
			$class = static::class;
			return new $class();
		}
	}