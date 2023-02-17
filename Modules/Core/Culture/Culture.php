<?php
/**
* Função responsável pela criação de diferentes culturas
*
* @package	Culture
* @author 	Lucas/Postali
*/
	namespace Culture;

	use \Datetime;
	use Navigation\Page;

	trait Culture
	{
		//Funções de cultura
		use _Culture;

		//Funções de moeda
		use _Currency;

		//Funções de língua
		use _Language;

		//Funções de fuso horário
		use _Timezone;

		/**
		* Retorna dados de configuração da cultura
		*
		* @param string $type (Opcional) Recebe o tipo de dado da cultura
		*
		* @return array
		*/
		private function _getCultureConfig ($type = null)
		{
			$config = $this->_config('culture');
			return is_null($type) ? $config : (isset($config[$type]) ? $config[$type] : null);
		}

		/**
		* Limpa todos os dados de cultura
		*
		* @return null
		*/
		private function _clearAllCulture ()
		{
			$this->clearCulture();
			$this->clearLanguage();
			$this->clearTimezone();
			$this->clearCurrency();
		}

		/**
		* Escreve uma frase para o idioma em questão (sem instância)
		*
		* @param string $content Recebe o conteúdo a ser traduzido
		* @param string $scope Recebe o escopo de tradução
		* @param string $values Recebe os valores
		* @param string $language Recebe código da língua
		* 
		* @return string
		*/
		static public function quickWrite ($content, $scope = null, $values = null, $language = null)
		{
			$core = new \Core;
			return $core->write($content, $scope, $values, $language);
		}

		/**
		 * Incorpora tradução Vue na página
		 * 
		 * @param Page $page Página onde será inserido
		 * 
		 * @return void
		 */
		public function addVueTranslator (Page $page, $language = null)
		{
			//Prepara a língua na qual será traduzido
			$language = $this->prepareLanguage($language);

			//Verificar se a língua está disponível
			$content = $GLOBALS['LanguageContent'][$language] ?? [];

			$page->addTagHeadJS(null, false, null, "const TRANSLATOR=" . json_encode($content));
			$page->addAsset('translator');
		}
	}

?>