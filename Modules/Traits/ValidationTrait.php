<?php
/**
* Função de validação
*
* @package	Traits
* @author 	Lucas/Postali
*/
	trait ValidationTrait
	{
		use FaultTrait;
		use FileTrait;

		/**
		* Valida se um array tem o formato esperado
		*
		* @param array $rulesMap Recebe um mapa de regras dos campos
		* @param array $data Recebe os dados a serem validados
		*
		* @return mixed
		*/
		public function validate ($rulesMap, &$data)
		{
			//Para cada uma das regras
			foreach( $rulesMap as $field => $options )
			{
				//Pegar o nome do campo em questão
				$fieldName = isset($options['name']) ? $options['name'] : $field;

				//Verificar se o campo é obrigatório
				if( isset($options['mandatory']) && !isset($data[$field]) )
					return $this->_fault('mandatory', $fieldName, $field);

				//Se o campo solicitado não existir, usar o valor padrão se existir
				if(!isset($data[$field]) && isset($options['defaultValue']) )
				{
					$data[$field] = $options['defaultValue'];
					continue;
				}
				//Se o campo solicitado não existir, ignorar
				else if(!isset($data[$field]))
				{
					continue;
				}

				//Se o campo contiver um arquivo, validá-lo
				if( isset($options['type']) && $options['type'] == 'file')
				{
					//Avaliar se o arquivo condiz com o mapa definido				
					$validationResult = $this->fileValidate($options, $data[$field]);

					//Retornar caso seja fault
					if($validationResult instanceof Fault)
						return $validationResult;

					continue;
				}

				//Verificar o tipo
				if( isset($options['type']) && gettype($data[$field]) != $options['type'] )
					return $this->_fault('type', $fieldName, $field);

				//Verificar se o campo é um array
				if( gettype($data[$field]) == 'array' )
				{
					//Verificar se o array tem a quantidade mínima de dados
					if( isset($options['minArrayCount']) && count($data[$field]) < $options['minArrayCount'] )
						return $this->_fault('minArrayCount', $fieldName, $field);

					//Verificar se o array tem a quantidade máxima de dados
					if( isset($options['maxArrayCount']) && count($data[$field]) > $options['maxArrayCount'] )
						return $this->_fault('maxArrayCount', $fieldName, $field);

					//Se houver um mapa de regras, validar os campos através do mapa
					if( isset($options['map']) )
					{
						if($this->is_associative($data[$field]))
						{
							$validationResult = $this->validate($options['map'], $data[$field]);
							if($validationResult !== true)
								return $validationResult;
						}else{
							foreach ($data[$field] as &$value)
							{
								$validationResult = $this->validate($options['map'], $value);
								if($validationResult !== true)
									return $validationResult;
							}							
						}
					}

					//Ignorar as próximas regras
					continue;
				}

				//Validar recaptcha
				if(isset($options['reCaptcha']) && $options['reCaptcha'] === true)
				{	
					//Instanciar recaptcha e validar
					$reCaptcha = new Google\ReCaptcha();
					$validateResult = $reCaptcha->validate($data[$field]);

					//Se for falso, retornar erro
					if($validateResult === false)
						return $this->_fault('invalidRecaptcha', $fieldName, $field);

					//OBS: Se retornar um Fault avisando que não há configuração, a validação será ignorada
				}

				//Se o valor enviado não for escalar (integer, float, string ou boolean)
				if( !is_scalar($data[$field]))
					continue;

				//Verifica se o valor está dentre os esperados
				if(isset($options['inArray']) && is_array($options['inArray']) && !in_array($data[$field], $options['inArray']))
					return $this->_fault('inArray', $fieldName, $field);
				
				//Se o valor enviado não for string, ignorar as próximas regras
				if( gettype($data[$field]) != 'string' )
					continue;

				//Executar o replace desejado
				if( isset($options['replaceBefore']) )
					$data[$field] = call_user_func_array('ReplaceLibrary::' . $options['replaceBefore'], array($data[$field]));

				//Verificar se o valor corresponde à expressão regular
				if( isset($options['match']) && !preg_match($options['match'], $data[$field]) )
					return $this->_fault('match', $fieldName, $field);

				//Verificar se o valor NÃO corresponde à expressão regular
				if( isset($options['notMatch']) && preg_match($options['notMatch'], $data[$field]) )
					return $this->_fault('notMatch', $fieldName, $field);

				//Verificar se o campo tem o tamanho mínimo
				if( isset($options['minLength']) && strlen($data[$field]) < $options['minLength'] )
					return $this->_fault('minLength', $fieldName, $field);

				//Verificar se o campo tem o tamanho máximo
				if( isset($options['maxLength']) && strlen($data[$field]) > $options['maxLength'] )
					return $this->_fault('maxLength', $fieldName, $field);

				//Incluir barras antes de aspas simples por segurança
				if( isset($options['addslashes']) && $options['addslashes'] === true )
					$data[$field] = addslashes($data[$field]);

				//Executar o replace desejado
				if( isset($options['replaceAfter']) )
					$data[$field] = call_user_func_array('ReplaceLibrary::' . $options['replaceAfter'], array($data[$field]));
			}
			return true;
		}

		/**
		* Valida se os arquivos têm o formato esperado
		*
		* @param array $rulesMap Recebe um mapa de regras dos campos
		* @param array $files Recebe os arquivos a serem validados
		*
		* @return mixed
		*/
		public function fileValidate ($rulesMap, $files)
		{		
			//Tenta recuperar o nome do campo
			$fileFieldName = isset($rulesMap['name']) ? $rulesMap['name'] : '';

			//Verifica se um arquivo é obrigatório e retorna erro caso não haja arquivos
			if(isset($rulesMap['mandatory']) && $rulesMap['mandatory'] === true && !isset($files['name']))
				return $this->_fault('mandatoryFile', null, $fileFieldName);

			//Verifica se um arquivo NÃO é obrigatório e retorna TRUE caso não haja arquivos
			if(isset($rulesMap['mandatory']) && $rulesMap['mandatory'] === false && !isset($files['name']))
				return true;

			//Caso não haja arquivos e não for obrigatório, retornar TRUE
			if(!isset($rulesMap['mandatory']) && !isset($files['name']))
				return true;

			//Verificar se há proibição de envio de múltiplos arquivos
			if(isset($rulesMap['multiple']) && $rulesMap['multiple'] === false && count($files['name']) > 1)
				return $this->_fault('mutiple', null, $fileFieldName);

			//Verificar se há número máximo de arquivos múltiplos
			if(isset($rulesMap['maxMultiple']) && count($files['name']) > $rulesMap['maxMultiple'])
				return $this->_fault('maxMutiple', $rulesMap['maxMultiple'], $fileFieldName);

			//Verificar se há número mínimo de arquivos múltiplos
			if(isset($rulesMap['minMultiple']) && count($files['name']) > $rulesMap['minMultiple'])
				return $this->_fault('minMutiple', $rulesMap['minMultiple'], $fileFieldName);

			//Variável que irá receber a soma de todos os arquivos
			$filesSizes = 0;

			//Normalizar arquivos
			$files = $this->_normalizeNonMultipleFiles($files);

			if($files instanceof Fault)
				return $files;

			//Para cada um dos arquivos
			for ($i=0; $i<count($files['name']); $i++)
			{	
				//Se o arquivo foi enviado com arquivo, retornar erro
				if($files['error'][$i] != 0)
					return $this->_fault('uploadError', null, $files['name'][$i], $files['error'][$i]);

				//Verificar se é exigido um formato de arquivo através do Mime
				if(isset($rulesMap['format']))

					//Verificar se foi enviado um (string) ou mais (array) formatos especificos
					if( (gettype($rulesMap['format']) == "array" && !in_array($files['type'][$i], $rulesMap['format'])) || (gettype($rulesMap['format']) != "array" && $files['type'][$i] != $rulesMap['format']))
						return $this->_fault('format', null, $files['name'][$i]);

				//Obter extensão do arquivo em questão
				preg_match("/\.([^\.]+)$/", $files['name'][$i], $fileInfo);

				//Verificar se é exigido uma extensão de arquivo
				if(isset($rulesMap['extension']) && isset($fileInfo[1]))

					//Verificar se foi enviado um (string) ou mais (array) extensões especificas
					if( 
						(gettype($rulesMap['extension']) == "array" && !in_array(strtolower($fileInfo[1]), $rulesMap['extension']))
						||
						(gettype($rulesMap['extension']) != "array" && strtolower($fileInfo[1]) != strtolower($rulesMap['extension']))
					)
						return $this->_fault('extension', null, $files['name'][$i]);

				//Retornar erro se o arquivo não tiver extensão
				if(isset($rulesMap['extension']) && !isset($fileInfo[1]))
					return $this->_fault('extension', null, $files['name'][$i]);

				//Somar o tamanho do arquivo
				$filesSizes += $files['size'][$i];

				//Verificar tamanho máximo do arquivo
				if(isset($rulesMap['maxSize']) && $files['size'][$i] > $rulesMap['maxSize'])
					return $this->_fault('maxSize', FileTrait::convertByte($rulesMap['maxSize']), $files['name'][$i], $fileFieldName);

				//Verificar tamanho mínimo do arquivo
				if(isset($rulesMap['minSize']) && $files['size'][$i] < $rulesMap['minSize'])
					return $this->_fault('minSize', null, FileTrait::convertByte($rulesMap['minSize']), $files['name'][$i], $fileFieldName);

				//Se foi solicitado dimensões da imagem
				if(isset($rulesMap['maxDimension']) || isset($rulesMap['minDimension']))
				{
					//Obter dimensões da imagem
					list($fileWidth, $fileHeight) = getimagesize($files['tmp_name'][$i], $fileFieldName);

					//Verificar se o WIDTH da imagem ultrapassa o limite definido
					if( isset($rulesMap['maxDimension']) && $rulesMap['maxDimension'][0] !== null && $rulesMap['maxDimension'][0] > 0 && $fileWidth > $rulesMap['maxDimension'][0])
						return $this->_fault('maxDimension', implode("x", $rulesMap['maxDimension']), $files['name'][$i]);

					//Verificar se o HEIGHT da imagem ultrapassa o limite definido
					if( isset($rulesMap['maxDimension']) && $rulesMap['maxDimension'][1] !== null && $rulesMap['maxDimension'][1] > 0 && $fileHeight > $rulesMap['maxDimension'][1])
						return $this->_fault('maxDimension', implode("x", $rulesMap['maxDimension']), $files['name'][$i]);

					//Verificar se o WIDTH da imagem é menor do que limite exigido
					if( isset($rulesMap['minDimension']) && $rulesMap['minDimension'][0] !== null && $rulesMap['minDimension'][0] > 0 && $fileWidth < $rulesMap['minDimension'][0])
						return $this->_fault('minDimension', implode("x", $rulesMap['minDimension']), $files['name'][$i]);

					//Verificar se o HEIGHT da imagem é menor do que limite exigido
					if( isset($rulesMap['minDimension']) && $rulesMap['minDimension'][1] !== null && $rulesMap['minDimension'][1] > 0 && $fileHeight < $rulesMap['minDimension'][1])
						return $this->_fault('minDimension', implode("x", $rulesMap['minDimension']), $files['name'][$i]);
				}
			}

			//Verificar se a soma do tamanho de todos os arquivos excede o limite estipulado
			if( isset($rulesMap['maxSizeAll']) && $filesSizes > $rulesMap['maxSizeAll'])
				return $this->_fault('maxSizeAll', FileTrait::convertByte($rulesMap['maxSizeAll']), $fileFieldName);

			//Verificar se a soma do tamanho de todos os arquivos fica abaixo do limite mínimo estipulado
			if( isset($rulesMap['minSizeAll']) && $filesSizes < $rulesMap['minSizeAll'])
				return $this->_fault('minSizeAll', FileTrait::convertByte($rulesMap['minSizeAll']), $fileFieldName);

			return true;			
		}
	}