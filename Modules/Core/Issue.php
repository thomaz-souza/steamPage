<?php
/**
* Classe para objeto de erro
*
* @package	Core
* @author 	Lucas/Postali
*/
	class Issue extends Exception
	{
		//Código do erro
		protected $code;
		
		//Mensagem do erro
		protected $message;

		//Linha do erro
		protected $line;

		//Arquivo do erro
		protected $file;

		//Módulo
		private $module;

		//Rastreamento do erro
		private $trace;

		//Data do erro
		private $datetime;

		//IP do usuário
		private $ip;

		//Função que gerou o erro
		private $function;

		//Caminho do arquivo de informações
		private $errorfile = null;

		/**
		* Atribuir dados ao instanciar
		*
		* @param string $message Mensagem de erro
		* @param string $code Código do erro
		*
		* @return null
		*/
		function __construct ($message = null, $code = null, $save = true)
		{
			$this->code = $code;
			$this->message = $message;
			$this->trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
			$this->module = isset($this->trace[1]['class']) ? $this->trace[1]['class'] : null;
			$this->function = isset($this->trace[1]['function']) ? $this->trace[1]['function'] : null;
			$this->datetime = date('r');
			$this->ip = _Core::getIP();

			if($save === true)
				$this->saveFile();

			return $this;
		}

		/**
		* Salvar arquivo
		*
		* @return null
		*/
		private function saveFile ()
		{
			$path = MAIN_FOLDER . DIRECTORY_SEPARATOR . 'Var' . DIRECTORY_SEPARATOR . 'Issue';

			if(!is_dir($path))
				if(!mkdir($path, 0655))
					return false;
			
			//Retornar falso se não for possível escrever na pasta
			if(!is_writable($path))
				return false;

			//Gerar nome do arquivo
			$filename = 'Issue ' . time() . ".html";

			//Registrar nome do arquivo de log
			$this->errorfile = $filename;

			//Salvar na pasta
			return file_put_contents( $path . DIRECTORY_SEPARATOR . $filename, $this->toHTML());
		}

		/**
		* Gerar uma visualização HTML do erro
		*
		* @return string
		*/
		public function toHTML ()
		{
			$string = 
				"<h3>$this->message [$this->code]</h3>" . 
				"<h4>Complete trace in ascending order of execution:</h4><ul>";

			foreach ( array_reverse($this->trace) as $step)
				$string .= "<li>File <strong>$step[file]</strong> at line <strong>$step[line]</strong> into function <strong>$step[function]</strong></li>";
			
			$string .=
				"</ul>" . 
				"<strong>Function:</strong> $this->function <br/>" .
				"<strong>Module:</strong> $this->module <br/>" .
				"<strong>Datetime:</strong> $this->datetime <br/>" .
				"<strong>Error file:</strong> $this->errorfile <br/>" .
				"<strong>User IP:</strong> $this->ip";

			return $string;
		}

		/**
		* Gerar uma visualização em texto do erro
		*
		* @return string
		*/
		public function toString ()
		{
			$string =
				"ISSUE: $this->message [$this->code]" . PHP_EOL . PHP_EOL .
				"Complete trace in ascending order of execution:" . PHP_EOL;

			foreach ( array_reverse($this->trace) as $step)
				$string .= "- File '$step[file]' at line '$step[line]' into function $step[function]" . PHP_EOL;
			
			return $string;
		}

		/**
		* Gerar uma visualização em Array do erro
		*
		* @return string
		*/
		public function toArray ()
		{
			return array(
				"code" => $this->code,
				"message" => $this->message,
				"line" => $this->line,
				"file" => $this->file,
				"module" => $this->module,
				"trace" => $this->trace,
				"datetime" => $this->datetime,
				"ip" => $this->ip,
				"function" => $this->function,
				'errorfile' => $this->errorfile
			);
		}

		/**
		* Gerar uma visualização em JSON do erro
		*
		* @return string
		*/
		public function toJSON ()
		{
			return json_encode($this->toArray());
		}

		/**
		* Retorna a string caso seja solicitado
		*
		* @return string
		*/
		public function  __toString ()
		{
			return $this->toString();
		}

		/**
		* Retornar variável chamada
		*
		* @return mixed
		*/
		public function __call ($name, $arguments)
		{
			$name = strtolower(preg_replace("/^get/", "", $name));
			if(isset($this->$name))
				return $this->$name;
			return '';
		}
	}

