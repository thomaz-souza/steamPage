<?php
/**
* Manipulação de arquivo
*
* @package	Traits
* @author 	Lucas/Postali
*/
trait FileTrait
{
		/**
		* Converter bytes em outras unidades automaticamente
		*
		* @param mixed $bytes Quantidade de bytes
		* @param integer $precision Precisão do arredondamento
		*
		* @return mixed
		*/
		static public function convertByte ($bytes, $precision = 2)
		{
			$units = array('B', 'KB', 'MB', 'GB', 'TB');
			$bytes = max($bytes, 0); 
			$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
			$pow = min($pow, count($units) - 1);
			$bytes /= pow(1024, $pow);
			return round($bytes, $precision) . $units[$pow]; 
		}

		/**
		* Renomeia os arquivos de acordo com o método escolhido
		*
		* @param array $files Recebe os arquivos vindos da requisiçao
		* @param string $method Método para processar o nome
		*
		* @return mixed
		*/
		public function renameUploadFiles (&$files, $method)
		{
			for ($i=0; $i<count($files['name']); $i++)
			{
				//Obter extensão do arquivo em questão
				if(!preg_match("/\.([^\.]+)$/", $files['name'][$i], $fileInfo))
					return $this->_fault('noValidExtension', $files['name'][$i], $files['name'][$i]);
				$ext = $fileInfo[0];

				//Nomear arquivo de acordo com o método
				switch ($method) {

					//ID único
					case 'uniqid':
						$files['name'][$i] = uniqid(true) . $ext;
					break;

					//ID único
					case 'md5_file_time':
						$files['name'][$i] = md5_file($files['tmp_name'][$i]) . md5(time()) . $ext;
					break;

					//Hash MD5 do arquivo
					case 'md5_file':
						$files['name'][$i] = md5_file($files['tmp_name'][$i]) . $ext;
					break;

					//Hash MD5 da data/hora
					case 'md5_time':
						$files['name'][$i] = md5(time()) . $ext;
					break;

					//Data YYYY-MM-DD
					case 'date':
						$files['name'][$i] = date('Y-m-d') . $ext;
					break;

					//Data YYYYMMDD
					case 'serial_date':
						$files['name'][$i] = date('Ymd') . $ext;
					break;

					//Data e hora YYYY-MM-DD_HH-MM-SS
					case 'date_time':
						$files['name'][$i] = date('Y-m-d_H-i-s') . $ext;
					break;

					//Data e hora YYYYMMDDHHMMSS
					case 'serial_date_time':
						$files['name'][$i] = date('YmdHis') . $ext;
					break;

					//Apenas normalizar o nome
					case 'name':
						$files['name'][$i] = $this->normalizeString($files['name'][$i]);
					break;
					
					//Nome normalizado + data 
					case 'name_date':
						$files['name'][$i] = date('Y-m-d') . "-". $this->normalizeString($files['name'][$i]);
					break;

					//Nome normalizado + data e hora
					case 'name_date_time':
						$files['name'][$i] = date('Y-m-d_H-i-s') . "-". $this->normalizeString($files['name'][$i]);
					break;

					//Nome normalizado + data (sequenciado)
					case 'name_serial_date':
						$files['name'][$i] = date('Ymd') . "-". $this->normalizeString($files['name'][$i]);
					break;

					//Nome normalizado + data e hora (sequenciado)
					case 'name_serial_date_time':
						$files['name'][$i] = date('YmdHis') . "-". $this->normalizeString($files['name'][$i]);
					break;
					
					default:
						$files['name'][$i] = date('Y-m-d_H-i-s') . "-". $this->normalizeString($files['name'][$i]);
					break;
				}

			}
			return true;
		}

		/**
		* Normalizar os arquivos para implementar suporte a upload de arquivos múltiplos e únicos
		*
		* @param array $files Arquivos vindos da requisição
		*
		* @return array
		*/
		protected function _normalizeNonMultipleFiles ($files)
		{
			if(!is_array($files['name']))
				foreach ($files as $k => &$value)
					$files[$k] = array($value);

				return $files;
			}

