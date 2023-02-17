<?php
/**
* Criação do robots.txt
*
* @package	SEO
* @author 	Lucas/Postali
*/

namespace SEO;

use Navigation\Navigation;

class Robots extends \Core
{
	
	/**
	* Incluir barra no início da instrução, caso não haja
	*
	* @param $dir Recebe o diretório em questão
	*
	* @return string
	**/
	private function _parseDirectory ($dir)
	{
		$dir = preg_replace("/^\\//", "", $dir);
		return "/$dir";
	}

	/**
	* Exibe o conteúdo do robots.txt
	*
	* @return string
	**/
	public function show ()
	{
		//Buscar configurações do SEO Robots
		$config = $this->_config('SEO')['robots'];

		//Listagem de instruções do robots
		$robots = [];

		//Para cada um dos agentes
		foreach ($config['agents'] as $agentName => $agentValues)
		{	
			//Incluir agente
			$robots[] = 'User-agent: ' . $agentName;

			//Se houver diretórios/arquivos permitidos
			if(isset($agentValues['allow']))
			{
				//Se for string e válido, incluir
				if(is_string($agentValues['allow']) && !empty($agentValues['allow']))
					$robots[] = "Allow: " . $this->_parseDirectory($agentValues['allow']);

				//Se for um array, incluir cada um
				else if(is_array($agentValues['allow']))
					foreach ($agentValues['allow'] as $value)
						$robots[] = "Allow: " . $this->_parseDirectory($value);
			}

			//Se houver diretórios/arquivos NÃO permitidos
			if(isset($agentValues['disallow']))
			{
				//Se for string e válido, incluir
				if(is_string($agentValues['disallow']) && !empty($agentValues['disallow']))
					$robots[] = "Disallow: " .  $this->_parseDirectory($agentValues['disallow']);

				//Se for um array, incluir cada um
				else if(is_array($agentValues['disallow']))
					foreach ($agentValues['disallow'] as $value)
						$robots[] = "Disallow: " .  $this->_parseDirectory($value);
			}
		}
		
		//Caso haja a configuração de sitemap e haja uma string, considerar a string
		if(isset($config['sitemap']) && is_string($config['sitemap']))
			$robots[] = 'Sitemap: ' . $config['sitemap'];

		//Se houver o sitemap e ele for TRUE ou ele não existir E houver um arquivo de sitemap, incluir caminho automático
		else if ( ((isset($config['sitemap']) && $config['sitemap'] === true) || !isset($config['sitemap'])) && $this->getRouteURL('sitemap'))
			$robots[] = 'Sitemap: ' . $this->getRouteURL('sitemap');

		//Criar arquivo
		return implode(PHP_EOL, $robots);
	}
}