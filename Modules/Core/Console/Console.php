<?php
/**
 * Módulo para interação com o console
 *
 * @package Console
 * @author Lucas/Postali
 */
	namespace Console;

	class Console extends \Core
	{
		private $arguments = array();
		private $_timeStart = null;
		/**
		* Construir classe
		*
		* @return null
		*/
		public function __construct ()
		{
			parent::__construct();

			$this->_timeStart = microtime(true);

			//Incluir mensagem de "Iniciando"
			$this->output($this->effect($this->write("Starting...", "console"), 'blue'));

			//Analisar os argumentos enviados
			$this->_parseArguments();

			//Chamar a função em questão
			$this->call();
		}

		/**
		* Exibir mensagem de finalização ao destruir a classe
		*
		* @return string
		*/
		public function __destruct ()
		{
			$executionTime = (microtime(true) - $this->_timeStart);
			$this->output($this->effect("\n" . $this->write("Finished. (Execution time: %s)", "console", $executionTime), 'blue'));
		}

		private $class;
		private $function;

		/**
		* Analisar os arquivos passados
		*
		* @return null
		*/
		private function _parseArguments ()
		{
			//Verificar se o primeiro parâmetro é uma classe:função
			if(!isset($GLOBALS['argv'][1]) || !preg_match("/^([^:\s]+)\:([^\s]+)$/", $GLOBALS['argv'][1], $match))
				$this->output($this->write("Malformed request. Expecting following information \"class:function\"", "console"), true);

			//Incluir nome da classe
			$this->class = preg_replace("/\\//", "\\", $match[1]);

			//Incluir nome da função
			$this->function = $match[2];

			//Para cada variável, adicionar à lista de variáveis
			foreach ($GLOBALS['argv'] as $key => $value) {
				if($key < 2)
					continue;
				$this->arguments[] = $value;
			}
		}

		/**
		* Chamar a função solicitada
		*
		* @return null
		*/
		private function call ()
		{
			//Verificar se a classe existe
			if(!class_exists($this->getClass()))
				$this->output($this->write("Class '%s' doesn't exists", "console", $this->getClass()), true);

			//Instanciá-la
			$instance = new $this->class;

			//Verificar se o método/função existem
			if(!method_exists($instance, $this->getFunction()))
				$this->output($this->write("Method '%s' doesn't exists in this class", "console", $this->getFunction()), true);

			//Informar que o método está sendo chamado
			$this->output($this->effect($this->write("Calling method", "console") . "\n", 'blue'));

			//Chamar o método/função em questão
			$result = $instance->{$this->function}($this);

			//Caso tenha retornado algum seultado, exibi-lo
			if($result)
				$this->output($result);
		}

		private $_outputRegistry = array();

		/**
		* Escreve dados no console
		*
		* @param mixed $data Recebe os dados a serem escritos
		* @param bool $error Caso TRUE, retorna a mensagem como erro e para o código
		*
		* @return null
		*/
		public function output ($data, $error = false)
		{
			//Se for erro, exibir como erro
			if($error)
			{
				$this->_outputRegistry[] = '[' . $this->write('ERROR', "console") . '] ' . $data . "\n";
				die($this->effect('[' . $this->write('ERROR', "console") . '] ', 'red') . $data . "\n");
			}
			
			//Exibir mensagem
			print_r($data);

			//Quebrar linha
			echo "\n";

			$this->_outputRegistry[] = $data . "\n";
		}

		/**
		 * Realiza um STRPAD unicode 
		 * 
		 * @param  string $str     String de entrada
		 * @param  int $pad_len Tamanho esperado
		 * @param  string $pad_str Valor a ser inserido
		 * @param  string $dir     Direção a ser inserida
		 * 
		 * @return String
		 */
		function str_pad_unicode ($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT)
		{
		    $str_len = mb_strlen($str);
		    $pad_str_len = mb_strlen($pad_str);
		    if (!$str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
		        $str_len = 1; // @debug
		    }
		    if (!$pad_len || !$pad_str_len || $pad_len <= $str_len) {
		        return $str;
		    }

		    $result = null;
		    if ($dir == STR_PAD_BOTH) {
		        $length = ($pad_len - $str_len) / 2;
		        $repeat = ceil($length / $pad_str_len);
		        $result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length))
		                . $str
		                . mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
		    } else {
		        $repeat = ceil($str_len - $pad_str_len + $pad_len);
		        if ($dir == STR_PAD_RIGHT) {
		            $result = $str . str_repeat($pad_str, $repeat);
		            $result = mb_substr($result, 0, $pad_len);
		        } else if ($dir == STR_PAD_LEFT) {
		            $result = str_repeat($pad_str, $repeat);
		            $result = mb_substr($result, 0, 
		                        $pad_len - (($str_len - $pad_str_len) + $pad_str_len))
		                    . $str;
		        }
		    }

		    return $result;
		}

		/**
		 * Escreve dados no console apagando a linha anterior
		 * 
		 * @param  string  $data  Informação a ser escrita
		 * @param  integer $lines (Opcional) Número de linhas anteriores a serem sobrescritas
		 * @param  boolean $end   (Opcional) Informa se deve ser colocado uma quebra de linha ao final
		 * 
		 * @return null
		 */
		public function outputOverwrite ($data, $lines = 1, $end = false)
		{
			$this->output("\033[0G" . $data . "\033[" . $lines . "A" . ($end ? "\n" : ''));
		}

		/**
		 * Escreve uma barra de porcentagem interativa
		 * 
		 * @param  [type] $from  Recebe um valor de porcentagem de 0 a 100 OU o valor inicial de um intervalo
		 * @param  [type] $to    (Opcional - ignorar se o valor enviado em $from é porcentagem) Valor final do intervalo
		 * @param  [type] $token (Opcional) Ícone de carregamento
		 * 
		 * @return null
		 */
		public function outputPercent ($from, $to = null, $token = null)
		{
			$token = $token ? $token : "▇";

			$p_total = !$to ? (($from / 100) * 30) : round(($from*30) / $to);

			$percent = $this->str_pad_unicode('', $p_total, $token);
			$percent = $this->str_pad_unicode($percent, 30, "_");


			$p_number = !$to ? round(($p_total * 100) / 30) : round(($from * 100) / $to);

			$percent = str_replace($token, $this->effect($token, "green"), $percent);
			$percent = str_replace("_",$token, $percent);			

			$output = "$percent";
			
			$output .= $this->effect(" ".$p_number."% ", "green");

			if($to) $output .= $from . "/" . $to;

			$this->outputOverwrite($output, 1, ($p_number == 100));
		}

		private $effectList = 
			[
				'black' 		=> '30',
				'red' 			=> '31',
				'green' 		=> '32',
				'yellow' 		=> '33',
				'blue' 			=> '34',
				'magenta' 		=> '35',
				'cyan'			=> '36',
				'white'			=> '37',
				'bg_black' 		=> '40',
				'bg_red' 		=> '41',
				'bg_green' 		=> '42',
				'bg_yellow' 	=> '43',
				'bg_blue' 		=> '44',
				'bg_magenta' 	=> '45',
				'bg_cyan'		=> '46',
				'bg_white'		=> '47',
				'reset' 		=> '0',  
				'bold_bright' 	=> '1',  
				'underline' 	=> '4',
				'inverse' 		=> '7', 
				'_bold_bright' 	=> '21',
				'_underline' 	=> '24',
				'_inverse' 		=> '27'
			];

		public function effect ($string, $effect)
		{
			return "\033[1;" . $this->effectList[$effect] . "m" . $string . "\033[0m";
		}

		/**
		* Retorna classe
		*
		* @return string
		*/
		public function getClass ()
		{
			return $this->class;
		}

		/**
		* Retorna função
		*
		* @return string
		*/
		public function getFunction ()
		{
			return $this->function;
		}

		/**
		* Retorna argumentos
		*
		* @param $position integer Posição de argumentos que se deseja receber. Se for ignorado, retorna todos os argumentos
		*
		* @return string
		*/
		public function getArguments ($position = null)
		{
			if(!is_null($position))
				if(isset($this->arguments[$position]))
					return $this->arguments[$position];
				else
					return false;
			return $this->arguments;
		}

		/**
		* Retorna se um argumento existe
		*
		* @param string $argument Nome do argumento
		*
		* @return bool
		*/
		public function getArgument ($argument)
		{
			foreach ($this->getArguments() as $value)
				if($value == $argument)
					return true;
			return false;
		}
	}

?>