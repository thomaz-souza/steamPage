<?php
/**
* Funções de inclusão de SEO nas páginas
*
* @package	SEO
* @author 	Lucas/Postali
*/	
	namespace SEO;

	use \CMS\InterfaceCMS;

	class PageSEO extends \Core
	{

		/**
		 * Resgata todas as páginas habilitadas a terem dados de SEO
		 * 
		 * @return  array
		 */
		public function getPages ($call)
		{
			$pages = [];

			foreach($this->_config('page') as $id => $page)
			{
				if(!isset($page['file']))
					continue;

				if(!isset($page['static']) && !isset($page['pattern']) && !isset($page['match']))
					continue;

				$page['id'] = $id;
				$pages[] = $page;
			}

			return $pages;
		}

		/**
		 * Salva dados de SEO de uma página
		 * 
		 * @return array
		 */
		public function savePage ($call)
		{
			$pages = $this->_config('page');
			$block = $pages[$call['content']['id']];

			//Se houver arquivos enviados, salvá-los
			if(!empty($call['ogImageFile']))
			{
				$uploads = Custom::saveOgImage($call['ogImageFile']);

				if($uploads instanceof \Fault)
					return $uploads;
			}

			foreach ($call['content']['seo'] as $language => &$seo)
			{
				//Se houver imagem enviada, salvá-la
				if(isset($seo['ogImageFile']))
				{
					$image = $uploads[$seo['ogImageFile']];
					$seo['ogImage'] = $image;

					//Salvar tamanho da imagem
					list($width, $height) = @getimagesize($image);

					$seo['ogImageWidth'] = $width;
					$seo['ogImageHeight'] = $height;
				}

				//Excluir tamanho da imagem, se não houver
				if(empty($seo['ogImage']))
				{
					$seo['ogImageWidth'] 	= '';
					$seo['ogImageHeight'] 	= '';
				}
			}

			$data = array_merge(isset($block['seo']) ? $block['seo'] : [], $call['content']['seo']);

			$this->_setConfig('page/' . $call['content']['id'], $data, 'seo');
			
			$sitemap = $call['content']['sitemap'];

			$this->_setConfig('page/' . $call['content']['id'], $sitemap, 'sitemap');

			return [$data, $sitemap];
		}

		/**
		 * Insere dados de SEO na página (através de hook no .config)
		 * 
		 * @return string
		 */
		public function pageHandler (\Navigation\Page $page, $content)
		{
			$seo = $page->getStructure('seo', null);

			//Se não houver dados de SEO
			if(empty($seo))
				return;

			$language = $this->currentLanguage();

			//Resgatar SEO da língua atual
			if(isset($seo[$language]))
				$seo = $seo[$language];

			//Se não houver da lingua atual, resgatar da primeira lingua
			else
				$seo = $seo[array_keys($seo)[0]];

			//Para cada variável de SEO
			foreach ($seo as $key => $value)
			{
				//Se já foi definida na página, não inserir
				if(!empty($page->variables[$key]))
					continue;

				if($key == 'ogImage' && !empty($value))
					$value = $this->formatURL($this->getURL(), $value);

				//Inserir nas variáveis da página
				$page->variables[$key] = $value;
			}
		}
	}
