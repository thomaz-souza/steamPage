<?php
/**
* Registro de logs e erros
*
* @package	Core/Utils
* @author 	Lucas/Postali
*/
	namespace Utils;

	trait Log
	{
		/**
		* [FUNÇÃO MOVIDA. PERMANECE PARA RETROCOMPATIBILIDADE]
		* Registra e exibe um erro fatal
		*
		* @param string $message Recebe a mensagem a ser inserida dentro do arquivo
		* @param boolean $showInFronted Enviar true caso queira exibir o erro na tela para o usuário
		* @return null
		*/
		public function Error ($message, $showInFronted = true, $frontendMessage = null, $details = null)
		{	
			Error($message, $showInFronted);
		}

		/**
		* Registra uma informação no log
		*
		* @param string $message Recebe a mensagem a ser inserida dentro do arquivo
		* @return null
		*/
		static public function Log ($message)
		{
			$content = (is_string($message) || is_numeric($message) || $message instanceof Fault) ? $message : var_export($message, true);

			writeLog($content);
		}
	}