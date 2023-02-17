<?php
/**
* Funções do Youtube do Google
*
* @package	Google
* @author 	Lucas/Postali
*/	

	namespace Google;
	use \Navigation\Page;

	class Youtube extends _Google
	{	

		/**
		* Cria a Tag do Iframe do Youtube
		*
		* @param string $videoId ID do vídeo
		* @param array $tagParams Parâmetros da Tag
		*
		* @return string
		*/	
		static public function embed ($videoId, $tagParams = array())
		{
			//Criar o link
			$tagParams['src'] = "https://www.youtube.com/embed/" . $videoId;

			//Permissão
			$tagParams['allow'] = "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture";

			//Tirar borda
			$tagParams['frameborder'] = "0";
				
			//Retornar Tag
			return Page::createTag('iframe', $tagParams, '');
		}

		/**
		* Inclui o JavaScript de incorporação de vídeo
		*
		* @param resource $page Recebe a página atual
		*
		* @return string
		*/	
		static public function addScriptTag (\Navigation\Page $page)
		{
			//Criar Tag com o conteúdo
			$content = 'var t=document.createElement("script");t.src="https://www.youtube.com/iframe_api";var f=document.getElementsByTagName("script")[0];f.parentNode.insertBefore(t,f);';
			
			$page->addTagHeadJS(null, null, null, $content);
		}

		/**
		* Inclui o JavaScript do botão Subscrever e retorna o botão
		*
		* @param resource $page Recebe a página atual
		* @param string $channel ID do canal
		* @param string $layout Layout do botão (Valores possíveis: default/full)
		* @param string $count Exibir contador de inscritos (Valores possíveis: hidden/default)
		*
		* @return string
		*/	
		static public function subscribeButton (\Navigation\Page $page, $channel, $layout = 'default', $count = 'hidden')
		{
			//Adicionar script à página
			$page->addTagJS('https://apis.google.com/js/platform.js', false);

			//Criar e retornar botão
			$tagParams = array(
				"class" => "g-ytsubscribe",
				"data-channel" => $channel,
				"data-layout" => $layout,
				"data-count" => $count
			);
			return Page::createTag('div', $tagParams, '');
		}

		
	}

?>