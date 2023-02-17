<?php
/**
* Objecto de funções union
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Union
	{
		private $_union = array();

		/**
		* Monta a cláusula de union
		*
		* @return string
		*/
		protected function _assembleUnion ()
		{
			return implode(" ", $this->_union);
		}

		/**
		 * Criar a união
		 * 
		 * @param  string $type  Tipo da união (UNION|UNION ALL)
		 * @param  string|Icecram $table Tabela Icecream ou string
		 * 
		 * @return null
		 */
		protected function _createUnion ($type, $table)
		{
			//Caso a lista seja uma instância de banco de dados
			if($table instanceof Icecream)
			{
				//Incluir parâmetros da instância passada e incluir a string da query
				$this->_mergeParam($table->getParam());
				$table = $table->selectQueryString();				
			}	

			$this->_union[] = $type . " " . $table;
		}

		/**
		* Cria uma union
		*
		* @param string|Icecram $table Tabela Icecream ou string
		*
		* @return object
		*/
		public function union ($table)
		{
			$this->_createUnion('UNION', $table);
			return $this;
		}

		/**
		* Cria uma union all
		*
		* @param string|Icecram $table Tabela Icecream ou string
		*
		* @return object
		*/
		public function unionAll ($table)
		{
			$this->_createUnion('UNION ALL', $table);
			return $this;
		}
	}