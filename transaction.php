<?php

	// error_reporting(0);
	// ini_set('display_errors', 0);
	
	require_once('main.php');

	trace("Starting transaction", "transaction");

	//Definindo o contexto a fim de exibir corretamente mensagens de erro
	context(CONTEXT_NAVIGATION_TRANSACTION);

	//Iniciar sessão
	if (session_id() == '' || session_status() == PHP_SESSION_NONE)
		session_start();			

	$transaction = new Navigation\JsonTransaction();

	trace("User", "transaction", $transaction->getIp());

	//Receber o nome do módulos/classe
	@$moduleName = isset($_GET['module']) ? $_GET['module'] : $transaction->outputError($transaction->write("Module hasn't been selected"), 400);

	//Substituir valores para aceitar namespaces
	$moduleName = preg_replace("/\\\\|\\//", "\\", $moduleName);

	//Cria o nome do arquivo
	$classFile = preg_replace("/\\\\|\\//", DIRECTORY_SEPARATOR, $moduleName);

	//Verifica se o módulo/classe chamado existe
	if(!file_exists(MAIN_FOLDER . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $classFile . '.php'))
		$transaction->outputError( $transaction->sprintWrite("The requested module '%s' doesn't exist", $moduleName), 404);
	
	//Receber a ação
	@$methodName = isset($_GET['method']) ? $_GET['method'] : $transaction->outputError( $transaction->write("Method hasn't been selected") , 400);

	trace("Instancing class", "transaction", $moduleName);

	//Instancia o módulo/classe solicitada
	$instance = new $moduleName();

	//Verifica se método existe dentro do módulo/classe
	if(!method_exists($instance, $methodName))
		$transaction->outputError( $transaction->sprintWrite("The method '%s' doesn't belongs to module '%s'", array($methodName, $moduleName)), 404);

	//Instanciar o módulo em uma Reflection Class
	$reflectionInstance = new ReflectionClass($moduleName);

	//Buscar o método solicitado
	$reflectionClass = $reflectionInstance->getMethod($methodName);

	//Verificar se o método é público
	if(!$reflectionClass->isPublic())
		$transaction->outputError( $transaction->sprintWrite("The method '%s' does not implements a valid model for transaction", array($methodName, $moduleName)), 404);

	//Resgatar parâmetros do método 
	$reflectionParameters = $reflectionClass->getParameters();

	//set_error_handler(function($a, $b, $c) use($transaction) { $transaction->setError($a, $b, $c); });

	//Avaliar se o método recebe uma variável com instância de Navigation\JsonTransaction
	if(count($reflectionParameters) > 1)
	{
		if($reflectionParameters[1]->getType()->getName() == "Navigation\\JsonTransaction")
		{
			trace("Calling method", "transaction", $methodName);
			
			//Resgatar dados da requisição
			$data = $transaction->getData();

			//Se o primeiro parâmetro solicitar uma classe, instanciar a classe e repassar
			if($reflectionParameters[0]->getType())
			{
				$dataClass = $reflectionParameters[0]->getType()->getName();
				$data = new $dataClass($data);
			}

			//Chamar a função
			$response = $instance->$methodName($data, $transaction);

			if(!is_null($response))
				$transaction->data($response);

			if(!$transaction->isOutputed())
				$transaction->output();

		}else{
			$transaction->outputError( $transaction->sprintWrite("The method '%s' does not implements a valid model for transaction", array($methodName, $moduleName)), 404);
		}
	}
	else
	{
		//Retornar erro caso o método não esteja no modelo necessário
		$transaction->outputError( $transaction->sprintWrite("The method '%s' does not implements a valid model for transaction", array($methodName, $moduleName)), 404);
	}

?>