		/**
		* Subir arquivos
		*
		* @param string $folder Pasta na qual serão salvos os arquivos
		* @param array $files Arquivos vindos da requisição
		* @param string $i Índice do arquivo a subir
		* @param boolean $overwrite Caso true, permite sobrescrever arquivos com nomes iguais
		* @param boolean $ignoreOnOverwrite Caso true, continua seguindo sem retornar erro caso haja um arquivo com nome duplicado
		*
		* @return array
		*/
		private function _upload ($folder, &$files, $i, $overwrite, $ignoreOnOverwrite)
		{
			//Montar o caminho
			$filePath = $this->getPath($folder . "/" . $files['name'][$i]);

			//Verificar se o arquivo já existe e retornar erro caso o arquivo não possa ser sobrescrito
			if($ignoreOnOverwrite === false && $overwrite === false && file_exists($filePath))
				return $this->_fault('fileExists', $files['name'][$i]);

			//Verificar se o arquivo já existe. Caso o arquivo não possa ser sobrescrito, apenas ignorar o upload
			if($ignoreOnOverwrite === true && $overwrite === false && file_exists($filePath))
				return true;

			//Mover arquivo e retornar erro em caso de problema
			if(!move_uploaded_file($files['tmp_name'][$i], $filePath))
				return $this->_fault('unsuccessful', null, $files['name'][$i]);

			//Re-checar se o arquivo foi enviado com sucesso
			if(!file_exists($filePath))
				return $this->_fault('notFoundAfterUpload', null, $files['name'][$i]);

			return true;
		}

		private function _parseFiles ($folder, $files, $method)
		{
			//Normalizar arquivos
			$files = $this->_normalizeNonMultipleFiles($files);

			//Calcular tamanho de todos os arquivos
			$totalSize = array_sum($files['size']);

			//Avaliar se a pasta é válida
			$availFolder = $this->_availFolder($folder, false, null, $totalSize);
			if($availFolder !== true)
				return $availFolder;

			//Renomear arquivos
			if($method !== false)
			{
				$renameResult = $this->renameUploadFiles($files, $method);
			
				//Caso a renomeação tenha retornado erro
				if($renameResult !== true)
					return $renameResult;
			}

			return $files;
		}

		/**
		* Salvar arquivos sistematicamente
		*
		* @param string $folder Pasta na qual serão salvos os arquivos
		* @param array $files Arquivos vindos da requisição
		* @param string $method Método para processar o nome
		* @param boolean $overwrite Caso true, permite sobrescrever arquivos com nomes iguais
		* @param boolean $ignoreOnOverwrite Caso true, continua seguindo sem retornar erro caso haja um arquivo com nome duplicado
		*
		* @return mixed
		*/
		public function saveUploadFiles ($folder, &$files, $method = false, $overwrite = false, $ignoreOnOverwrite = false)
		{	
			//Parsear arquivos
			$files = $this->_parseFiles($folder, $files, $method);
			
			//Se não estiver correto, retornar
			if(!is_array($files))
				return $files;

			//Lista de arquivos enviados
			$uploadedFiles = [];

			//Para cada arquivo enviado
			for ($i=0; $i<count($files['name']); $i++)
			{
				$upload = $this->_upload($folder, $files, $i, $overwrite, $ignoreOnOverwrite);
				if($upload !== true)
					return $upload;

				$uploadedFiles[] = $files['name'][$i];
			}

			//Se foi enviado apenas um arquivo, retornar string
			if(count($uploadedFiles) == 1)
				$uploadedFiles = end($uploadedFiles);

			return $uploadedFiles;
		}


		public function saveUploadImages ($map, $folder, &$files, $method = false, $overwrite = false, $ignoreOnOverwrite = false)
		{
			//Parsear arquivos
			$files = $this->_parseFiles($folder, $files, $method);

			//Se não estiver correto, retornar
			if(!is_array($files))
				return $files;

			$uploadList = array();

			//Para cada arquivo enviado
			for ($i=0; $i<count($files['name']); $i++)
			{
				//Se houver um mapa de imagem executar um batch
					$upload = $this->batchImage($files['tmp_name'][$i], isset($map['image']) ? $map['image'] : $map, $folder, $files['name'][$i]);

				//Caso haja um erro, retornar
				if($upload instanceof Fault)
					return $upload;
				
				//Adicionar à lista
				$uploadList[] = $upload;
			}

			return $uploadList;
		}
	}

?>