<?php
/**
* Funções de paginação
*
* @package	Icecream
* @author 	Lucas/Postali
*/	
	namespace Icecream;

	trait _Paginate
	{
		/**
		* Realiza a paginação dos resultados
		*
		* @param integer $page Número da página atual
		* @param integer $perPage Número de resultados por página
		* @param boolean $select Retorna os resultados
		* @param boolean $onlyPaginate Se true apenas realiza a paginação, sem retornar os dados relacionados
		*
		* @return mixed
		*/
		public function paginate ($page, $perPage, $select = true, $onlyPaginate = false)
		{
			//Zerar limite e offset
			$this->limit(null);
			$this->offset(null);

			//Tamanho dos resultados
			$size = $this->count();

			if($size instanceof \Fault)
				return $size;

			//Definir o limite de resultados como sendo o perPage
			$this->limit($perPage);

			//Definir o deslocamento de resultados
			$offset = $perPage * ($page - 1);
			$this->offset($offset);

			//Se foi solicitado apenas a paginação, sem retornar dados, retornar o objeto
			if($onlyPaginate === true)
				return $this;

			if($select === true)
			{
				$results = $this->select();
				$total = $this->size();
			}
			else
			{
				//Total de resultados
				$total = $this->count();
			}

			//Início dos resultados
			$resultsFrom = ($total == 0) ? 0 : ($offset + 1);

			//Final dos resultados
			$resultsTo = ($total == 0) ? 0 : ($offset + $perPage);
			$resultsTo = ($resultsTo > $size) ? $size : $resultsTo;

			return [
				'results' 		=> isset($results) ? $results : null, //Resultados
				'size'			=> $size, 					//Total de itens gerais
				'totalResults'	=> $total,					//Total de itens nesta consulta
				'currentPage' 	=> $page,					//Número da página atual
				'totalPages'	=> ceil($size/$perPage),	//Quantidade de páginas
				'itemsPerPage' 	=> $perPage,				//Itens por página
				'resultsFrom'	=> $resultsFrom,			//Início dos resultados
				'resultsTo'		=> $resultsTo				//Final dos resultados
			];
		}
	}