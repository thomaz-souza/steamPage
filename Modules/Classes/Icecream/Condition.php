<?php
/**
* Objeto principal do Icecream
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	class Condition extends \Databank\Connection
	{
		//Incluir funções de Where
		use _Where;

		//Incluir funções de Parâmetro
		use _Params;

		/**
		* Alias para a função _assembleWhere
		*
		* @return object
		*/
		public function assembleWhere ()
		{
			return $this->_assembleWhere(false, false);
		}

		/**
		* Instanciar a classe em questão para consultas rápidas
		*
		* @return object
		*/
		static public function new ()
		{
			$class = static::class;
			return new $class();
		}

		/**
		* Função fantasma para satisfazer ao necessário do where
		*
		* @return object
		*/
		public function eraseResults () {}
	}