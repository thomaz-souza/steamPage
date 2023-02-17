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

	class Datatable extends _Core
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
			//Alterar chave de dados
			$results['data'] = $results['results'];
			unset($results['results']);

			//Incluir filtros esperados pela tabela
			$results['recordsFiltered'] = $results['size'];
			$results['recordsTotal'] = $results['totalResults'];

			unset($results['currentPage']);
			unset($results['size']);
			unset($results['totalResults']);

			return $results;
		}

		/**
		* Obtem a coluna a ser ordenada
		*
		* @return string
		*/
		public function getOrder ()
		{
			$orders = [];
			foreach ($this->_params['order'] as $order)			
				$orders[] = $this->_params['columns'][$order['column']]['data'] . (isset($order['dir']) ? " " .$order['dir'] : '');
			return $orders;
		}

		/**
		* Resgatar campo de busca
		*
		* @return string
		*/
		public function getSearch ()
		{
			return isset($this->_params['search']) && isset($this->_params['search']['value']) ? $this->_params['search']['value'] : '';
		}

		/**
		* Resgatar itens por página
		*
		* @return string
		*/
		public function getPerPage ()
		{
			return intval($this->_params['length']);
		}

		/**
		* Resgatar página atual
		*
		* @return int
		*/
		public function getPage ()
		{
			if(!isset($this->_params['start']) || $this->_params['start'] == 0)
				return 1;

			return intval($this->_params['start'] / $this->_params['length']) + 1;
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
		* Realiza a pesquisa através de uma fonte de dados do Icecream
		*
		* @param $source object Recebe a instância do Icecream
		*
		* @return object
		*/
		public function setSource (Icecream $source)
		{
			//Incluir busca, se hovuer
			if($this->getSearch() != "")
				 $source->search($this->_searchIn, $this->getSearch(), $this->_searchType);

			//Incluir ordem, se houver
			if($this->getOrder() != "")
				foreach ($this->getOrder() as $order)
				 	$source->orderBy($order);

			//Resgatar resultados paginados
			$results = $source->paginate($this->getPage(), $this->getPerPage());

			//Retornar erro, se houver
			if($results instanceof \Fault)
				return $results;

			$this->setResults($results);
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

			//Retornar resultados
			return $this->_resultsToDatatable($this->_results);
		}
	}

?>