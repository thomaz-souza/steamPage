<?php
/**
* Propriedades padrões de Cartão
*
* @package	Cielo
* @author 	Lucas/Postali
*/	
	namespace Newsletter;

	use \Icecream\Icecream;
	use \CMS\InterfaceCMS;
	use Datatable\KTDatatable;

	class Newsletter extends \Core
	{
		const TABLE = "newsletter";

		/**
		* Retorna a uma instância da tabela de newsletter
		*		
		* @return \Icecream
		*/
		static protected function table ()
		{
			return new Icecream(self::TABLE);
		}

		/**
		* Retorna a query de consulta padrão para newsletter
		*		
		* @return \Icecream
		*/
		public function queryNewsletter ()
		{
			return self::table()

				->columns([
					'name',
					'email',
					'DATE_FORMAT(date_create, "%d/%m/%Y %H:%i:%s") AS date_create'
				])

				->whereNull('date_delete');
		}

		/**
		* Salva um novo registro na newsletter
		*
		* @param string $email Recebe o e-mail do usuário
		*
		* @return mixed
		*/
		static public function submit ($email, $name = '')
		{
			return self::table()
				->insert(['email' => $email, 'name' => $name]);
		}

		/**
		* Inclui menu de Newsletter no CMS
		*
		* @param InterfaceCMS $interface Interface do CMS
		*
		* @return null
		*/
		public function sideMenu (InterfaceCMS $interface)
		{
			$interface->addSideMenu([
				'icon' => 'far fa-newspaper',
				'title' => 'Newsletter',
				'action' => ['Newsletter', 'List']
			], 'newsletter');
		}

		/**
		* Exporta os dados de newsletter em CSV
		*
		* @param \Navigation\Navigation $navigation Objeto de navegação
		*
		* @return null
		*/
		public function Export ($navigation)
		{
			$navigation->requireAdmin(true);
			
			$data = $this->queryNewsletter()->select();

			if(!empty($data))
				return $this->arrayToCSV($data, ['Nome', 'E-mail', 'Data da inscrição']);
		}

		/**
		* Dados em formato Datatable para o CMS
		*
		* @param \Navigation\Navigation $navigation Objeto de navegação
		*
		* @return null
		*/
		public function Datatable ($navigation)
		{
			$navigation->requireAdmin(true);
			
			$data = $navigation->getData('POST');

			if(!empty($data))
			{
				$datatable = new KTDatatable($data);
				$datatable->setSearch(['email']);
				$datatable->setSource($this->queryNewsletter());
				$navigation->outputJson($datatable->getDatatable());
			}			
		}
	}