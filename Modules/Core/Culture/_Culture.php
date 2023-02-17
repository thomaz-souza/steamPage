<?php
/**
* Função responsável pela alteração das culturas
*
* @package	Culture
* @author 	Lucas/Postali
*/
	namespace Culture;

	trait _Culture
	{
		/**
		* Checa se há uma cultura definida
		*
		* @return mixed
		*/
		public function isCultureSet ()
		{
			return ($this->_getSessionCulture() === false && $this->_currentCulture === null) ? false : true;	
		}

		/**
		* Limpa os dados de cultura
		*
		* @return mixed
		*/
		private function clearCulture ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())
				unset($_SESSION['currentCulture']);

			$this->_currentCulture = null;
		}

		/**
		* Resgata a cultura salva na sessão
		*
		* @return mixed
		*/
		private function _getSessionCulture ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())
			{
				//Se NÃO houver uma cultura atual
				if(!isset($_SESSION['currentCulture']))
					return false;

				//Se houver uma cultura
				else
					return $_SESSION['currentCulture'];
			}

			return false;
		}

		/**
		* Insere na sessão a cultura utilizada
		*
		* @param $culture string Código da cultura
		*
		* @return mixed
		*/
		private function _setSessionCulture ($culture)
		{
			//Se houver uma sessão ativa, salvar
			if($this->hasSession())				
				return $_SESSION['currentCulture'] = $culture;

			return false;
		}

		private $_currentCulture = null;

		/**
		* Define ou resgata a cultura padrão do momento.
		*
		* @return mixed
		*/
		public function currentCulture ()
		{
			//Retornar cultura da sessão, se houver
			$sessionCulture = $this->_getSessionCulture();
			if($sessionCulture)
				return $sessionCulture;
			
			//Se não houver cultura 
			if(is_null($this->_currentCulture))
			{
				//Tenta identificar a cultura necessária
				if(!$this->autoAssignCulture())
				{
					//Setar cultura padrão na sessão
					$this->setCulture($this->_getCultureConfig('default'));
					/*$this->_currentCulture = $this->_getCultureConfig('default');
					$this->_setSessionCulture($this->_currentCulture);*/
					$this->setSystemTimezone();
					$this->setSystemLanguage();
				}
			}			
			//Se já houver uma cultura instanciada, retornar
			return $this->_currentCulture;
		}

		/**
		* Altera a cultura padrão do momento e retorna todos os seus dados
		*
		* @param $culture string Código da cultura
		*
		* @return mixed
		*/
		public function setCulture ($culture, $clearCulture = true)
		{
			trace('Setting culture', 'Culture', $culture);

			//Se for enviada uma cultura, trocá-la
			if(isset($this->_getCultureConfig('cultures')[$culture]))
			{
				//Remover todos os dados de cultura
				if($clearCulture === true)
					$this->_clearAllCulture();

				$this->_currentCulture = $culture;
				$this->_setSessionCulture($this->_currentCulture);
				$this->setSystemTimezone();
				$this->setSystemLanguage();
				return $this->_currentCulture;
			}

			return false;
		}

		/**
		* Resgata a cultura padrão do momento e retorna todos os seus dados
		*
		* @param $culture string (Opcional) Código da cultura. Se não for passado, é retornada a cultura atual.
		*
		* @return mixed
		*/
		public function getCulture ($culture = null)
		{
			//Se não foi solicitada uma cultura específica, retornar a atual
			if(is_null($culture))
				return $this->_getCultureConfig('cultures')[$this->currentCulture()];

			//Retornar a cultura solicitada
			return $this->_getCultureConfig('cultures')[$culture];
		}

		/**
		* Resgata o timezone da cultura padrão do momento e retorna todos os seus dados
		*
		* @param $culture string (Opcional) Código da cultura. Se não for passado, é retornada a cultura atual.
		*
		* @return mixed
		*/
		public function getCultureTimezone ($culture = null)
		{
			//Retornar a cultura solicitada
			return $this->getCulture()['timezone'];
		}

		/**
		* Resgata a moeda da cultura padrão do momento e retorna todos os seus dados
		*
		* @param $culture string (Opcional) Código da cultura. Se não for passado, é retornada a cultura atual.
		*
		* @return mixed
		*/
		public function getCultureCurrency ($culture = null)
		{	
			//Se não foi solicitada uma cultura específica, retornar a atual
			if(is_null($culture))
				return $this->_getCultureConfig('cultures')[$this->currentCulture()]['currency'];

			//Retornar a cultura solicitada
			return $this->_getCultureConfig('cultures')[$culture]['currency'];
		}

		/**
		* Resgata a língua da cultura padrão do momento e retorna todos os seus dados
		*
		* @param $culture string (Opcional) Código da cultura. Se não for passado, é retornada a cultura atual.
		*
		* @return mixed
		*/
		public function getCultureLanguage ($culture = null)
		{
			//Se não foi solicitada uma cultura específica, retornar a atual
			if(is_null($culture))
				return $this->_getCultureConfig('cultures')[$this->currentCulture()]['language'];

			//Retornar a cultura solicitada
			return $this->_getCultureConfig('cultures')[$culture]['language'];
		}

		/**
		* Define a cultura principal baseada no cabeçalho Accept-Language
		*
		* @return mixed
		*/
		public function autoAssignCulture ($clearCulture = true)
		{
			trace('Auto discovering culture', 'Culture', null, TRACE_INFO);

			//Resgatar cabeçalhos
			$headers = in_array(context(), [CONTEXT_CONSOLE_CRON, CONTEXT_CONSOLE_BASH]) ? [] : $this->getHeaders();

			//Se não houver o parâmetro de línguas aceitas, abortar
			if(!isset($headers['Accept-Language']))
				return false;

			//Resgatar línguas 
			$acceptLanguages = explode(",", $headers['Accept-Language']);

			//Resgatar culturas
			$cultures = $this->_getCultureConfig('cultures');

			//Para cada língua solicitada
			foreach ($acceptLanguages as $acceptLanguage)
			{
				//Verificar se há disponibilidade da língua
				$language = $this->checkLanguageAvailability(explode(";", $acceptLanguage)[0]);
				
				//Se a língua solicitada estiver disponível, associar cultura
				if($language)
				{
					//Verificar em cada cultura qual possui a língua em questão
					foreach ($cultures as $cultureId => $culture)
						
						//Se a língua disponível estiver 
						if($culture['language'] == $language)
							return $this->setCulture($cultureId, $clearCulture);						

					//Se nenhuma cultura com essa língua foi encontrada, associa apenas a língua e encerra
					return $this->setLanguage($language);
				}
			}
			return false;
		}
	}