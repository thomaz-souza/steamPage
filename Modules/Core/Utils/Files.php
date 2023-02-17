<?php
/**
* Utilidades simples para arquivos
*
* @package	Core/Utils
* @author 	Lucas/Postali
*/
	namespace Utils;
	
	trait Files
	{
		/**
		* Avaliar se uma pasta é válida
		*
		* @param string $folder Recebe o caminho
		* @param boolean $create Se TRUE cria a pasta caso não exista
		* @param integer $chmod Permissão da pasta
		* @param integer $sizeNeeded Tamanho que a pasta deve ter
		*
		* @return mixed
		*/
		protected function _availFolder (string $folder, $create = false, $chmod = 0655, $sizeNeeded = null)
		{
			//Normalizar caminho da pasta
			$folder = $this->getPath($folder);

			//Verificar se a pasta escolhida existe
			if(!is_dir($folder))
			{
				//Se não existir, porém não criar, retornar erro
				if($create === false)
					return $this->_fault('noFolder', null, $folder);

				//Criar a página caso não exista
				$folderCreation = @mkdir($folder, $chmod);

				//Retornar erro se não foi possível criar a pasta
				if(!$folderCreation)
					return $this->_fault('noCreatable', null, $folder);
			}

			//Verificar se é possível escrever na pasta escolhida 
			if(!is_writable($folder))
				return $this->_fault('noWritable', null, $folder);

			//Avaliar se há espaço em disco para salvar os arquivos
			if($sizeNeeded !== null && $sizeNeeded > disk_free_space($folder))
				return $this->_fault('noSpace', null, $folder);

			return true;
		}

		/**
		* Obtém o caminho absoluto de uma pasta ou arquivo (sem barra no final)
		*
		* @param string $path Recebe o caminho
		*
		* @return string
		*/
		protected function getPath ($path)
		{

			//Retira barras extras
			//$path = preg_replace("/^\\\\|^\\//", '' , $path);

			//Altera a barra / pela barra correspondente ao sistema
			$path = preg_replace("/\\\\|\\//", DIRECTORY_SEPARATOR , $path);

			//Retira barras extras
			//$path = preg_replace("/^\\\\|^\\//", '' , $path);

			//Retirar caminho padrão do sistema, se houver
			$path = str_replace(MAIN_FOLDER, "", $path);

			//Inclui o caminho padrão
			$path = MAIN_FOLDER . DIRECTORY_SEPARATOR . $path;

			//Retira barras do final
			$path = preg_replace("/\\$|\/$/", "", $path);
			
			return $path;
		}
	}