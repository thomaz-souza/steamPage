<?php
/**
* Utilidades simples para strings e textos
*
* @package	Core/Utils
* @author 	Lucas/Postali
*/
	namespace Utils;

	trait Util
	{
		/**
		* Normalizar string, retirando caracteres especiais
		*
		* @param string $string Recebe a string a ser normalizada
		*
		* @return string
		*/
		static public function normalizeString ($string, $url = true)
		{
			$table = array(
		        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c',
		        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
		        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
		        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
		        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
		        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'dj', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
		        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
		        'ÿ'=>'y', 'R'=>'R', 'r'=>'r'
		    );
		    //Substituir caracteres especiais
		    $string = strtr($string, $table);

		    //Retirar demais caracteres
		    $string = preg_replace("/[^A-z0-9\.-_\s]+/", "", $string);

		    if($url === true)
		    {
			    //Substituir espaços por traços
			    $string = preg_replace("/[\s]+/", "-", $string);

			    //Transformar texto em minúsculo
			    $string = mb_strtolower($string,'UTF-8');
			}

		    return $string;		
		}

		/**
		* Converte nome para camelCase
		*
		* @param string $string Recebe a string a ser convertida
		*
		* @return string
		*/
		static public function convertToCamelCase ($string)
		{
			$string = self::normalizeString($string);

			$string = preg_replace_callback("/[-_]+([a-z]{1})/", function($a)
				{
					return ucfirst($a[1]);
				}, $string);

			return $string;
		}

		/**
		* Verifica se um array é associativo
		*
		* @param array $array Array para verificação
		*
		* @return boolean
		*/
		public function is_associative (array $array)
		{
			return array_keys($array) !== range(0, count($array) - 1) ? true : false;
		}

		/**
		 * Terceiriza dados a uma função, permitindo que funções "externas" realizem interações num objeto
		 * 
		 * @param string $config Nome da configuração aceita
		 * @param mixed $params Parâmetros
		 * @param mixed $params2 Parâmetros (utilizado apenas uma vez caso $returnAsParam seja true)
		 * @param bool $returnAsParam Se true, envia o retorno do método anterior para o próximo método como segundo parâmetro
		 * 
		 * @return mixed
		 */
		public function outsource ($config, $params, $params2 = null, $returnAsParam = false)
		{
			$return = null;

			foreach ($this->_listConfig($config) as $method)
			{
				$return = $this->parseMethod($method, true, $params, $params2);
				
				//Se foi solicitado que o retorno fosse um parâmetro, utilizá-lo
				if($returnAsParam === true)
					$params2 = $return;
			}

			//Retorna o último
			return $return;
		}

		/**
		* Interpreta caminho de método/classe
		*
		* @param string $path Caminho do método/classe
		* @param mixed $params (Opcional) Parâmetro a ser enviado. Se true, apenas executa a função
		*
		* @return mixed
		*/
		protected function parseMethod ($path, $execute, $params = null, $params2 = null)
		{
			$path = explode("::", $path);

			//Verifica se o caminho tem exatamente uma classe e uma função
			if(count($path) != 2)
				return false;

			//Resgatar classe com namespaces corrigido
			$class = preg_replace("/\/|\\\\/", "/", $path[0]);
			$class = preg_replace("/\//", "\\", $class);

			//Nome do método/função
			$method = $path[1];

			//Verifica se a classe existe
			if(!class_exists($class))
				return false;

			//Instancia a classe solicitada
			$instance = new $class();

			//Verifica se o método existe
			if(!method_exists($instance, $method))
				return false;

			if($execute)
			{
				if($params || $params2)
					return @$instance->$method($params, $params2);
				return @$instance->$method();
			}

			return ['class' => $instance, 'method' => $method];
		}

		/**
		* Verifica se há sessão
		*
		* @return bool
		*/
		public function hasSession ()
		{
			//Checar se está em modo terminar
			if (php_sapi_name() == 'cli')
				return false;			
				
			return session_status() === PHP_SESSION_ACTIVE;
		}

		/**
		* Transforma um array em CSV
		* 
		* @param array $array Array com dados
		* @param array|bool $titles (Opcional) Títulos ou false para não incluir os títulos
		*
		* @return string
		*/
		public function arrayToCSV ($array, $titles = [])
		{
			$csv = "";

			if(!empty($array))
			{
				if(empty($titles) && $titles !== false && $this->is_associative($array[0]))
					$titles = array_keys($array[0]);
				
				if($titles !== false)
					array_unshift($array, $titles);

				foreach ($array as $values)
				{
					foreach ($values as $value)
						$csv .= '"' . addslashes($value) . '";';				

					$csv .= "\n";
				}
			}

			return $csv;
		}

		/**
		* Verifica se o trace está habilitado
		*
		* @return bool
		*/
		static public function isTraceEnabled ()
		{
			$core = new \Core();
			$config = $core->_config('navigation');
			if(!isset($config['trace']))
				return false;

			$trace = $config['trace'];

			if($trace === true)
				return true;

			//Caso seja um IP, normalizar
			if(gettype($trace) == "string")
				$trace = [$trace];

			//Verificar se os IPs permitidos correspondem ao IP do usuário atual
			if(gettype($trace) == "array" && in_array($core->getIP(), $trace))
				return true;

			return false;
		}
	}