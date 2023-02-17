<?php
/**
* Classe com funções principais
*
* @package	Core
* @author 	Lucas/Postali
*/
	class Core
	{
		/* ------------------------------------------------ UTILS */

		//Usar funções de log e erro
		use Utils\Log;

		//Usar funções simples de arquivos
		use Utils\Files;

		//Usar funções de arquivos de configuração
		use Utils\Config;

		//Usar funções úteis
		use Utils\Util;

		//Funções de falha
		use FaultTrait;

		/* ------------------------------------------------ MODULES */

		//Usar funções de cultura e internacionalização
		use Culture\Culture;

		//Funções de navegação e rotas
		use Navigation\Routes;

		function __construct (){}
		
	}

?>