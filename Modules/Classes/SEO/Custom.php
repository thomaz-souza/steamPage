<?php
/**
* Módulo independente de SEO para conteúdos dinâmicos
*
* @package	SEO
* @author 	Lucas/Postali
*/
	namespace SEO;

	use \Navigation\Page;
	use \Icecream\Icecream;

	class Custom extends \Core
	{
		use \FileTrait;
		use \ImageTrait;

		/**
		 * Instala as tabelas de SEO no banco de dados
		 * 
		 * @param  \Console\Console $console
		 * 
		 * @return null
		 */
		public function install (\Console\Console $console)
		{
			//Instância da tabela
			$table = self::table();

			//Colunas da tabela
			$columns = [
				'id' => [
					'type' => 'int',
					'key' => 'primary',
					'default' => 'NOT NULL AUTO_INCREMENT'
				],

				'scope' => [
					'type' => 'VARCHAR(100)',
					'key' => 'index',
					'default' => 'NULL'
				],
				
				'id_source' => [
					'type' => 'VARCHAR(11)',
					'key' => 'index',
					'default' => 'NULL'
				],
				'language' => [
					'type' => 'VARCHAR(10)',
					'key' => 'index',
					'default' => 'NULL'
				],
				

				'pageTitle' => [
					'type' => 'VARCHAR(500)',
					'default' => 'NULL'
				],
				'metaTitle' => [
					'type' => 'VARCHAR(500)',
					'default' => 'NULL'
				],
				'metaDescription' => [
					'type' => 'VARCHAR(1000)',
					'default' => 'NULL'
				],
				'metaKeywords' => [
					'type' => 'VARCHAR(500)',
					'default' => 'NULL'
				],
				'ogTitle' => [
					'type' => 'VARCHAR(500)',
					'default' => 'NULL'
				],
				'ogImage' => [
					'type' => 'VARCHAR(255)',
					'default' => 'NULL'
				],

				'ogImageWidth' => [
					'type' => 'INT(6)',
					'default' => 'NULL'
				],
				'ogImageHeight' => [
					'type' => 'INT(6)',
					'default' => 'NULL'
				],

				'ogDescription' => [
					'type' => 'VARCHAR(1000)',
					'default' => 'NULL'
				],

				'date_create' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP'
				],
				'date_update' => [
					'type' => 'datetime',
					'default' => 'NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
				],
				'date_delete' => [
					'type' => 'datetime',
					'default' => 'NULL'
				]
			];

			//Verifica se uma tabela existe
			if($table->existTable())
			{
				//Se pediu para limpar tabela, excluir
				if($console->getArgument('clean'))
					$table->drop();

				//Senão, avisar que a tabela já existe
				else
					return $console->output($this->write("Table '%s' already exists. Skipping.", "admin", $table->getTable()));
			}

			//Criar tabela
			$result = $table->create($columns);

			if($result instanceof \Fault)
				return $result;

			$console->output("Done!");
		}

		/**
		 * Tabela de SEO
		 * 
		 * @return Icecream
		 */
		static protected function table ()
		{
			return new Icecream('seo_custom');
		}

		/**
		 * Resgata a língua a ser gravada
		 * 
		 * @param  string $language Língua solicitada (ou null para retornar língua padrão)
		 * 
		 * @return string           
		 */
		static protected function _getLanguage ($language = null)
		{
			if(is_string($language))
				return $language;

			$core = new \Core();
			return $core->currentLanguage();
		}

		/**
		 * Salva um novo resgitro de SEO
		 * 
		 * @param  string 		$scope 		Escopo
		 * @param  string|int	$id_source  Identificação do conteúdo
		 * @param  array 		$data  		Dados a serem salvos
		 * @param  string  		$language 	Idioma. Caso esteja como null, considera o idioma atual
		 * 
		 * @return int|string
		 */
		static public function save ($scope, $id_source, $data, $language = null)
		{
			//Campos a serem salvos
			$map = [
				'pageTitle',
				'metaTitle',
				'metaDescription',
				'metaKeywords',
				'ogTitle',
				'ogImage',
				'ogImageWidth',
				'ogImageHeight',
				'ogDescription'
			];

			foreach ($data as $key => $value)
			{
				if(!in_array($key, $map))
				{
					unset($data[$key]);
					continue;
				}

				if(empty($value))
					$data[$key] = null;
			}

			//Normalizar título para OG
			if(empty($data['ogTitle']) && !empty($data['metaTitle']))
				$data['ogTitle'] = $data['metaTitle'];

			//Normalizar descrição para OG
			if(empty($data['ogDescription']) && !empty($data['metaDescription']))
				$data['ogDescription'] = $data['metaDescription'];

			$data['scope'] = $scope;
			$data['id_source'] = $id_source;
			$data['language'] = self::_getLanguage($language);

			//Se houver imagem
			if(!empty($data['ogImage']))
			{
				//Se não for um link externo
				if(substr($data['ogImage'], 0, 4) != 'http')
				{
					//Buscar caminho interno da imagem
					$core = new Custom();
					$image = $core->getPath($data['ogImage']);

					if(file_exists($image))
					{
						//Salvar tamanho da imagem
						list($width, $height) = @getimagesize($image);

						$data['ogImageWidth'] = $width;
						$data['ogImageHeight'] = $height;

						$data['ogImage'] = $core->getURL() . "/" . $data['ogImage'];
					}
				}
			}

			//Inserir ou atualizar registro
			return self::table()
				->where('scope', $scope)
				->where('id_source', $id_source)
				->where('language', $data['language'])
				->whereNull('date_delete')
				->place($data);
				
		}

		public const IMAGE_PATH = 'public/images/og-img';

		/**
		 * Salvar uma imagem OG na pasta correta
		 * 
		 * @param  array $file Array com arquivo
		 * 
		 * @return string
		 */
		static public function saveOgImage ($file)
		{
			$core = new Custom();
			$path = $core->getPath(self::IMAGE_PATH);

			if(!is_dir($path))
				mkdir($path, 0655, true);

			$map = [
				[
					'maxDimension' 		=> [1200, 627],
					'quality' 			=> 95,
					'appendFilename' 	=> false,
					'convert' 			=> 'jpg'
				]
			];

			$upload = $core->saveUploadImages($map, $path, $file, 'md5_file');

			if($upload instanceof \Fault)
				return $upload;

			$images = [];

			foreach ($upload as $image)
			{
				foreach ($image as $file)
					$images[] = self::IMAGE_PATH . '/' . $file;
			}

			return $images;
		}

		/**
		 * Resgata dados de SEO de um conteúdo
		 * 
		 * @param  string 		$scope Escopo
		 * @param  string|int	$id_source    Identificação do conteúdo
		 * @param  string  		$language Idioma. Caso esteja como null, considera o idioma atual
		 * 
		 * @return array
		 */
		static public function retrieve ($scope, $id_source, $language = null)
		{
			return self::table()
					->whereNull('date_delete')
					->where('scope', $scope)
					->where('id_source', $id_source)
					->where('language', self::_getLanguage($language))
					->selectFirst();
		}

		/**
		 * Insere os dados de SEO numa página (para exibir automaticamente no header)
		 * 
		 * @param Page   $page  Objeto da página onde serão incluídos os dados
		 * @param  string 		$scope Escopo
		 * @param  string|int	$id_source    Identificação do conteúdo
		 * @param  string  		$language Idioma. Caso esteja como null, considera o idioma atual
		 *
		 * @return  null
		 */
		static public function setOnPage (Page $page, $scope, $id_source, $language = null)
		{
			$data = self::retrieve($scope, $id_source, $language);

			if(!$data)
				return false;

			$page->variables = array_merge($page->variables, $data);
		}

		/**
		 * Retorna a query de consulta para incorporar em uma query
		 * 
		 * @param  string $scope Escopo
		 * @param  string $language Idioma. Caso esteja como null, considera o idioma atual
		 * 
		 * @return \Icecream\Icecream
		 */
		static public function embedQuery ($scope, $language = null)
		{
			return self::table()
				->whereNull('date_delete')
				->where('language', self::_getLanguage($language))
				->where('scope', $scope);
		}



	}