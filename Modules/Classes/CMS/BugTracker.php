<?php
/**
* Módulo de listagem de erros
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMS;

	class BugTracker extends \_Core
	{
		public function sideMenu (InterfaceCMS $interface)
		{
			$options = [
				"icon" => "fas fa-bug",
				"title" => $this->write("Bug tracker", 'admin'),
				"action" => array("CMS","BugTracker")
			];

			$interface->addSideMenu($options, 'cms-bug-tracker');
		}

		private $log;

		private function loadLog ()
		{
			$file = $this->getPath('Var/error-log.json');

			if(!file_exists($file))
				return false;

			$content = json_decode(file_get_contents($file), true);

			if(json_last_error())
				return false;

			$this->log = $content;
		}

		public function getLog ()
		{
			if(is_null($this->log))
				$this->loadLog();

			return $this->log;
		}

		public function countLog ()
		{
			$log = $this->getLog();

			if(!$log)
				return 0;

			$total = 0;

			foreach ($log as $year => $months)
				foreach ($months as $month => $days)				
					foreach ($days as $day => $registry)					
						$total += count($registry);
				
			return $total;
		}

		public function getParsedLog ()
		{
			$log = $this->getLog();

			if(!$log)
				return [];

			$lines = [];

			foreach ($log as $year => $months)
			{
				foreach ($months as $month => $days)
				{
					foreach ($days as $day => $registry)					
					{
						foreach ($registry as $line)
						{
							$line[] = "$day/$month/$year";
							$lines[] = $line;
						}						
					}
				}
			}
				
			return $lines;

		}
	}