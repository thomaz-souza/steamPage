<?php
/**
* Classe para execução assíncrona
*
* @package		Core
* @author 		Lucas/Postali
*/

	namespace Async;

    use \Console\Console;

	class Execute extends \Core
	{
        /**
         * Executa um Async a partir de uma chamada do console
         * 
         * @param Console $console Objeto passado pelo console
         * 
         * @return null
         */
        public function execute (Console $console)
        {
            //Resgata a hash do arquivo
            $filename = $console->getArguments(0);

            //Baixa o arquivo e reabre a instância
            $file = $this->getPath("Var/$filename.async");            
            $instance = unserialize(file_get_contents($file));
            
            //Remove o arquivo
            unlink($file);

            //Executa
            return $instance->execute();
        }
    }