<?php
	require_once('main.php');

	//Iniciar sessão
	if (session_id() == '' || session_status() == PHP_SESSION_NONE)
		trace('Starting session', 'web', session_start(), TRACE_LOW);

	trace('Starting web module', 'web', '', TRACE_LOW);
	
	//Definindo o contexto a fim de exibir corretamente mensagens de erro
	context(CONTEXT_NAVIGATION_WEB);

	$nav = new Navigation\Navigation();

	trace("User", "web", $nav->getIp());

	$page = $nav->getCurrentURIRoute();

	die($page->render());