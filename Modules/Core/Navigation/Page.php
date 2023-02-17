<?php
/**
* Classe para exibição de páginas e navegação
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;

	class Page extends Navigation
	{
		//Incorporando função de Tags
		use _Tags;

		//Incorporando dados extras (using, tags, assets)
		use _PageExtraContent;

		//Incorporando função de SuperTags
		use _SuperTags;

		const ROUTE_TYPE_METHOD = 'method';
		const ROUTE_TYPE_BLOCK = 'block';
		const ROUTE_TYPE_PAGE = 'page';
		const ROUTE_TYPE_REDIRECT = 'redirect';
		
		/**
		 * @var array Página atual
		 */
		protected $currentPage = [];

		/**
		 * @var string Nome/ID da rota
		 */
		private $_name = null;

		/**
		 * @var array Variáveis da página
		 */
		public $variables = [];

		/**
		 * @var array Dados e especificações do bloco
		 */
		private $_structure = [];


		function __construct ($routeName, $variables = [], $structure = null)
		{			
			$this->_name = $routeName;
			$this->_structure = !empty($structure) ? $structure : $this->getRoute($routeName);

			$this->currentPage = $this->_structure;

			$this->variables = $this->getStructure('data', []);
			if(!empty($variables))			
				$this->variables = array_merge($this->variables, $variables);

			$this->_assets['header'] = $this->getStructure('headerAssets', []);
			$this->_assets['footer'] = $this->getStructure('footerAssets', []);
		}

		/**
		 * Resgata o nome da página
		 * 
		 * @return string
		 */
		public function getName ()
		{
			return $this->_name;
		}

		/**
		 * Resgata o a URL da página
		 * 
		 * @return string
		 */
		public function getPageURL ($variables = null)
		{
			return $this->getRouteURL($this->getName(), $variables === null ? $this->variables : $variables);
		}

		/**
		 * Resgata características desse bloco
		 * 
		 * @param string $key Chave a ser buscada
		 * @param mixed $default Valor a ser retornado caso não se encontre
		 * 
		 * @return mixed
		 */
		public function getStructure ($key = null, $default = null)
		{
			if(!$key) return $this->_structure;

			return isset($this->_structure[$key]) ? $this->_structure[$key] : $default;
		}

		/**
		 * Resgata os filtros da página
		 * 
		 * @return string
		 */
		public function getFilters ($key = null)
		{
			$filters = $this->getStructure('filters', []);

			if(empty($key))
				return $filters;

			return isset($filters[$key]) ? $filters[$key] : null;
		}

		/**
		 * Identifica o tipo dessa rota (bloco, método ou página)
		 * 
		 * @return string
		 */
		public function getType ()
		{
			if(!empty($this->getRedirect()))
				return self::ROUTE_TYPE_REDIRECT;

			if(!empty($this->getMethod()))
				return self::ROUTE_TYPE_METHOD;

			if($this->getStructure('static', false) === false && empty($this->getStructure('pattern')) && empty($this->getStructure('match')))
				return self::ROUTE_TYPE_BLOCK;

			return self::ROUTE_TYPE_PAGE;
		}

		/**
		 * Informa se é um método a ser carregado
		 * 
		 * @return bool
		 */
		public function isMethod ()
		{
			return $this->getType() === self::ROUTE_TYPE_METHOD;
		}

		/**
		 * Informa se é um bloco a ser carregado
		 * 
		 * @return bool
		 */
		public function isBlock ()
		{
			return $this->getType() === self::ROUTE_TYPE_BLOCK;
		}

		/**
		 * Informa se é uma página a ser carregada
		 * 
		 * @return bool
		 */
		public function isPage ()
		{
			return $this->getType() === self::ROUTE_TYPE_PAGE;
		}

		/**
		 * Informa se a rota é um redirecionamento
		 * 
		 * @return bool
		 */
		public function isRedirect ()
		{
			return $this->getType() === self::ROUTE_TYPE_REDIRECT;
		}

		/**
		 * Resgata dados de blocos a serem incluídos
		 * 
		 * @param string $place Localização (before|after)
		 * 
		 * @return array|null
		 */
		public function getInclude ($place)
		{
			$blocks = [];

			if($place == 'before')
				$blocks = $this->getStructure('includeBefore', []);

			else if($place == 'after')
				$blocks = $this->getStructure('includeAfter', []);

			foreach ($blocks as &$block)
				$block = $this->getBlock($block);			

			return $blocks;
		}

		/**
		 * Apelido para getInclude('before')
		 * 
		 * @return array|null
		 */
		public function getIncludeBefore ()
		{
			return $this->getInclude('before');
		}

		/**
		 * Apelido para getInclude('after')
		 * 
		 * @return array|null
		 */
		public function getIncludeAfter ()
		{
			return $this->getInclude('after');
		}

		/**
		 * Retorna todos os blocos de inclusão
		 * 
		 * @return array
		 */
		public function getAllIncludes ()
		{
			return array_merge($this->getIncludeBefore(), $this->getIncludeAfter());
		}

		/**
		 * Resgata redirecionamento
		 * 
		 * @return string|null
		 */
		public function getRedirect ()
		{
			return $this->getStructure('redirect');
		}

		/**
		 * Resgata código de redirecionamento
		 * 
		 * @return string|null
		 */
		public function getRedirectCode ()
		{
			return $this->getStructure('redirectCode', 302);
		}

		/**
		 * Resgata as classes a serem incorporadas nessa página
		 * 
		 * @return array
		 */
		public function getUsing ()
		{
			return $this->getStructure('using', []);
		}

		/**
		 * Resgata o tipo de conteudo
		 * 
		 * @return string
		 */
		public function getContentType ()
		{
			return $this->getStructure('contentType', 'text/html');
		}

		/**
		 * Resgata o arquivo desse bloco
		 * 
		 * @return string|null
		 */
		public function getFile ()
		{
			return $this->getStructure('file');
		}

		/**
		 * Resgata o método desse bloco
		 * 
		 * @return string|null
		 */
		public function getMethod ()
		{
			return $this->getStructure('method');
		}
		
		/**
		 * Resgata o conteúdo executado do arquivo
		 * 
		 * @param string $file (Opcional) Arquivo a ser carreado 
		 * 
		 * @return string
		 */
		protected function _processContent ($file = null)
		{
			$file = $file === null ? $this->getFile() : $file;

			//Conferir se o arquivo do bloco está definido
			if(empty($file))
				$this->Error($this->write("There is no file defined for block '%s'.", "validation", $this->getName()), true);

			//Caminho do arquivo
			$file = $this->getPath("View/" . $file);

			//Conferir se o arquivo do bloco solicitado existe
			if(!file_exists($file))
				$this->Error($this->write("The file for block '%s' doesn't exist.", "validation", $this->getName()), true);

			//Iniciar a captura de dados
			ob_start();

			//Incorporar arquivo
			require($file);

			//Resgatar conteúdo
			$content = ob_get_contents();

			//Limpar os dados de saída
			ob_end_clean();			

			return $content;
		}

		/**
		 * Carrega um bloco
		 * @param string $routeName Nome da rota
		 * @return string
		 */
		public function block ($routeName)
		{
			$block = $this->getBlock($routeName);

			//Incluir todos os using
			foreach ($block->getUsing() as $class)
				$this->using($class);

			//Incluir todos os assets deste bloco
			$this->_includeBlockAssets($block);

			return $this->_processContent($block->getFile());
		}

		/**
		 * Incluir todos os assets de um bloco
		 * 
		 * @param Page $block Objeto de bloco
		 * 
		 * @return null
		 */
		protected function _includeBlockAssets ($block)
		{
			//Incorporar os assets do header
			foreach ($block->getHeaderAssets() as $asset)
				$this->addAsset($asset);

			//Incorporar os assets do footer
			foreach ($block->getFooterAssets() as $asset)
				$this->addAsset($asset, false);
		}

		/**
		 * Renderizar a página
		 * 
		 * @return string
		 */
		public function render ()
		{	
			//Se for um redirecionamento, executar
			if($this->isRedirect())
				return $this->redirectTo($this->getRedirect(), $this->getRedirectCode());

			//Incluir content type da página
			$this->setContentType($this->getContentType());

			//Se for um método, processar como método
			if($this->isMethod())
			{
				$execution = $this->parseMethod($this->getMethod(), true, $this);

				if($execution !== false)
					return $execution;

				throw new \Exception("Method doesn't exist", 2);
			}

	   		//------A partir daqui, o código assume que se trata de uma página ou bloco			

			//Para cada uma das inclusões
			foreach($this->getAllIncludes() as $block)
			{
				//Incluir todos os using
				foreach ($block->getUsing() as $class)
					$this->using($class);

				//Incorporar os dados
				$this->variables = array_merge($block->variables, $this->variables);
			}

			//Incluir todos os "using" desse bloco
			foreach ($this->getUsing() as $class)
				$this->using($class);

			//Processar e resgatar conteúdo da página principal
			$content = $this->_processContent();

			//Processar e exbir conteúdo de todos os blocos que devem ser incluídos antes
			foreach($this->getIncludeBefore() as $block)
			{
				$this->_includeBlockAssets($block);
				$content = $this->_processContent($block->getFile()) . $content;
			}

			//Incluir assets desse bloco
			$this->_includeBlockAssets($this);			

			//Processar e exbir conteúdo de todos os blocos que devem ser incluídos em seguida
			foreach($this->getIncludeAfter() as $block)
			{
				$this->_includeBlockAssets($block);	
				$content .= $this->_processContent($block->getFile());
			}

			return $this->_includeExtraContent($content);
		}

	}