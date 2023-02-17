<?php
	$GLOBALS['accessContext'] = null;

	define('CONTEXT_NAVIGATION_WEB', 11);
	define('CONTEXT_NAVIGATION_TRANSACTION', 12);
	define('CONTEXT_CONSOLE_BASH', 21);
	define('CONTEXT_CONSOLE_CRON', 22);

	/**
	 * Define o contexto em que o usuário está acessando o sistema
	 * 
	 * @param int $context Contexto atual
	 * 
	 * @return int
	 */
	function context ($context = null)
	{
		if(!empty($context))
			$GLOBALS['accessContext'] = $context;

		return $GLOBALS['accessContext'];
	}


	$GLOBALS['debugTrace'] = [];

	define('TRACE_LOW', 0);
	define('TRACE_INFO', 1);
	define('TRACE_WARNING', 2);
	define('TRACE_ERROR', 3);

	/**
	* Registra ou resgata o traço de programações para debug
	*
	* @param string $message (Opcional) Recebe a mensagem a ser inserida dentro do arquivo
	* @param string $module (Opcional) Nome do módulos
	* @param mixed $details (Opcional) Recebe detalhes extras
	* @param bool $level (Opcional) Nível do traço
	*
	* @return null
	*/
	function trace ($message = null, $module = null, $details = [], $level = 1)
	{
		if(is_null($message))
			return $GLOBALS['debugTrace'];

		$GLOBALS['debugTrace'][] = [
			'module' => $module,
			'message' => $message,
			'details' => $details,
			'time' => microtime(true),
			'level' => $level
		];
	}	

	/**
	 * Escreve o log de saída
	 * 
	 * @param string $content Conteúdo do arquivo
	 * @param string $file Nome do arquivo
	 * @param string $raw Escreve o arquivo sem intervenção
	 * 
	 * @return type
	 */
	function writeLog ($content, $file = null, $raw = false)
	{
		if($file === null)
			$file = "log-" . date('Y-m-d') . ".txt";

		//Coloca a pasta correta no arquivo
		$file = MAIN_FOLDER . DIRECTORY_SEPARATOR . "Var" . DIRECTORY_SEPARATOR . $file;

		//Adiciona a data e as quebras de linha
		if(!$raw)
			$content = PHP_EOL . date('d/m/Y H:i:s | ') . $content . PHP_EOL . '______________________________________' . PHP_EOL;

		//Abre o arquivo e escreve na última linha
		$fp = fopen($file, 'a+');
		fwrite($fp, $content);
		fclose($fp);
	}

	/**
	 * Retorna a mensagem padrão de erro já traduzida
	 * @return string
	 */
	function _getDefaultErrorMessage ()
	{
		$message = "There was an error loading this content";
		$translation = @Core::quickWrite($message);		
		return !$translation ? $message : $translation;
	}

	/**
	 * Exibe erros em formato de console 
	 * 
	 * @param int $errorType Tipo do erro
	 * @param string $message Mensagem de erro
	 * @param string $file (Opcional) Arquivo em que houve o erro
	 * @param int|string $lineNumber (Opcional) Número da linha do erro
	 * @param array $trace (Opcional) Trace do erro
	 * @param bool $discreet (Opcional) Informa se deve ser exibido discretamente o erro, ou verboso (caso true)
	 * 
	 * @return null
	 */
	function _ShowErrorConsole ($errorType, $message, $file = null, $lineNumber = null, $trace = [], $discreet = false)
	{
		echo 
			"\033[1;31m|\033[0m\n" .
			"\033[1;31m| ERROR - ".errorTypeConvert($errorType)."  \033[0m\n".
			"\033[1;31m|\033[0m\n" . 
			"\033[1;31m|\033[0m  \033[1;36m MESSAGE\033[0m {$message}\n".
			"\033[1;31m|\033[0m  \033[1;36m FILE\033[0m    {$file}\033[1;32m:{$lineNumber}\033[0m\n";

		if(!empty($trace))
			echo "\033[1;31m|\033[0m\n\033[1;31m| ERROR TRACE\033[0m\n\033[1;31m|\033[0m\n";

		foreach ($trace as $line)
		{
			$file = isset($line['file']) ? $line['file'] : '{closure}';
			$fileLine = isset($line['line']) ? "\033[1;32m:{$line['line']}\033[0m" : '';

			echo "\033[1;31m|\033[0m   {$file}{$fileLine} \033[1;34m{$line['function']}()\033[0m\n";
		}

		echo "\033[1;31m|\033[0m\n\033[1;31m| SYSTEM TRACE\033[0m\n\033[1;31m|\033[0m\n";

		foreach (trace() as $line)
		{
			$lineColorCode = "32";

			if($line['level'] == TRACE_WARNING)
				$lineColorCode = "33";

			else if($line['level'] == TRACE_ERROR)
				$lineColorCode = "35";

			echo "\033[1;31m|\033[0m   \033[1;{$lineColorCode}m{$line['module']}\033[0m {$line['message']}\n";
		}
			 
		die("\033[1;31m|\033[0m\n\033[1;31m|\033[0m");		
	}

	/**
	 * Exibe erros em formato Web
	 * 
	 * @param int $errorType Tipo do erro
	 * @param string $message Mensagem de erro
	 * @param string $file (Opcional) Arquivo em que houve o erro
	 * @param int|string $lineNumber (Opcional) Número da linha do erro
	 * @param array $trace (Opcional) Trace do erro
	 * @param bool $discreet (Opcional) Informa se deve ser exibido discretamente o erro, ou verboso (caso true)
	 * 
	 * @return null
	 */
	function _ShowErrorWeb ($errorType, $message, $file = null, $lineNumber = null, $trace = [], $discreet = false)
	{
		if($discreet === true)
		{
			if($errorType != ERROR_TYPE_SYSTEM)
				$message = _getDefaultErrorMessage();

			$data = '<!DOCTYPE html><html>
					<head>
						<title>Error</title>
						<meta http-equiv="X-UA-Compatible" content="IE=edge">
						<meta name="viewport" content="width=device-width, initial-scale=1.0">
						<meta name="charset" content="UTF-8">
						<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,900&display=swap">
						<style>
						html,body{ width: 100%; height: 100%; margin:0px auto; font-family: \'Nunito\', sans-serif;     background: linear-gradient(180deg, #F3F3F3, #FFFFFF);color:#444; }
						.main{ width: 100%; height: 100%; display:flex; position:relative; align-items: center; justify-content: center; }
						.content{ padding: 50px; }
						h1{text-align:center}
						p{font-size:17px;}
						.message{font-size:16px; border-top: 1px solid #E7E7E7; padding-top:20px; margin-top:20px; }
						</style>
					</head>
					<body>					
						<div class="main"><div class="content">
						<h1>'.$message.'</h1>
					</body>
					</html>';
			if(@ob_get_length()) @ob_end_clean();
			@header("Content-type: text/html");
			die($data);
		}

		$systemTrace = [];

		foreach (trace() as $lines)
		{
			$line = '<li>' .			
			'<p class="trace_function">' . $lines['module'] . '</p>'.
			'<p class="trace_level_' . $lines['level'] . '">' . $lines['message'] . '</p>'.
			'<p class="trace_file">' . var_export($lines['details'], true) . '</p>'.
			'</li>';

			$systemTrace[] = $line;
		}

		$errorTrace = [];

		foreach ($trace as $lines)
		{
			$args = [];
			if(isset($lines['args']))
			{
				foreach ($lines['args'] as $arg)
					$args[] = var_export($arg, true);				
			}

			$args = '<span class="trace_arg">' . implode('</span>,<br> <span class="trace_arg">', $args) . '</span>';

			$line = '<li><p class="trace_function">'.$lines['function'].' ('.$args.')</p>';

			if(isset($lines['file']))
				$line .= '<p class="trace_file">'.$lines['file'].' <span class="trace_line">'.$lines['line'].'</span></p>';

			$line .= '</li>';
			$errorTrace[] = $line;
		}

		$fileContent = "";

		if(!empty($file) && !empty($lineNumber))
		{
			$fileContent = file_get_contents($file);

			$fileContent = highlight_string($fileContent , true);
			$fileContent = explode("<br />", $fileContent);

			foreach ($fileContent as $num => &$lineContent)	
				$lineContent = '<span class="line_number' . ( $num + 1 == $lineNumber ? ' line_highlight' : '') . '">' . ($num + 1) .'</span>' . $lineContent;			

			$fileContent[$lineNumber-1] = '<div class="line_highlight">' . $fileContent[$lineNumber-1] . '</div>';
			$fileContent = implode("<br />", $fileContent);
		}

		$errorTypeTitle = errorTypeConvert($errorType);

		$data = '<!DOCTYPE html>
			<html>
			<head>
				<title>Error</title>
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<meta name="charset" content="UTF-8">
				<style>
					.trace_level_0{
						color:#333;
					}
					.trace_level_1{
						color:#333;
					}
					.trace_level_2{
						color:#e68a00;
					}
					.trace_level_3{
						color:#e60000;
					}
					.line_number{

					}
					body{
						position:relative;
						margin:0 auto;
						font-family: system,monospace;
						font-size:12px;
					}
					.file_content{
						position:relative;
						padding: 10px 0px;
						background-color: #F7F7F7;
						max-height: 300px;
						overflow: auto;
					}
					.trace_list{
						margin-left: 10px;
						padding-left: 10px;
						flex: 1 1;
						max-width:48%;
					}
					.trace_list:nth-child(2){
						border-left: 1px solid #DDD;
					}
					.trace_list ul{
						list-style:none;
						padding:0;
						overflow:auto;
					}
					.trace_list ul li{
						border-bottom:1px solid #E7E7E7;
						margin:0px;
						padding:5px;
					    min-height: 30px;
					    display: flex;
					    flex-direction: column;
					    justify-content: center;
					}
					.trace_list ul li p{
						margin:0px;
						padding: 2px;
					}
					.trace_function{
						font-weight:bold;
						color:#00994d;
					}
					.trace_file{
						font-style:italic;
						color:#999;
					}
					.trace_line{
						font-weight:bold;
						color:#71bd52;
						border: 1px solid #71bd52;
					    padding: 2px 4px;
					    border-radius: 5px;
					    font-size:11px;
					}
					.trace_arg{
						color:#333;
						font-weight:normal;
						padding:0px 5px;
					}
					.top_error{
						width:100%;
						padding:10px;
						background-color:#E7E7E7;
						box-sizing: border-box;
					}
					.top_error .trace_file{
						color:#666;
					}
					.top_error .trace_line{
						color:#71bd52;
						border-color:#71bd52;
					}
					.top_error h1{
						font-size:18px;
					}
					.top_error h2{
						font-size:15px;
					}
					.top_error h1,.top_error h2{
						margin:3px 0px;
					}
					.trace_blocks{
						display:flex;
					}
					.line_number{
						background-color: #CCC;
					    color: #666;
					    padding: 0px 6px;
					    margin-right: 6px;
					    min-width: 30px;
    					display: inline-block;
					}
					.line_highlight{
						background-color: #ffd3d3 !important;
						display:inline-block;						
						animation: glowline 1s ease infinite;
					}
					.line_number.line_highlight{
						background-color: #ea7878 !important;
    					color: #FFF;
						animation: glowline 1s ease infinite;
					}
					.error_type{
						font-variant: all-petite-caps;
						font-size:15px;
						padding:1px 10px;
						background-color:#;
						display:inline-block;
						border-radius:5px;
					}
					.error_type_1 .top_error{
						background-color:#ddefc9;
					}
					.error_type_2 .top_error{
						background-color:#f1eacc;
					}
					.error_type_3 .top_error{
						background-color:#f9d1d1;
					}
					.error_type_1 .error_type{
						background-color:#adca8d;
						color:#FFF;
					}
					.error_type_2 .error_type{
						background-color:#d0bc73;
						color:#FFF;
					}
					.error_type_3 .error_type{
						background-color:#d45346;
						color:#FFF;
					}
					.error_type_1 .line_highlight{
						background-color: #ddefc9 !important;
						display:inline-block;
					}
					.error_type_1 .line_number.line_highlight{
						background-color: #92b56b !important;
    					color: #FFF;
					}
					@keyframes glowline {
						0%{ filter: brightness(1) }
						50%{ filter: brightness(0.9) }
						100%{ filter: brightness(1) }
					}
				</style>
				<script>
					var systemTrace = '.json_encode(trace()).';
					var errorTrace = '.json_encode($trace).';
					console.log("System trace", systemTrace);
					console.log("Error trace", errorTrace);
					window.onload = function(){ 
						document.querySelector(".file_content").scrollTop = document.querySelector(".line_highlight").offsetTop - 150;
					}
				</script>
			</head>
			<body class="error_type_'.$errorType.'">
				<div class="top_error">
					<div class="error_type">'.$errorTypeTitle.'</div>
					<h1>'.$message.'</h1>
					<h2><span class="trace_file">'.$file.'</span> <span class="trace_line">'.$lineNumber.'</span></h2>
				</div>';

		$data .= '<div class="file_content">'.$fileContent.'</div><div class="trace_blocks">';

		if(!empty($errorTrace))
			$data .= '<div class="trace_list">
					<h3>Error trace</h3>
					<p>You can see detailed error trace in the console.</p>
					<ul>'. implode("", $errorTrace) . '</ul>
				</div>';

		if(!empty($systemTrace))
			$data .= '<div class="trace_list">
					<h3>System trace</h3>
					<p>You can see detailed error trace in the console.</p>
					<ul>'. implode("", $systemTrace) . '</ul>
				</div>';

					
		$data .= '</div>
		</body></html>';

		if(@ob_get_length()) @ob_end_clean();
		@header("Content-type: text/html");
		die($data);
	}

	/**
	 * Exibe erros em formato json
	 * 
	 * @param int $errorType Tipo do erro
	 * @param string $message Mensagem de erro
	 * @param string $file (Opcional) Arquivo em que houve o erro
	 * @param int|string $lineNumber (Opcional) Número da linha do erro
	 * @param array $trace (Opcional) Trace do erro
	 * @param bool $discreet (Opcional) Informa se deve ser exibido discretamente o erro, ou verboso (caso true)
	 * 
	 * @return null
	 */
	function _ShowErrorJson ($errorType, $message, $file = null, $lineNumber = null, $trace = [], $discreet = false)
	{
		$output = [
			'status' => 500,
			'error' => [
				'message' => $message
			]
		];

		if($discreet !== true)
		{
			$output['trace'] = trace();
			$output['error']['type'] = errorTypeConvert($errorType);
			$output['error']['line'] = $lineNumber;
			$output['error']['file'] = $file;
			$output['error']['trace'] = $trace;
		}
		else if($errorType != ERROR_TYPE_SYSTEM)
		{
			$output['error']['message'] = _getDefaultErrorMessage();
		}

		if(@ob_get_length()) @ob_end_clean();
		die(json_encode($output));
	}

	//Definição de constantes com os tipos de erros padrões
	define("ERROR_TYPE_SYSTEM", 1);
	define("ERROR_TYPE_EXCEPTION", 2);
	define("ERROR_TYPE_ERROR", 3);

	/**
	 * Converte o código erro para o formato escrito
	 * 
	 * @param int $errorCode Código do erro
	 * 
	 * @return string
	 */
	function errorTypeConvert ($errorCode)
	{
		$list = [
			ERROR_TYPE_SYSTEM => 'CONTROLLED ERROR',
			ERROR_TYPE_EXCEPTION => 'EXCEPTION',
			ERROR_TYPE_ERROR => 'ERROR'
		];

		return isset($list[$errorCode]) ? $list[$errorCode] : null;
	}

	/**
	 * Função que direciona corrtamente as mensagens de erro 
	 * 
	 * @param int $errorType Tipo do erro
	 * @param string $message Mensagem de erro
	 * @param string $file (Opcional) Arquivo em que houve o erro
	 * @param int|string $lineNumber (Opcional) Número da linha do erro
	 * @param array $trace (Opcional) Trace do erro
	 * @param bool $showMessage (Opcional) Define se a mensagem controlada poderá ser exibida para o usuário
	 * 
	 * @return null
	 */
	function _ErrorHandling ($errorType, $message, $file = null, $lineNumber = null, $trace = [], $showMessage = true)
	{
		//Reverter o trace para melhorar a visualização
		$trace = array_reverse($trace);

	
		//Simplificar todo o trace de erro para inserção no error.txt
		$simpleErrorTrace = [];
		foreach ($trace as $line)
			$simpleErrorTrace[] = (isset($line['file']) ? $line['file'] : '{closure}') . (isset($line['line']) ? (" :" . $line['line']): '') . " " . $line['function'] . "()";
		

		//Simplificar todo o trace do sistema para inserção no error.txt
		$simpleTrace = [];
		foreach (trace() as $line)
			$simpleTrace[] = "[{$line['module']}] {$line['message']} " . var_export($line['details'], true);

		//Criar conteúdo de log
		$content = errorTypeConvert($errorType) . " at context ".context()."\n\n{$message}\n{$file} :{$lineNumber}\n---\n" . implode(PHP_EOL, $simpleErrorTrace)."\n---\n" . implode(PHP_EOL, $simpleTrace);

		//Adicionar informação de erros no arquivo de error
		@writeLog($content, "error.txt");

		//Criar conteúdo para arquivo de erro json
		$errorLog = [
			'context' => context(),
			'date' => date('Y-m-d H:i:s'),
			'errorType' => $errorType, 
			'message' => $message, 
			'file' => $file, 
			'lineNumber' => $lineNumber,
			'trace' => trace(),
			'errorTrace' => $trace
		];

		//Salvar log
		@writeLog(json_encode($errorLog).",", "error.json", true);

		$discreet = !Core::isTraceEnabled();

		//Se o usuário não tem trace habilitado e a mensagem não deve ser exibida, alterar conteúdo da mensagem
		if($discreet && !$showMessage && !in_array(context(), [CONTEXT_CONSOLE_CRON, CONTEXT_CONSOLE_BASH]))
			$message = _getDefaultErrorMessage();

		if(in_array(context(), [CONTEXT_CONSOLE_CRON, CONTEXT_CONSOLE_BASH]))
			_ShowErrorConsole($errorType, $message, $file, $lineNumber, $trace, $discreet);

		else if(context() == CONTEXT_NAVIGATION_TRANSACTION)
			_ShowErrorJson($errorType, $message, $file, $lineNumber, $trace, $discreet);

		else
			_ShowErrorWeb($errorType, $message, $file, $lineNumber, $trace, $discreet);

		
	}

	/**
	 * Função pela qual o código pode chamar um erro controlado
	 * 
	 * @param string $message Mensagem
	 * 
	 * @return null
	 */
	function Error ($message, $showMessage = true)
	{
		$function = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		_ErrorHandling(ERROR_TYPE_SYSTEM, $message, $function[0]['file'], $function[0]['line'], debug_backtrace(), $showMessage); 
	}

	/**
	 * Manipulador de exceções 
	 * 
	 * @param Error $e objeto de exceção
	 * 
	 * @return null
	 */
	function HandlerException ($e)
	{
		_ErrorHandling(ERROR_TYPE_EXCEPTION, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
	}

	/**
	 * Manipulador de erros
	 * 
	 * @param string $errno Número/Código do erro
	 * @param string $errstr Mensagem de erro
	 * @param string $errfile Arquivo em que houve o erro
	 * @param string $errline Número da linha do erro
	 * 
	 * @return null
	 */
	function HandlerError ($errno, $errstr, $errfile, $errline)
	{
		_ErrorHandling(ERROR_TYPE_ERROR, $errstr , $errfile, $errline, debug_backtrace());
	}