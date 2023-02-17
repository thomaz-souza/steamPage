<?php
/**
* Função responsável pelos dados de Moeda de cada cultura
*
* @package	Culture
* @author 	Lucas/Postali
*/
	namespace Culture;

	trait _Currency
	{
		/**
		* Limpa os dados de moeda
		*
		* @return mixed
		*/
		private function clearCurrency ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())
				unset($_SESSION['currentCurrency']);

			$this->_currentCurrency = null;
		}

		/**
		* Resgata a moeda salva na sessão
		*
		* @return mixed
		*/
		private function _getSessionCurrency ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())

				//Se NÃO houver uma moeda atual
				if(!isset($_SESSION['currentCurrency']))
					return false;

				//Se houver uma moeda
				else
					return $_SESSION['currentCurrency'];

			return false;
		}

		/**
		* Insere na sessão a moeda utilizada
		*
		* @param $currency string Código da moeda
		*
		* @return mixed
		*/
		private function _setSessionCurrency ($currency)
		{
			//Se houver uma sessão ativa, salvar
			if($this->hasSession())				
				return $_SESSION['currentCurrency'] = $currency;

			return false;
		}

		private $_currentCurrency = null;

		/**
		* Define ou resgata a moeda padrão do momento.
		*
		* @return mixed
		*/
		public function currentCurrency ()
		{		
			//Retornar moeda da sessão, se houver
			$sessionCurrency = $this->_getSessionCurrency();
			if($sessionCurrency)
				return $sessionCurrency;
			
			//Se não houver moeda 
			if(is_null($this->_currentCurrency))
			{
				//Se não houver sessão, setar moeda padrão na sessão
				$this->_currentCurrency = $this->getCultureCurrency();
				$this->_setSessionCurrency($this->_currentCurrency);
			}

			//Se já houver uma moeda instanciada, retornar
			return $this->_currentCurrency;
		}

		/**
		* Altera a moeda padrão do momento e retorna todos os seus dados
		*
		* @param $currency string Código da moeda
		*
		* @return mixed
		*/
		public function setCurrency ($currency)
		{	
			//Se for enviada uma moeda, trocá-la
			if(isset($this->_getCultureConfig('currencies')[$currency]))
			{
				$this->_currentCurrency = $currency;
				$this->_setSessionCurrency($this->_currentCurrency);
				return $this->_currentCurrency;
			}

			return false;
		}

		/**
		* Resgata a moeda padrão do momento e retorna todos os seus dados
		*
		* @param $currency string (Opcional) Código da moeda. Se não for passado, é retornada a moeda atual.
		*
		* @return mixed
		*/
		public function getCurrency ($currency = null)
		{	
			//Se não foi solicitada uma moeda específica, retornar a atual
			if(is_null($currency))
				return $this->_getCultureConfig('currencies')[$this->currentCurrency()];

			//Retornar a moeda solicitada
			return $this->_getCultureConfig('currencies')[$currency];
		}

		/**
		* Retorna a marca de decimal
		*
		* @param string $currency Recebe código da moeda
		* @return string
		*/
		public function getCurrencyDecimalMark ($currency = null)
		{
			return $this->getCurrency($currency)['decimalMark'];
		}

		/**
		* Retorna a marca de milhar
		*
		* @param string $currency Recebe código da moeda
		* @return string
		*/
		public function getCurrencyThousandMark ($currency = null)
		{
			return $this->getCurrency($currency)['thousandMark'];
		}

		/**
		* Retorna a quantidade de casas decimais
		*
		* @param string $currency Recebe código da moeda
		* @return string
		*/
		public function getCurrencyDecimalPlaces ($currency = null)
		{
			return $this->getCurrency($currency)['decimalPlaces'];
		}

		/**
		* Retorna o símbolo da moeda
		*
		* @param string $currency Recebe código da moeda
		* @return string
		*/
		public function getCurrencySymbol ($currency = null)
		{
			return $this->getCurrency($currency)['symbol'];
		}


		/**
		* Retorna a posição do símbolo
		*
		* @param string $currency Recebe código da moeda
		* @return string
		*/
		public function getCurrencySymbolPlacement ($currency = null)
		{
			return $this->getCurrency($currency)['symbolPlacing'];
		}

		/**
		* Adiciona o símbolo da moeda ao número
		*
		* @param string $currency Recebe código da moeda
		* @return string
		*/
		public function addCurrencySymbol ($number, $currency = null)
		{
			$placement = $this->getCurrencySymbolPlacement($currency);
			$placement = str_replace("$", $this->getCurrencySymbol($currency), $placement);
			$placement = str_replace("#", $number, $placement);
			return $placement;
		}

		/**
		* Retorna um número configurado no formato da moeda selecionada
		*
		* @param float $number Número a ser convertido
		* @param bool $addSymbol (Opcional) Se true, adiciona o símbolo
		* @param string $currency Recebe código da moeda
		*
		* @return float
		*/
		public function numberToCurrency ($number, $addSymbol = true, $currency = null)
		{
			$formated = number_format($number,
				$this->getCurrencyDecimalPlaces($currency),
				$this->getCurrencyDecimalMark($currency),
				$this->getCurrencyThousandMark($currency));

			if($addSymbol === true)
				$formated = $this->addCurrencySymbol($formated, $currency);

			return $formated;
		}


		public function currencyToNumber ($number)
		{
			$number = preg_replace("/[^\d\.\,]+/", "", $number);
			$number = preg_replace("/\,/", ".", $number);
			return floatval($number);
		}

	}