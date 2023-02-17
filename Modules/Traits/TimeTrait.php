<?php
/**
* Funções de Tempo
*
* @package	Traits
* @author 	Lucas/Postali
*/
	trait TimeTrait
	{	
		private function _serializeDate ($date)
		{
			return date('Ymd', strtotime($date));
		}

		private function _serializeTime ($date)
		{
			return date('His', strtotime($date));
		}

		private function _serializeDateTime ($date)
		{
			return date('YmdHis', strtotime($date));
		}

		private function _autoAttributeDateTime (&$date1, &$date2)
		{
			$date1 = ($date1 !== null) ? $date1 : $this->_todayDateTime();
			$date2 = ($date2 !== null) ? $date2 : $this->_todayDateTime();
		}

		// private function _todayDate ()
		// {
		// 	return date('Y-m-d');
		// }

		// private function _todayTime ()
		// {
		// 	return date('H:i:s');
		// }

		private function _todayDateTime ()
		{
			return date('Y-m-d H:i:s');
		}

		public function dateTimeGreater ($date1 = null, $date2 = null)
		{
			$this->_autoAttributeDateTime($date1, $date2);
			return $this->_serializeDateTime($date1) > $this->_serializeDateTime($date2);
		}

		public function dateGreater ($date1 = null, $date2 = null)
		{
			$this->_autoAttributeDateTime($date1, $date2);
			return $this->_serializeDate($date1) > $this->_serializeDate($date2);
		}

		public function dateTimeGreaterEqual ($date1 = null, $date2 = null)
		{
			$this->_autoAttributeDateTime($date1, $date2);
			return $this->_serializeDateTime($date1) >= $this->_serializeDateTime($date2);
		}

		public function dateGreaterEqual ($date1 = null, $date2 = null)
		{
			$this->_autoAttributeDateTime($date1, $date2);
			return $this->_serializeDate($date1) >= $this->_serializeDate($date2);
		}

		public function dateDiff ($fromDate = null, $toDate = null)
		{
			$this->_autoAttributeDateTime($fromDate, $toDate);			
			return date_diff(date_create($toDate), date_create($fromDate));
		}

		/**
		* Converte data para o formato da cultura/língua atual
		*
		* @param string $date Valor da data
		* @param string (Opcional) $language Recebe código da língua
		*
		* @return string
		*/
		public function convertToCultureDate ($date, $language = null)
		{
			$format = $this->getLanguageDatetimeFormat($language);

			$date = new DateTime($date);
			return $date->format($format);
		}

		/**
		* Converte data para do formato da cultura/língua atual para formato padrão
		*
		* @param string $date Valor da data
		* @param string (Opcional) $language Recebe código da língua
		*
		* @return string
		*/
		public function convertFromCultureDate ($date, $language = null)
		{
			$format = $this->getLanguageDatetimeFormat($language);

			$newDate = DateTime::createFromFormat($format, $date);
			return $newDate->format('Y-m-d H:i:s');
		}
	}

?>