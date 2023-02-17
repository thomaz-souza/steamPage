<?php
/**
 * Módulo de execuções do cron
 *
 * @package Cron
 * @author Lucas/Postali
 */
	namespace Cron;

	class Cron extends \Core
	{
		function __construct ()
		{
			context(CONTEXT_CONSOLE_CRON);
		}

		/**
		 * Lista todos os crons solicitados
		 * 
		 * @return array
		 */
		protected function _listJobs ()
		{
			return $this->_listConfig('cron');
		}

		/**
		 * Listar jobs agendados
		 *  
		 * @return null
		 */
		public function listJobs ($console)
		{
			$console->output($this->write('Listing cron tasks', 'console'));
			$console->output($this->write('Current date', 'console') . " " . $console->effect(implode(" ", $this->getCurrentDate()).PHP_EOL, "magenta"));

			foreach ($this->_listJobs() as $task)
			{
				//Caso não haja um método, pular
				if(!isset($task['method']) || !isset($task['frequency'])) //  && !isset($task['condition'])
					continue;

				//Consultar se a frequência solicitada corresponde
				$running = $this->checkFrequency($task['frequency']);

				$console->output($console->effect($task['frequency'], "magenta") . " " . $task['method'] . ' ' . ($running ? $console->effect('['.$this->write('Ready', 'console').']', "green") : ''));
			}
		}

		/**
		 * Retorna a data atual dividida
		 * 
		 * @return array
		 */
		public function getCurrentDate()
		{
			return explode("|", date('i|G|j|n|w|Y'));
		}

		/**
		 * Analisa se pode ser executado dada a frequência selecionada
		 * 
		 * @param string $frequency
		 * 
		 * @return bool
		 */
		protected function checkFrequency ($frequency)
		{
			/*
				0 - minuto
				1 - hora
				2 - dia
				3 - mês
				4 - dia da semana
				5 - ano
			*/

			//Dividir requisitos do mês
			$times = explode(" ", $frequency);

			//Resgatar data atual no mesmo formato
			$currentDate = $this->getCurrentDate();

			
			for($i = 0; $i<count($times); $i++)
			{
				//Caso seja "qualquer", pular
				if($times[$i] == "*")
					continue;

				//Se a data atual for compatível, pular
				if(@intval($times[$i]) == intval($currentDate[$i]))
					continue;

				//Verificar se foi solicitado um número multiplo (*/5) e checa se a data atual corresponde
				if(strpos($times[$i], "/") !== false)
					if($currentDate[$i] % intval(explode("/", $times[$i])[1]) == 0)
						continue;

				//Verificar se foi solicitado um intervalo de números (1-5) e checa se a data atual corresponde
				if(strpos($times[$i], "-") !== false)
				{
					$time = explode("-", $times[$i]);
					if(in_array(strval(intval($currentDate[$i])), range($time[0], $time[1])))
						continue;
				}

				//Resgatar todas os valores, se estiverem divididos por vírgulas
				$time = explode(",", $times[$i]);

				//Verificar se a data atual corresponde a algum dos valores selecionado
				if(in_array(strval(intval($currentDate[$i])), $time))
					continue;

				//Se não houve nenhuma correspondência, retornar falso
				return false;
			}

			return true;
		}

		/**
		 * Executa os crons solicitados
		 * 
		 * @param Console $console 
		 * 
		 * @return null
		 */
		public function execute ($console)
		{	
			trace('Starting cron tasks', 'Cron', implode(" ", $this->getCurrentDate()));

			$console->output($this->write('Starting cron tasks', 'console'));
			$console->output($this->write('Current date', 'console') . " " . $console->effect(implode(" ", $this->getCurrentDate()).PHP_EOL, "magenta"));

			foreach ($this->_listJobs() as $task)
			{
				//Caso não haja um método, pular
				if(!isset($task['method']) || !isset($task['frequency'])) //  && !isset($task['condition'])
					continue;

				//Consultar se a frequência solicitada corresponde
				if(!$this->checkFrequency($task['frequency']))
					continue;

				//Informar sobre a execução do método
				$console->output($console->effect("[" . $this->write("RUNNING", "console") . "]", "green") . " " . $task['method']);
					
				trace('Running: ' . $task['method'], 'Cron');

				//Rodar método
				$run = @$this->parseMethod($task['method'], true, $console);

				//Se retornou falso, avisar
				if($run === false)
					$console->output($this->write("Invalid method", "console"), true);

				//Se retornou algum dado, exibir
				if($run)
					$console->output("   " . $run);
				
			}

			trace("Finished cron tasks", 'Cron');

			$console->output(PHP_EOL . $this->write("Finished cron tasks", "console"));
		}
	}