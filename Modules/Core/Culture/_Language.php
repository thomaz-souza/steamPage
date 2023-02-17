<?php
/**
* Função responsável pelos dados de Língua de cada cultura
*
* @package	Culture
* @author 	Lucas/Postali
*/
	namespace Culture;

	trait _Language
	{
		/**
		* Registra a língua do sistema (PHP)
		*
		* @param $language string (Opcional) Código da Língua. Se não for passado, é retornada a língua atual.
		*
		* @return string
		*/
		protected function setSystemLanguage ($language = null)
		{
			return setlocale (LC_ALL, $this->getLanguageLabel($language));
		}

		/**
		* Limpa os dados de língua
		*
		* @return mixed
		*/
		private function clearLanguage ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())
				unset($_SESSION['currentLanguage']);

			$this->_currentLanguage = null;
		}

		/**
		* Resgata a língua salva na sessão
		*
		* @return mixed
		*/
		private function _getSessionLanguage ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())

				//Se NÃO houver uma língua atual
				if(!isset($_SESSION['currentLanguage']))
					return false;

				//Se houver uma língua
				else
					return $_SESSION['currentLanguage'];

			return false;
		}

		/**
		* Insere na sessão a língua utilizada
		*
		* @param $language string Código da língua
		*
		* @return mixed
		*/
		private function _setSessionLanguage ($language)
		{
			//Se houver uma sessão ativa, salvar
			if($this->hasSession())				
				return $_SESSION['currentLanguage'] = $language;

			return false;
		}

		/**
		* Define ou resgata a língua padrão do momento.
		*
		* @return mixed
		*/
		public function listLanguages ()
		{
			return $this->_getCultureConfig('languages');
		}

		private $_currentLanguage = null;

		/**
		* Define ou resgata a língua padrão do momento.
		*
		* @return mixed
		*/
		public function currentLanguage ()
		{
			//Retornar língua da sessão, se houver
			$sessionLanguage = $this->_getSessionLanguage();
			if($sessionLanguage)
				return $sessionLanguage;
			
			//Se não houver língua 
			if(is_null($this->_currentLanguage))
			{
				//Se não houver sessão, setar língua padrão na sessão
				$this->_currentLanguage = $this->getCultureLanguage();
				$this->_setSessionLanguage($this->_currentLanguage);
			}

			//Se já houver uma língua instanciada, retornar
			return $this->_currentLanguage;
		}

		/**
		* Altera a língua padrão do momento e retorna todos os seus dados
		*
		* @param $language string Código da língua
		*
		* @return mixed
		*/
		public function setLanguage ($language)
		{	
			//Se for enviada uma língua, trocá-la
			if(isset($this->_getCultureConfig('languages')[$language]))
			{
				$this->_currentLanguage = $language;
				$this->_setSessionLanguage($this->_currentLanguage);
				return $this->_currentLanguage;
			}

			return false;
		}

		/**
		* Resgata a língua padrão do momento e retorna todos os seus dados
		*
		* @param $language string (Opcional) Código da língua. Se não for passado, é retornada a língua atual.
		*
		* @return mixed
		*/
		public function getLanguage ($language = null)
		{	
			//Se não foi solicitada uma língua específica, retornar a atual
			if(is_null($language))
				return $this->_getCultureConfig('languages')[$this->currentLanguage()];

			//Retornar a língua solicitada
			return $this->_getCultureConfig('languages')[$language];
		}

		/**
		* Retorna a primeira ocorrência da língua que está disponível
		*
		* @param mixed $languages Recebe os códigos das línguas a serem checadas. Pode ser uma string única ou array de línguas
		*
		* @return midex
		*/
		public function checkLanguageAvailability ($languages)
		{
			//Se for uma única string, transformar em array
			if(is_string($languages))
				$languages = [$languages];

			//Resgatar línguas disponíveis
			$availableLanguages = $this->_getCultureConfig('languages');

			//Para cada língua enviada
			foreach ($languages as $language)

				//Verificar em cada língua registrada
				foreach ($availableLanguages as $langId => $availableLanguage)

					//Verificar se há 'suitableFor' indicando as línguas as quais essa configuração serve, ou, caso não haja, verifica se é o mesmo que 'label'
					if(isset($availableLanguage['suitableFor']) && in_array($language, $availableLanguage['suitableFor'])
						|| $language == $availableLanguage["label"])
						return $langId;
			
			//Retornar falso caso nenhuma língua satisfaça a necessidade
			return false;
		}

		private $languageContent = array();

		/**
		* Busca arquivo da língua
		*
		* @param string $language Recebe código da língua
		* @return array
		*/
		public function languageLoadFile ($language)
		{
			//Retirar caracteres maliciosos
			$language = preg_replace("/[^A-z_-]+/", "", $language);

			//Criar nova de língua
			$GLOBALS['LanguageContent'][$language] = ["_" => []];

			//Resgatar caminho do arquivo e verificar se o mesmo existe
			$languageFile = $this->getPath("Resources/Languages/$language");
			if(!file_exists($languageFile))
				return false;

			foreach(scandir($languageFile) as $scopeFile)
			{
				if(preg_match("/([^\\.]+)\\.json/", $scopeFile, $match))
				{
					$content = file_get_contents($this->getPath("Resources/Languages/$language/$scopeFile"));
					$content = json_decode($content, true);
					if(json_last_error())
						continue;

					$GLOBALS['LanguageContent'][$language][$match[1]] = $content;
					$GLOBALS['LanguageContent'][$language]['_'] = array_merge($GLOBALS['LanguageContent'][$language]['_'], $content);
				}
			}			
			return $GLOBALS['LanguageContent'][$language];
		}

		/**
		* Prepara para iniciar uma tradução
		*
		* @param string $language Recebe código da língua
		* @return string
		*/
		private function prepareLanguage ($language = null)
		{
			//Se nenhuma língua foi enviada, resgatar língua atual
			if(is_null($language))				
				$language = $this->currentLanguage();			

			//Carregar conteúdo dessa língua caso ainda não tenha
			if(!isset($GLOBALS['LanguageContent'][$language]))
				$this->languageLoadFile($language);

			//Retornar língua a ser utilizada
			return $language;	
		}

		/**
		* Escreve uma frase para o idioma em questão
		*
		* @param string $content Recebe o conteúdo a ser traduzido
		* @param string $language Recebe código da língua
		* @return string
		*/
		private function _write ($content, $scope = null, $language = null)
		{
			//Prepara a língua na qual será traduzido
			$language = $this->prepareLanguage($language);

			//Verificar se o escopo é nulo
			if(is_null($scope))
				$scope = "_";

			//Verificar se a língua está disponível
			if(!isset($GLOBALS['LanguageContent'][$language]))
				return $content;

			//Verificar se o escopo está disponível, se não estiver, definir escopo padrão
			if(!isset($GLOBALS['LanguageContent'][$language][$scope]))
				return $content;// $scope = "_";

			//Verificar se o conteúdo existe no escopo e língua
			if(!isset($GLOBALS['LanguageContent'][$language][$scope][$content]))
				return $content;

			//Retornar conteúdo
			return $GLOBALS['LanguageContent'][$language][$scope][$content];
		}

		/**
		* Escreve uma frase para o idioma em questão
		*
		* @param string $content Recebe o conteúdo a ser traduzido
		* @param string $scope Recebe o escopo de tradução
		* @param string $values Recebe os valores
		* @param string $language Recebe código da língua
		* @return string
		*/
		public function write ($content, $scope = null, $values = null, $language = null)
		{
			if(!is_null($values))
				return $this->sprintWrite ($content, $values, $scope, $language = null);
			
			return $this->_write($content, $scope, $language);
		}

		/**
		* Escreve uma frase para o idioma em questão utilizando os valores enviados
		*
		* @param string $content Recebe o conteúdo a ser traduzido
		* @param array $values Recebe os valores para substituição
		* @param string $language Recebe código da língua
		* @return string
		*/
		public function sprintWrite ($content, $values, $scope = null, $language = null)
		{
			//Traduz conteúdo
			$translated = $this->_write($content, $language);

			if(!is_array($values))
				$values = [$values];

			//Retorna dados substituídos
			return vsprintf($translated, $values);
		}

		/**
		* Retorna o Charset da língua em questão
		*
		* @param string $language Recebe código da língua
		* @return string
		*/
		public function getLanguageCharset ($language = null)
		{
			return $this->getLanguage($language)['charset'];
		}

		/**
		* Retorna o Nome da língua em questão
		*
		* @param string $language Recebe código da língua
		* @return string
		*/
		public function getLanguageName ($language = null)
		{
			return $this->getLanguage($language)['name'];
		}

		/**
		* Retorna a Direção de Escrita da língua em questão
		*
		* @param string $language Recebe código da língua
		* @return string
		*/
		public function getLanguageDirection ($language = null)
		{
			return $this->getLanguage($language)['direction'];
		}

		/**
		* Retorna a Etiqueta da língua em questão
		*
		* @param string $language Recebe código da língua
		* @return string
		*/
		public function getLanguageLabel ($language = null)
		{
			return $this->getLanguage($language)['label'];
		}

		/**
		* Retorna os códigos de língua as quais esta língua supre
		*
		* @param string $language Recebe código da língua
		* @return string
		*/
		public function getLanguageSuitableFor ($language = null)
		{
			return $this->getLanguage($language)['suitableFor'];
		}
	}