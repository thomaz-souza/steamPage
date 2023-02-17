<?php
/**
* Função responsável por criar DataTables
*
* @package	Datatable
* @author 	Lucas/Postali
*/
	namespace Datatable;

	use \Navigation\Page;
	use \_Core;
	use \Icecream\Icecream;

	class KTDatatable extends _Core
	{
		function __construct ($params = null)
		{
			parent::__construct();
			if($params)
				$this->setParams($params);
		}

		private $_params;

		/**
		* Insere os parâmetros enviados pelo Datatable
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
		* Resgata um parâmetro, retorna false se não houver
		*
		* @param array $param Recebe o nome do parâmetro
		*
		* @return mixed
		*/

		public function getParam ($param)
		{
			return isset($this->_params[$param]) ? $this->_params[$param] : false;
		}

		/**
		* Obter dados de língua para a Data Table
		*
		* @param string $language Recebe código da língua
		*
		* @return string
		*/
		static public function getDataTableLanguage ($language = null)
		{
			$page = new Page();

			return  [
				'url' => $page->getPublicURL('cms/datatable/lang/' . $page->getLanguageLabel($language) . '.json'),
				'decimal' => $page->getCurrencyDecimalMark($language)
			];
		}

		/**
		* Transforma resultados normais em resultados próprios do datatable
		*
		* @param array $results Recebe resultados a serem reescritos
		*
		* @return array
		*/
		protected function _resultsToDatatable ($results)
		{
			$order = $this->getOrder();
			
			if($results instanceof \Fault)
				return $results;

			$newResults = [
				'data' => $results['results'],
				'meta' => [
					'page' => $results['currentPage'],
					'pages' => $results['totalPages'],
					'perpage' => $results['itemsPerPage'],
					'total' => $results['size'],
					'field' => $order ? $order['field'] : null,
					'sort' => $order ? $order['sort'] : null,
				]
			];

			return $newResults;
		}

		/**
		* Obtem a coluna a ser ordenada
		*
		* @return string
		*/
		public function getOrder ()
		{
			if(!empty($this->_params['sort']) && !empty($this->_params['sort']['field']))
				return $this->_params['sort'];
			
			return false;
		}

		/**
		* Resgatar campo de busca
		*
		* @return string
		*/
		public function getSearch ()
		{
			return isset($this->_params['query'][0]) ? $this->_params['query'][0] : null;
		}

		/**
		* Resgatar campo de fitro
		*
		* @return string
		*/
		public function getFilter ($key = null)
		{
			$filter = isset($this->_params['query']) && !empty($this->_params['query']) ? $this->_params['query'] : [];
			unset($filter[0]);

			if($key === null)
				return $filter;
			else
				return isset($filter[$key]) ? $filter[$key] : null;
		}

		/**
		* Resgatar itens por página
		*
		* @return string
		*/
		public function getPerPage ()
		{
			return !empty($this->_params['pagination']['perpage']) ? intval($this->_params['pagination']['perpage']) : 10;
		}

		/**
		* Resgatar página atual
		*
		* @return int
		*/
		public function getPage ()
		{
			return !empty($this->_params['pagination']['perpage']) ? intval($this->_params['pagination']['page']) : 1;
		}

		private $_searchIn = [];
		private $_searchType = [];

		/**
		* Define as informações que devem ser buscados os dados solicitados na busca
		*
		* @param mixed $columns Recebe as colunas
		*
		* @return object
		*/
		public function setSearch ($columns, $type = [Icecream::SEARCH_EQUAL, Icecream::SEARCH_START, Icecream::SEARCH_END, Icecream::SEARCH_ANY])
		{
			//Transformar em Array caso não seja
			if(is_array(!$columns))
				$columns = [$columns];

			$this->_searchIn = $columns;
			$this->_searchType = $type;
			return $this;
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
		 * @var bool Filtro automático
		 */
		public $_autoFilter = false;

		/**
		 * Habilita, desabilita ou retorna o filtro automático
		 * 
		 * @return bool
		 */
		public function autoFilter ($situation = null)
		{
			if($situation === false || $situation === true)
				$this->_autoFilter = $situation;

			return $this->_autoFilter;
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
			if(!empty($this->getSearch()) && !empty($this->_searchIn))
				$source->search($this->_searchIn, $this->getSearch(), $this->_searchType);

			//Incluir filtros, se houver
			if($this->autoFilter() === true)
			{
				foreach ($this->getFilter() as $column => $filter)
					$source->whereIn($column, $filter);
			}
			
			//Incluir ordem, se houver
			$order = $this->getOrder();
			if($order)
				$source->orderBy($order['field'], $order['sort']);


			//Resgatar resultados paginados
			$results = $source->paginate($this->getPage(), $this->getPerPage());
			
			$this->setResults($results);

			//Retornar erro, se houver
			if($results instanceof \Fault)
				return $results;
			
			return $this;
		}

		/**
		* Retorna a tabela de dados
		*
		* @param $results mixed (Opcional) Recebe os resultados
		*
		* @return object
		*/
		public function getDatatable ($results = null)
		{
			//Caso o resultado tenha sido passado
			if(!is_null($results))

				//Verificar se é uma instância  de banco de dados
				if($results instanceof Icecream)
					$this->setSource($results);

				//Senão, salvar como dados
				else
					$this->setResults($results);

			if($this->_results instanceof Fault)
				return $this->_results;

			//Retornar resultados
			return $this->_resultsToDatatable($this->_results);
		}
	}

?>