<?php
/**
* Objeto principal do Icecream
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \PDO;
	use \Fault;

	class Icecream extends \Databank\Connection
	{
		//Incluir funções de Tabelas
		use _Table;

		//Incluir funções de Seleção
		use _Select;

		//Incluir funções de Condição
		use _Where;

		//Incluir funções de União das Tabelas (Join)
		use _Join;

		//Incluir funções de União das Tabelas (Union)
		use _Union;

		//Incluir funções de Parâmetros
		use _Params;

		//Incluir funções de Inserção e Atualização
		use _Data;

		//Incluir funções de Ordenação
		use _Order;

		//Incluir funções de Limite e Offset
		use _Limit;

		//Incluir funções de Agrupamento
		use _Group;

		//Incluir funções de contagem
		use _Count;

		//Incluir funções de paginação
		use _Paginate;

		//Incluir funções de exclusão
		use _Delete;

		//Incluir funções de Busca
		use _Search;

		//Incluir funções de busca de resultados e execução
		use _Results;

		//Incluir funções de interação com dados
		use _Fetch;

		//Incluir funções de console
		use _Console;


		//Termos identicos
		const SEARCH_EQUAL = "eql";

		//Inicia-se pelo termo
		const SEARCH_START = "str";

		//Finaliza-se pelo termo
		const SEARCH_END = "end";

		//Há termo está em qualquer posição
		const SEARCH_ANY = "any";

		/**
		* Montagem da classe
		*
		* @param string $table Nome da tabela
		* @param string $alias Nome do alias
		*
		* @return null
		*/
		function __construct ($table = null, $alias = null)
		{
			parent::__construct();
			if(!is_null($table))
				$this->table($table);

			if(!is_null($alias))
				$this->table($alias);

			if(isset($GLOBALS['icecream_throw_error']) && $GLOBALS['icecream_throw_error'] === true)
				$this->_throwError = true;
		}

		/**	
		 * Instancia um novo objeto puro
		 * 
		 * @param string $string Recebe o conteúdo puro a ser instanciado
		 * 
		 * @return Raw
		 */
		static public function Raw ($string)
		{
			return new Raw($string);
		}

	}