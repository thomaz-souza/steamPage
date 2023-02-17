<?php
/**
* Objeto para agendamento de execução
*
* @package		Core
* @author 		Lucas/Postali
*/

	namespace Async;

	class Async extends \Core
	{
        /**
         * @var string Caminho completo do método
         */
        private $_method = null;

        /**
         * @var string Caminho completo do método de retorno
         */
        private $_callback = null;

        /**
         * @var array Argumentos a serem enviados
         */
        private $_arguments = [];

        /**
         * @var string Número do processo
         */
        private $_pid = null;

        /**
         * Nova instância 
         * 
         * @param   string    $method Caminho completo do método
         * @param   string    $callback Caminho completo do método de retorno
         * @param   array     $arguments Argumentos a serem passados
         * @param   bool      $start Indica se a função deve ser chamada imediatamente
         * 
         * @return int PID do chamado
         */
        function __construct($method, $callback = null, $arguments = null, $start = false)
        {
            $this->_method = $method;
            $this->_callback = $callback;
            $this->_arguments = $arguments ? $arguments : [];

            if($start)
                return $this->start();
        }

        /**
         * Cria uma nova instância 
         * 
         * @param   string    $method Caminho completo do método
         * @param   string    $callback Caminho completo do método de retorno
         * @param   array     $arguments Argumentos a serem passados
         * @param   bool      $start Indica se a função deve ser chamada imediatamente
         * 
         * @return Async
         */
        static public function open ($method, $callback = null, $arguments = null, $start = false)
        {
            $class = static::class;
			return new $class($method, $callback, $arguments, $start);
        }

        /**
         * Cria uma nova instância 
         * 
         * @param   string    $method Caminho completo do método
         * @param   string    $callback Caminho completo do método de retorno
         * @param   array     $arguments Argumentos a serem passados
         * 
         * @return int PID do chamado
         */
        static public function run ($method, $callback = null, $arguments = null)
        {
            $instance = self::open($method, $callback, $arguments);
            return $instance->start();
        }

        /** 
         * Resgata o ambiente atual
         * 
         * @param string $compare Consulta o ambiente
         * 
         * @return bool
         */
        private function _getENV ($compare = null)
        {
            if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                $env = 'windows';
            else
                $env = 'linux';

            return ($compare) ? $compare == $env : $env;
        }

        /** 
         * Resgata o Caminho do PHP para ambiente atual
         * 
         * @return string
         */
        private function _getPHPPath ()
        {
            $env = $this->_getENV();
            return $this->_config("system/async/$env/php_path", "php");
        }

        /** 
         * Resgata o Caminho de Dump para ambiente atual
         * 
         * @return string
         */
        private function _getDumpPath ()
        {
            $env = $this->_getENV();
            return $this->_config("system/async/$env/dump_path", " * >NUL 2>NUL");
        }

        /**
         * Inicia a função de maneira assíncrona
         * 
         * @return int PID do chamado
         */
        public function start ()
        {
            //Caminho do PHP
			$phpPath = escapeshellarg($this->_getPHPPath());

			//Caminho do executador
			$consolePath = escapeshellarg($this->getPath('console'));

            $dumpPath = $this->_getDumpPath();

            //Salva arquivo com funções serializadas
            $instance = serialize($this); 
            $hash = md5($instance) . uniqid(true);
            file_put_contents($this->getPath("Var/$hash.async"), $instance);
            
            //Prepara comando
			$command = "$phpPath $consolePath Async/Execute:execute $hash " . $dumpPath;

            //Executar de acordo com o tipo de sistema
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                $pipe = null;
                $process = proc_open('start /B cmd /C "'. $command .'"', array(), $pipe);
                $this->_pid = proc_get_status($process)['pid'];
                proc_close($process);
            }
            
            //Execução Linux
            else
            {
                $this->_pid = exec($command);
            }

            return $this->_pid;
        }

        /** 
         * Chama uma função dado seu caminho
         * 
         * @param string $method Caminho do método/função
         * @param array $arguments Argumentos a serem passados para a função
         * 
         * @return mixed Retorno da função executada
         */
        private function _call ($method, $arguments)
        {
            list($class, $method) = explode("::", $method);
            return call_user_func_array([new $class(), $method], $arguments);
        }

        /** 
         * Executa o documento atual
         * 
         * @param string $method Caminho do método/função
         * @param array $arguments Argumentos a serem passados para a função
         * 
         * @return null
         */
        public function execute()
        {
            $response = $this->_call($this->_method, $this->_arguments);

            //Se houver função de callback, chamá-la
            if($this->_callback)
                $this->_call($this->_callback, [$response]);
        }
    }