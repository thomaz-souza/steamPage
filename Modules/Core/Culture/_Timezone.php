<?php
/**
* Função responsável pelos dados de Fuso Horário de cada cultura
*
* @package	Culture
* @author 	Lucas/Postali
*/
	namespace Culture;

	use \Datetime;

	trait _Timezone
	{

		/**
		* Registra o fuso horário selecionado o sistema
		*
		* @param $timezone string (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return string
		*/
		protected function setSystemTimezone ($timezone = null)
		{
			date_default_timezone_set($this->getTimezoneValue($timezone));
			return date_default_timezone_get();
		}

		/**
		* Limpa os dados de timezone
		*
		* @return mixed
		*/
		private function clearTimezone ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())
				unset($_SESSION['currentTimezone']);

			$this->_currentTimezone = null;
		}

		/**
		* Resgata a timezone salva na sessão
		*
		* @return mixed
		*/
		private function _getSessionTimezone ()
		{
			//Se houver uma sessão ativa
			if($this->hasSession())
			{
				//Se NÃO houver uma timezone atual
				if(!isset($_SESSION['currentTimezone']))
					return false;

				//Se houver uma timezone
				else
					return $_SESSION['currentTimezone'];
			}

			return false;
		}

		/**
		* Insere na sessão a timezone utilizada
		*
		* @param $timezone string Código da timezone
		*
		* @return mixed
		*/
		private function _setSessionTimezone ($timezone)
		{
			//Se houver uma sessão ativa, salvar
			if($this->hasSession())				
				return $_SESSION['currentTimezone'] = $timezone;

			return false;
		}

		private $_currentTimezone = null;

		/**
		* Define ou resgata a timezone padrão do momento.
		*
		* @return mixed
		*/
		public function currentTimezone ()
		{		
			//Retornar timezone da sessão, se houver
			$sessionTimezone = $this->_getSessionTimezone();
			if($sessionTimezone)
				return $sessionTimezone;
			
			//Se não houver timezone 
			if(is_null($this->_currentTimezone))
			{
				//Se não houver sessão, setar timezone padrão na sessão
				$this->_currentTimezone = $this->getCultureTimezone();
				$this->_setSessionTimezone($this->_currentTimezone);
			}

			//Se já houver uma timezone instanciada, retornar
			return $this->_currentTimezone;
		}

		/**
		* Altera a timezone padrão do momento e retorna todos os seus dados
		*
		* @param $timezone string Código da timezone
		*
		* @return mixed
		*/
		public function setTimezone ($timezone)
		{	
			//Se for enviada uma timezone, trocá-la
			if(isset($this->_getCultureConfig('timezones')[$timezone]))
			{
				$this->_currentTimezone = $timezone;
				$this->_setSessionTimezone($this->_currentTimezone);
				$this->setSystemTimezone($this->currentTimezone());
				return $this->currentTimezone();
			}

			return false;
		}

		/**
		* Resgata a timezone padrão do momento e retorna todos os seus dados
		*
		* @param $timezone string (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function getTimezone ($timezone = null)
		{	
			$timezones = $this->_getCultureConfig('timezones');

			//Se não foi solicitada uma timezone específica, retornar a atual
			if(is_null($timezone))
			{
				$current = $this->currentTimezone();
				
				//Se a atual estiver válida, retornar
				if(isset($timezones[$current]))
					return $timezones[$current];
					
				//Senão, retornar a timezone padrão
				return $timezones[$this->getCultureTimezone()];
			}

			//Retornar a timezone solicitada
			return $timezones[$timezone];
		}

		/**
		* Resgata o fuso horário selecionado
		*
		* @param $timezone string (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function getTimezoneValue ($timezone = null)
		{	
			return $this->getTimezone($timezone)['timezone'];
		}

		/**
		* Resgata o formato de data
		*
		* @param $timezone string (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function getTimezoneDateFormat ($timezone = null)
		{	
			return $this->getTimezone($timezone)['dateFormat'];
		}

		/**
		* Converter para a data no padrão da cultura atual
		*
		* @param string $date Data em formato padrão
		* @param string $timezone (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function convertToDate ($date, $timezone = null)
		{
			return date($this->getTimezoneDateFormat($timezone), strtotime($date));
		}

		/**
		* Converter da data no formato da cultura para formato padrão
		*
		* @param string $date Data em formato padrão
		* @param string $timezone (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function convertFromDate ($date, $timezone = null)
		{
			$dateTime = DateTime::createFromFormat($this->getTimezoneDateFormat($timezone), $date);
			return $dateTime->format('Y-m-d');
		}

		/**
		* Resgata o formato de hora
		*
		* @param $timezone string (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function getTimezoneTimeFormat ($timezone = null)
		{	
			return $this->getTimezone($timezone)['timeFormat'];
		}

		/**
		* Converter para a hora no padrão da cultura atual
		*
		* @param string $date Data em formato padrão
		* @param string $timezone (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function convertToTime ($date, $timezone = null)
		{
			return date($this->getTimezoneTimeFormat($timezone), strtotime($date));
		}

		/**
		* Converter da hora no formato da cultura para formato padrão
		*
		* @param string $date Data em formato padrão
		* @param string $timezone (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function convertFromTime ($date, $timezone = null)
		{
			$dateTime = DateTime::createFromFormat($this->getTimezoneTimeFormat($timezone), $date);
			return $dateTime->format('H:i:s');
		}

		/**
		* Resgata o formato de data e hora
		*
		* @param $timezone string (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function getTimezoneDateTimeFormat ($timezone = null)
		{	
			return $this->getTimezone($timezone)['dateTimeFormat'];
		}

		/**
		* Converter para a data e hora no padrão da cultura atual
		*
		* @param string $date Data em formato padrão
		* @param string $timezone (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function convertToDateTime ($date, $timezone = null)
		{
			return date($this->getTimezoneDateTimeFormat($timezone), strtotime($date));
		}

		/**
		* Converter da data e hora no formato da cultura para formato padrão
		*
		* @param string $date Data em formato padrão
		* @param string $timezone (Opcional) Código da timezone. Se não for passado, é retornada a timezone atual.
		*
		* @return mixed
		*/
		public function convertFromDateTime ($date, $timezone = null)
		{
			$dateTime = DateTime::createFromFormat($this->getTimezoneDateTimeFormat($timezone), $date);
			return $dateTime->format('Y-m-d H:i:s');
		}

	}