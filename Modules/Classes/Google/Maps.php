<?php
/**
* Funções do Maps do Google
*
* @package	Google
* @author 	Lucas/Postali
*/	
	
	namespace Google;

	class Maps extends _Google
	{
		//Caminho da API
		const API_URL = "https://maps.googleapis.com";		

		//Endpoint do StreetView
		const STREET_VIEW_ENDPOINT = '/maps/api/streetview';

		/**
		* Resgatar uma imagem do StreetView e retorna a URL da imagem
		*
		* @param array $params Parâmetros do local
		* @return	string
		*/	
		public function streetView ($params = array())
		{
			//Buscar configurações do Google
			$config = $this->_config('google');

			//Verificar se os dados de configuração estão presentes
			if(!isset($config['maps']) || !isset($config['maps']['street-view-key']) || !isset($config['maps']['street-view-key']['key']) )
				return new \Fault('Configuration of Google Maps is missing', 'google-maps-config-missing');

			//Incluir chave
			$params['key'] = $config['maps']['street-view-key']['key'];

			//Incluir assinatura, se houver
			if(isset($config['maps']['street-view-key']['signature']))
				$this->_signURL(self::STREET_VIEW_ENDPOINT, $params, $config['maps']['street-view-key']['signature']);

			return self::API_URL . self::STREET_VIEW_ENDPOINT . "?" . http_build_query($params);
		}

		//Endpoint do Static Map
		const STATIC_MAP_ENDPOINT = '/maps/api/staticmap';

		/**
		* Resgatar uma imagem do Mapa e retorna a URL da imagem
		*
		* @param array $params Parâmetros do local
		* @param array $markers Marcadores de local
		*
		* @return	string
		*/
		public function staticMap ($params = array(), $markers = array())
		{
			//Buscar configurações do Google
			$config = $this->_config('google');

			//Verificar se os dados de configuração estão presentes
			if(!isset($config['maps']) || !isset($config['maps']['static-map-key']) || !isset($config['maps']['static-map-key']['key']))
				return new \Fault('Configuration of Google Maps is missing', 'google-maps-config-missing');

			$params['markers'] = array();
			foreach ($markers as $key => $value)
				$params['markers'][] = "$key:$value";

			//Incluir marcadores
			$params['markers'] = urlencode(implode("&", $params['markers']));

			//Incluir chave
			$params['key'] = $config['maps']['static-map-key']['key'];

			//Incluir assinatura, se houver
			if(isset($config['maps']['static-map-key']['signature']))
				$this->_signURL(self::STATIC_MAP_ENDPOINT, $params, $config['maps']['static-map-key']['signature']);

			return self::API_URL . self::STATIC_MAP_ENDPOINT . '?' . http_build_query($params);
		}

		public function mapJavascriptScriptTag (\Navigation\Page $page, $function = 'initMap')
		{
			//Buscar configurações do Google
			$config = $this->_config('google');

			//Verificar se os dados de configuração estão presentes
			if(!isset($config['maps']) || !isset($config['maps']['map-js']) || !isset($config['maps']['map-js']['key']))
				return new \Fault('Configuration of Google Maps is missing', 'google-maps-config-missing');

			//Parâmetros da URL
			$url_params = array(
				'key' => $config['maps']['map-js']['key'],
				'callback' => $function
			);

			//Parâmetros da Tag
			$params = array(
				'async' => null,
				'defer' => null, 
				'src' => self::API_URL . "/maps/api/js?" . http_build_query($url_params)
			);

			//Incluir Tag
			$page->addTagJS(null, null, $params);
		}

		const ZOOM_WORLD = 1;
		const ZOOM_CONTINENT = 5;
		const ZOOM_CITY = 10;
		const ZOOM_STREETS = 15;
		const ZOOM_BUILDINGS = 20;

		public function mapJavascript (\Navigation\Page $page, $elementId, $center, $zoom = 8)
		{
			//Buscar configurações do Google
			$config = $this->_config('google');

			//Verificar se os dados de configuração estão presentes
			if(!isset($config['maps']) || !isset($config['maps']['map-js']) || !isset($config['maps']['map-js']['key']))
				return new \Fault('Configuration of Google Maps is missing', 'google-maps-config-missing');

			//Incluir tag de script com dados
			$this->mapJavascriptScriptTag($page);

			//Criar conteúdo da função
			$content = "var initMap = function(){ map = new google.maps.Map(document.getElementById('$elementId'), {center:" . json_encode($center) . ", zoom: $zoom })};";

			//Adicionar função à página
			$page->addTagJS(null, null, null, $content);
		}

		public function mapEmbed ($url, $urlParams = array(), $tagParams = array())
		{	
			//Buscar configurações do Google
			$config = $this->_config('google');

			//Verificar se os dados de configuração estão presentes
			if(!isset($config['maps']) || !isset($config['maps']['map-embed']) || !isset($config['maps']['map-embed']['key']))
				return new \Fault('Configuration of Google Maps is missing', 'google-maps-config-missing');

			//Adicionar a Tag
			$urlParams['key'] = $config['maps']['map-embed']['key'];

			//Criar o link
			$tagParams['src'] = "https://www.google.com/maps/embed/v1/$url?" . http_build_query($urlParams);

			//Permissão
			$tagParams['allow'] = "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture";

			//Tirar borda
			$tagParams['frameborder'] = "0";
				
			//Retornar Tag
			return \Navigation\Page::createTag('iframe', $tagParams, '');
		}

		public function mapEmbedPlace ($place, $tagParams = array())
		{
			//Criar parâmetro 'q' e gerar mapa
			$params = array( 'q' => $place );
			return $this->mapEmbed('place', $params, $tagParams);
		}

		public function mapEmbedView ($center, $zoom = null, $mapType = null, $tagParams = array())
		{	
			//Definir parâmetros passados
			$params = array(
				'center' => $center,
				'zoom' => $zoom,
				'maptype' => $mapType
			);
			return $this->mapEmbed('view', $params, $tagParams);
		}
	}

?>