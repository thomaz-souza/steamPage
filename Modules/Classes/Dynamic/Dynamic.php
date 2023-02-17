<?php
/**
* Função responsável por criar dados dinâmicos
*
* @package	Dynamic
* @author 	Lucas/Postali
*/
	namespace Dynamic;

	use \_Core;
	use \Icecream\Icecream;

	class Dynamic extends _Core
	{
		function __construct ($params = null)
		{
			parent::__construct();
			if($params)
				$this->setParams($params);
		}

		/**
		* Insere na página
		*
		* @return null
		*/
		static public function addTag(\Navigation\Page $page)
		{
			$page->addAsset('dynamic');
			//$page->addTagHeadJS('assets/dynamic/paginate.min.js');
			//$page->addTagCSS('assets/dynamic/paginate.min.css');
		}

		private $_params = [];

		/**
		* Insere os parâmetros enviados pelo Dynamic
		*
		* @param array $params Recebe resultados a serem reescritos
		*
		* @return array
		*/
		public function setParams ($params)
		{
			$this->_params = $params;
		}

		/**
		* Obtem a coluna a ser ordenada
		*
		* @return string
		*/
		public function getOrder ()
		{
			if(isset($this->_params['order']))
				return $this->_params['order'];

			return false;
		}

		/**
		* Resgatar campo de busca
		*
		* @return string
		*/
		public function getSearch ()
		{
			return isset($this->_params['search']) && !empty($this->_params['search']) ? $this->_params['search'] : false;
		}

		/**
		* Resgatar itens por página
		*
		* @return string
		*/
		public function getPerPage ()
		{
			return isset($this->_params['prp']) ? intval($this->_params['prp']) : false;
		}

		/**
		* Resgatar página atual
		*
		* @return int
		*/
		public function getPage ()
		{
			return isset($this->_params['pag']) ? intval($this->_params['pag']) : false;
		}

		/**
		* Resgatar filtros
		*
		* @param string $filter (Opcional) Chave do filtro a ser recuperado
		*
		* @return mixed
		*/
		public function getFilters ($filter = null)
		{
			$filters = [];
			foreach ($this->_params as $key => $value)
				if(!in_array($key, ['pag', 'prp']))
					$filters[$key] = $value;

			if($filter)
				if(isset($filters[$filter]))
					return $filters[$filter];
				else
					return false;
			
			return $filters;
		}

		private $_results;

		/**
		* Inclui os resultados paginados
		*
		* @param $results mixed Recebe os resultados paginados
		*
		* @return object
		*/
		public function setResults ($results)
		{
			$this->_results = $results;
			return $this;
		}

		/**
		* Realiza a pesquisa através de uma fonte de dados do Icecream
		*
		* @param $source object Recebe a instância do Icecream
		*
		* @return object
		*/
		public function setSource (Icecream $source)
		{
			//Incluir busca, se hovuer
			if($this->getSearch())
				 $source->search($this->getSearch()[0], $this->getSearch()[1]);

			//Incluir ordem, se houver
			if($this->getOrder())
				$source->orderBy($this->getOrder()[0], $this->getOrder()[1]);

			//Resgatar resultados paginados
			if($this->getPage() && $this->getPerPage())
				$results = $source->paginate($this->getPage(), $this->getPerPage());
			else
				$results = $source->select();

			//Retornar erro, se houver
			if($results instanceof \Fault)
				return $results;

			$this->setResults($results);
			return $this;
		}

		/**
		* Retorna os dados dinâmicos
		*
		* @param $results mixed (Opcional) Recebe os resultados
		*
		* @return object
		*/
		public function getDynamic ($results = null)
		{
			//Caso o resultado tenha sido passado
			if(!is_null($results))
			
				//Verificar se é uma instância  de banco de dados
				if($results instanceof Icecream)
				{
					$query = $this->setSource($results);
					//Retornar erro, se houver
					if($query instanceof \Fault)
						return $query;
				}

				//Senão, salvar como dados
				else
					$this->setResults($results);
			

			//Retornar resultados
			return $this->_results;
		}
	}

?>