<?php
/**
* Classe para Leitura e criação de Super Tags
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;

	Trait _SuperTags
	{
		protected function _parseTagParams ($content)
		{
			//Resgatar nome da tag e conteúdo da tag
			if(!preg_match("/<([^\s]+)[\s]{1,}([\s\S]+?)>/", $content, $tagMatches))
				return false;

			//Nome da Tag
			$tagName = $tagMatches[1];

			//Conteúdo da Tag
			$tagContentParams = $tagMatches[2];

			$params = [];
			
			//Resgatar parâmetros
			if(preg_match_all("/([0-9A-z_:]+)[\s]{0,}={0,1}[\s]{0,}([\"\']+([\s\S]+?)[\"\']+){0,}[\s]{0,}/", $tagContentParams, $tagParamsMatches))
				for($i = 0; $i < count($tagParamsMatches[0]); $i++)
				{
					$paramName = $tagParamsMatches[1][$i];
					$params[str_replace("su:", "", $paramName)] = $tagParamsMatches[3][$i];
				}
			
			return $params;
		}

		private function _parseSuperTagContent (&$params)
		{
			//if(!$params) return "";

			//Se houver o parâmetro Tags
			if(isset($params['tags']))
			{
				//Resgatar o conteúdo da tag, e retornar conteúdo
				$content = $this->getTags($params['tags']);
				unset($params['tags']);
				
				return $content;
			}

			//Se foi selecionado o parâmetro de tradução
			if(isset($params['write']))
			{
				//Realizar tradução 
				$content = $this->write(
					$params['write'], //Texto
					isset($params['scope']) ? $params['scope'] : null, //Escopo
					null,
					isset($params['language']) ? $params['language'] : null
				);

				switch (isset($params['case']) ? $params['case'] : null)
				{
					case 'upper':
						$content = mb_strtoupper($content, $this->getLanguageCharset());
					break;

					case 'lower':
						$content = mb_strtolower($content, $this->getLanguageCharset());
					break;

					case 'camelcase':
						$content = ucwords($content);
					break;

					case 'capital':
						$content = ucfirst($content);
					break;

					case 'uncapital':
						$content = lcfirst($content);
					break;
				}

				unset($params['write']);
				unset($params['language']);
				unset($params['scope']);
				unset($params['case']);

				return $content;
			}

			//Parâmetro de variável
			if(isset($params['var']))
			{
				$content = isset($this->variables[$params['var']]) ? $this->variables[$params['var']] : '';
				unset($params['var']);
				return $content;
			}

			//Parâmetro de variável
			if(isset($params['block']))
			{
				$content = $this->_processContent($params['block']);
				unset($params['block']);
				return $content;
			}

			//Parâmetro de URL
			if(isset($params['url']))
			{
				$url = "";

				switch ($params['url']) {

					case 'self':
						$url = $this->getPageURL();
					break;

					case 'page':
						if(isset($params['page']))
							$url = $this->getRouteURL($params['page']);
					break;

					case 'site':
						$url = $this->getURL();
					break;

					case 'public':
						$url = $this->getPublicURL();
					break;
				}	

				if(isset($params['vars']))
					$url = $this->formatURL($url, explode("/", $params['vars']));

				unset($params['url']);
				unset($params['vars']);

				return $url;
			}
			
			return '';
		}

		protected function includeSuperTags ($content)
		{
			$content = preg_replace_callback("/(\<su:[\s\S]+?\>)/", function ($m)
				{
					$params = $this->_parseTagParams($m[1]);
					return $this->_parseSuperTagContent($params);	

				},
			$content);

			$content = preg_replace_callback("/\<([^\s>]+)([^>]+?)su\:([0-9A-z_]+?)[\s]{0,}=[\s]{0,}([\"\']+([\s\S]+?)[\"\']+)([^>]+?){0,}>/", function ($m)
				{
					$params = $this->_parseTagParams($m[0]);
					$inside = "";

					$content = $this->_parseSuperTagContent($params);	

					foreach ($params as $key => $param)
						$inside .= " $key=\"$param\"";					

					return "<$m[1]$inside>$content";
				},
			$content);

			return $content;
		}



	}