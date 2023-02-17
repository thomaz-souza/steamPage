<?php
/**
* Funções de saída de dados
*
* @package		Navigation
* @author 		Lucas/Postali
*/
	namespace Navigation;
	
	trait _Output
	{
		/**
		 * @var bool Verificação sobre retorno dos dados
		 */
		protected $outputed = false;

		/**
		 * Informa se os dados já foram enviados
		 * 
		 * @return bool
		 */
		public function isOutputed ()
		{
			return $this->outputed;
		}

		/**
		* Exibe dados em JSON
		*
		* @param array $array Dados a serem encodados em JSON e exibidos
		* @return null
		*/
		public function outputJson ($array)
		{
			$this->outputed = true;
			
			//Incluir headers do JSON
			$this->includeHeadersArray(["Content-Type: application/json; charset=UTF-8", "Access-Control-Max-Age: 3600"]);

			//Exibir JSON e encerrar
			die(json_encode($array));
		}

		/**
		* Exibe dados em Binario
		*
		* @param string $content Conteúdo em binário
		* @param string $mimeType Tipo de Mime
		* @param string|bool $download Se true realiza download ao invés de exibir. Se for string, usa como nome do arquivo
		* 
		* @return null
		*/
		public function outputBinary ($content, $mimeType = null, $download = false)
		{
			$this->outputed = true;

			if($mimeType)
				$this->setContentType($mimeType);

			header("Content-Transfer-Encoding: Binary");
			header('Content-Length: ' . mb_strlen($content, '8bit'));

			if($download)
				header("Content-disposition: attachment; filename=\"" . $download . "\"");

			//Exibir Binário e encerrar
			die($content);
		}

		/**
		* Exibe arquivo
		*
		* @param string $file Caminho do arquivp
		* @param string|bool $download Se true realiza download ao invés de exibir. Se for string, usa como nome do arquivo
		* 
		* @return null
		*/
		public function outputFile ($file, $download = false, $mimeType = null)
		{
			//Carregar arquivo
			$file = $this->getPath($file);

			if(!file_exists($file))
				return $this->Error($this->write('File not found'));
			
			$fp = fopen($file, 'r+');
			$content = fread($fp, filesize($file));
			fclose($fp);
				
			//Detectar tipo mime
			$mimeType = empty($mimeType) ? mime_content_type($file) : $mimeType;

			//Se foi solicitado um download, ou for um arquivo não exibível
			if($download === true || ($download === false && $mimeType == 'application/octet-stream'))
				$download = basename($file);
			
			$this->outputBinary($content, $mimeType, $download);
		}
	}