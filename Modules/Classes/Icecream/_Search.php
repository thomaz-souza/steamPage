<?php
/**
* Objeto de funções contagem de dados
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;
	
	use \Fault;

	trait _Search
	{
		/**
		* Realiza uma busca de maneira abrangente
		*
		* @param mixed $columns Colunas nas quais devem ser buscados os termos (string ou array)
		* @param string $term Termo sendo buscado
		* @param array $types Tipos de busca a serem realizadas
		*
		* @return object
		*/
		public function search ($columns, $term, $types = [self::SEARCH_EQUAL, self::SEARCH_START, self::SEARCH_END, self::SEARCH_ANY])
		{
			//Caso não for um array, transformar
			if(!is_array($columns))
				$columns = [$columns];

			//Caso não for um array, transformar
			if(!is_array($types))
				$types = [$types];

			//Termo normalizado
			$normalizedTerm = $this->normalizeString($term);

			$terms = [
				//Termo normal
				'term' => $term,

				//Termo sem normalização dividido
				'implodedTerms' => implode('%', explode(" ", $term)),

				//Termo normalizado
				'normalizedTerm' => $normalizedTerm,

				//Termo normalizado dividido
				'implodedNormalizedTerms' => implode('%', explode(" ", $normalizedTerm))
			];
			
			//Iniciar nova condição
			$condition = new Condition();
			
			foreach ($columns as $column)
			{
				//Buscar strings iguais
				if (in_array(self::SEARCH_EQUAL, $types))
				{
					foreach ($terms as $t)
					{
						$condition->orWhere($column, $t);	
						$this->orderBy("(IF($column = " . $this->_setParam($column, $t) . ", 0, 1))");
					}
				}

				//Buscar por string que se inicie com o termo
				if (in_array(self::SEARCH_START, $types))
				{
					foreach ($terms as $t)
					{
						$condition->orWhereLike($column, $t . '%');	
						$this->orderBy("(IF($column LIKE " . $this->_setParam($column, $t . '%') . ", 0, 1))");
					}
				}

				//Buscar pela palavra em qualquer posição
				if (in_array(self::SEARCH_ANY, $types))
				{
					foreach ($terms as $t)
					{
						$condition->orWhereLike($column, '%' . $t . '%');
						$this->orderBy("(IF($column LIKE " . $this->_setParam($column, '%' . $t . '%') . ", 0, 1))");
					}
				}

				//Buscar por string que termina com o termo
				if (in_array(self::SEARCH_END, $types))
				{
					foreach ($terms as $t)
					{
						$condition->orWhereLike($column, '%' . $t);
						$this->orderBy("(IF($column LIKE " . $this->_setParam($column, '%' . $t) . ", 0, 1))");
					}
				}
			}
			//Incluir condição
			$this->condition($condition);

			return $this;
		}
	}