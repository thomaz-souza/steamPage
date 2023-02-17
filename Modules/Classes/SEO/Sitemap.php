<?php
/**
* Funções de Sitemap
*
* @package	SEO
* @author 	Lucas/Postali
*/

namespace SEO;

use Navigation\Navigation;

class Sitemap extends \Core
{
	/**
	* @var Valores permitidos para o parâmetro "changefreq"
	**/
	const ALLOWED_CHANGEFREQ = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];

	/**
	* Exibe o conteúdo do sitemap.xml
	*
	* @return string
	**/
	public function show ()
	{
		//Iniciar a captura de dados
		ob_start();

		//Conjunto de URLS
		$urlset = [];

		//Para cada página declarada
		foreach ($this->_config('page') as $key => $page)
		{
			$sitemap = isset($page['sitemap']) ? $page['sitemap'] : [];

			//Se o sitemap for false, ignorar
			if($sitemap === false)
				continue;			

			//Se não há um caminho estático e não há um caminho definido em SEO => loc, ignorar
			if( !isset($page['static']) && !isset($sitemap['loc']) && !isset($page['pattern']))
				continue;

			if( isset($page['pattern']) && !isset($sitemap['locVariablesSource']) )
				continue;

			//Se for uma página de redirecionamento e não houver sitemap declarado, ignorar
			if(isset($page['redirect']) && (!$sitemap || empty($sitemap)))
				continue;

			//Iniciar valores desse conjunto
			$url = [];

			if(isset($page['pattern']) && isset($sitemap['locVariablesSource']))
			{
				if(isset($sitemap['locVariablesSource']) && is_string($sitemap['locVariablesSource']))
					$locVariablesSources = $this->parseMethod($sitemap['locVariablesSource'], true);
				else 
					$locVariablesSources = $sitemap['locVariablesSource'];
			}

			//Se houver um changefreq definido e ele for válido, incluí-lo
			if(isset($sitemap['changefreq']) && in_array($sitemap['changefreq'], self::ALLOWED_CHANGEFREQ))
				$url['changefreq'] = $sitemap['changefreq'];

			//Se houver um lastmod definido
			if(isset($sitemap['lastmod']))

				//Se for true, gerar automaticamente
				if($sitemap['lastmod'] === true)
					$url['lastmod'] = date('Y-m-d', filemtime($this->getPath('View/'.$page['file'])));

				//Senão, incluir o valor declarado
				else
					$url['lastmod'] = $sitemap['lastmod'];

			//Se houver um priority e ele for válido, incluí-lo
			if(isset($sitemap['priority']) && 
				is_numeric($sitemap['priority']) && $sitemap['priority'] >= 0 && $sitemap['priority'] <= 1)
				$url['priority'] = $sitemap['priority'];

			//Se houver uma localização definida, incluir
			if($sitemap && isset($sitemap['loc']))
			{
				//Se for solicitado parsear o parâmtro LOC
				if(isset($sitemap['locParse']) && $sitemap['locParse'] === true)
					$url['loc'] = $this->getURL() . "/" . $sitemap['loc'];

				//Senão, incluí-lo normalmente
				else
					$url['loc'] = $sitemap['loc'];
			}

			//Se houver pattern
			else if(isset($page['pattern']) && $locVariablesSources)
			{	
				//Se há vários valores dentro do array, incluir várias páginas
				if( isset($locVariablesSources[0]) && is_array($locVariablesSources[0]) )
				{	
					//Para cada um desses arrays
					foreach ($locVariablesSources as $locVar)
					{	
						//Se houver parâmetros adicionais de sitemap
						if(isset($locVar['sitemap']))
						{
							$params = $locVar['sitemap'];

							//Se houver um lastmod definido
							if(isset($params['lastmod']))
								$url['lastmod'] = $params['lastmod'];

							//Se houver um changefreq definido
							if(isset($params['changefreq']) && in_array($params['changefreq'], self::ALLOWED_CHANGEFREQ))
								$url['changefreq'] = $params['changefreq'];

							//Se houver um priority definido
							if(isset($params['priority']) && 
								is_numeric($params['priority']) && $params['priority'] >= 0 && $params['priority'] <= 1)
								$url['priority'] = $params['priority'];

							$locVar = $locVar['variables'];
						}
						$url['loc'] = $this->getRouteURL($key, $locVar);
						$urlset[] = ['url' => $url ];
					}
					continue;
				}

				//Se é um array comum apenas com os valores, retornar 
				$url['loc'] = $this->getRouteURL($key, $locVariablesSources);
			}
			//Senão, receber o link da página estática
			else
			{
				$url['loc'] = $this->getRouteURL($key);
			}

			$urlset[] = ['url' => $url ];
		}

		//Montar XML
		$urlset = [
			'urlset' => $urlset,
			'_params' =>
				['attribute_ns' => 
					[
					'schemaLocation' => ['prefix' => 'xsi', 'value' => "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd", 'uri' => "http://www.w3.org/2001/XMLSchema-instance"],
					],
				'attribute' => 
					[
						'xmlns' => "http://www.sitemaps.org/schemas/sitemap/0.9"
					]
				]
		];

		//Parsear XML
		$xml = XMLConverter::arrayToXMLConverter($urlset);
		
		//Limpar saída de dados
		ob_end_clean();

		//Retornar conteúdo do XML
		return $xml;			
	}
}