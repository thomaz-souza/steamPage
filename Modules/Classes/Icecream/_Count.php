<?php
/**
* Objeto de funções contagem de dados
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	use \PDO;
	use \Fault;

	trait _Count
	{
		/**
		* Obtém a contagem de resultados
		*
		* @param boolean $takeResults Conta se já houver resultados dessa consulta. Caso false, sempre executa uma nova contagem
		*
		* @return integer
		*/
		private function _count ($takeResults = false)
		{
			//Caso já haja dados, contá-los
			if($this->hasResults() && $takeResults === true)
			{
				//Se for associativo, significa que há apenas um resultado
				if($this->is_associative($this->_result) && !empty($this->_result))
					return 1;

				//Senão, contar os dados
				return count($this->_result);
			}

			//Criar uma nova tabela como subselect
			$query = new Icecream($this);

			//Setar servidor atual
			$query->setServer($this->currentServer);

			//Retornar contagem de todos os dados
			$result = $query
				->columns('COUNT(*) AS q')
				->selectFirst();

			//Verificar se é uma instância de erro
			if($result instanceof Fault)
				return $result;

			//Retornar quantidade de campos
			return intval($result['q']);
		}

		/**
		* Realiza a contagem de dados, desprezando os resultados já existentes
		*
		* @return integer
		*/
		public function count ()
		{
			return $this->_count(false);
		}

		/**
		* Realiza a contagem de dados, considerando os resultados já existentes (se houver)
		*
		* @return integer
		*/
		public function size ()
		{
			return $this->_count(true);
		}

		/**
		* Verifica se um determinado dado existe
		*
		* @return boolean
		*/
		public function exist ()
		{
			$count = $this->_count();

			if($count instanceof Fault)
				return $count;

			return $this->_count() > 0;
		}
	}