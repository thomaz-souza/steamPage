<?php
/**
* Módulo padrão para todas as funções, responsável por carregar as classes e incluir funções globais
*
* @author 	Lucas/Postali
*/

	define('REQUIRED_PHP_VERSION', '7.1');

	define('MAIN_FOLDER', __DIR__);

	/**
	 * Nomraliza os separadores de um nome de arquivo
	 * @param string $file Nome do arquivo
	 * @return string
	 */
	function normalizeDirSeparator ($file)
	{
		return preg_replace("/\\/|\\\\/", DIRECTORY_SEPARATOR, $file);
	}

	require_once('utils.php');

	set_error_handler(
	    function ($errno, $errstr, $errfile, $errline) {
	        HandlerError($errno, $errstr, $errfile, $errline);     
	    }
	);

	set_exception_handler(
	    function ($err) {
	        HandlerException($err);     
	    }
	);

	//Função de carregamento automático das classes
	function _autoload ($class)
	{
		$folders = array(
			array('Modules','Custom'),
			array('Modules','Core'),
			array('Modules','Classes'),
			array('Controllers'),
			array('Modules','Traits'),
			array('vendor'),
			array('Modules','Libraries'),
			array('Modules','Interfaces'),
			array('Models')
		);	

		foreach($folders as $folder)
		{
			$file = MAIN_FOLDER . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $folder) . DIRECTORY_SEPARATOR . normalizeDirSeparator($class) . '.php';

			if(file_exists($file))
			{
				//trace("Loading class '$class' from file '$file'", 'main', $class, TRACE_LOW);
				require_once($file);
				return;
			}
		}
		
		trace("Class '$class' not found", 'main', $class, TRACE_ERROR);
		throw new Exception("Class '$class' not found", 1);
	}

	//Auto registrar classes
	spl_autoload_register('_autoload');

	$composerAutoloadFile = MAIN_FOLDER . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

	if(file_exists($composerAutoloadFile))
		require_once($composerAutoloadFile);
	else
		Error('The composer autoload is not installed. Run composer install on console');

	trace('System ready', 'main', MAIN_FOLDER, TRACE_LOW);

?>