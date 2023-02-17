<?php
/**
* Objeto de funções para alteração interativa de dados
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \Fault;

	trait _Fetch
	{
		private $_columns = [];

		/**
		* Incluir coluna
		*
		* @param string $name Nome da coluna
		* @param mixed $value Valor do campo
		*
		* @return null
		*/
		private function _setColumn ($name, $value = null)
		{
			$this->_columns[$name] = $value;
			$this->$name = $value;
		}

		/**
		* Zerar colunas
		*
		* @return null
		*/
		private function _removeColumns ()
		{
			foreach ($this->getColumns() as $col)
				if(isset($this->$col))
					unset($this->$col);
			
			$this->_columns = [];
		}


		/**
		* Resgatar colunas
		*
		* @return null
		*/
		public function getColumns ()
		{
			return $this->_columns;
		}

		/**
		* @param string Nome da coluna chave (ID)
		*/
		private $_key = null;

		/**
		* @param string Valor da chave (ID)
		*/
		private $_keyValue = null;

		/**
		* Resgatar colunas de acordo com coluna chave e valor
		*
		* @param string $key Nome do campo chave
		* @param string $value (Opcional) Valor da chave (ID)
		*
		* @return Icecream
		*/
		public function fetch ($key, $value = null)
		{
			$this->_key = $key;
			$this->_keyValue = $value;
			$this->_fetch();

			return $this;
		}

		/**
		* Realiza o resgate de informações
		*
		* @return null
		*/
		private function _fetch ()
		{
			$this->where($this->_key, $this->_keyValue);

			//Impedir que os parâmetros sejam excluídos
			$this->_allowEraseParams(false);

			//Buscar resultados
			$results = $this->_keyValue === null ? [] : $this->selectFirst();

			//Verificar se houve erro
			if($results instanceof Fault)
				return $results;

			//Zerar colunas
			$this->_removeColumns();

			//Se não há resultado, buscar colunas
			if(empty($results))
			{
				//Impedir que os parâmetros sejam excluídos
				$this->_allowEraseParams(false);
				$columns = $this->getTableColumns();

				//Verificar se houve erro
				if($columns instanceof Fault)
					return $columns;

				foreach ($columns as $col)
					$this->_setColumn($col['Field']);
			}
			
			//Se há resultado, atribuí-los às colunas
			else
			{
				foreach ($results as $col => $val)
					$this->_setColumn($col, $val);		
			}			
		}

		/**
		* Resgata todos os valores atuais
		*
		* @return null
		*/
		public function getValues ()
		{
			$values = [];

			foreach ($this->getColumns() as $col => $value)
				$values[$col] = isset($this->$col) ? $this->$col : null;	

			return $values;
		}

		/**
		* Resgata apenas valores que foram alterados
		*
		* @return null
		*/
		public function getChangedValues ()
		{
			$values = [];

			foreach ($this->getColumns() as $col => $value)
			{
				if((isset($this->$col) || $this->$col === null) && $this->$col !== $value)				
					$values[$col] = $this->$col;				
			}

			return $values;
		}

		/**
		* Salvar dados
		*
		* @return mixed
		*/
		public function save ()
		{			
			//Resgatar valores que foram alterados
			$values = $this->getChangedValues();

			//Verificar se há valores alterados
			if(count($values) == 0)
				return false;

			//Permitir alterar apenas um resultado
			$this->limit(1);
			
			//Criar vínculo
			$this->where($this->_key, $this->_keyValue);

			//Executar alteração
			$result = $this->place($this->getChangedValues());		


			//Se o valor da chave era nulo, usar o valor gerado
			if(is_null($this->_keyValue) ||
				( is_null($this->getColumns()[$this->_key]) &&
					$this->getColumns()[$this->_key] == $this->getValues()[$this->_key]
				)
			)
			{
				$this->_keyValue = $result;
			}
		
			//Zerar todos os parâmetros
			$this->_eraseParam();

			//Zerar todos os parâmetros
			$this->_eraseConditions();

			//Buscar novamente o item
			$this->_fetch();
		
			return $result;
		}
	}