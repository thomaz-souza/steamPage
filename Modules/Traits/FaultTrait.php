<?php
/**
* Geração de Faults
*
* @package	Traits
* @author 	Lucas/Postali
*/
	trait FaultTrait
	{
		//Lista de erros de validação
		static public $faultList = array (
			'mandatory' => "The mandatory field '%s' hasn't been sent",
			'type' => "The field '%s' value is in a unexpected format",
			'match' => "The field '%s' contains an unexpected value",
			'notMatch' => "The field '%s' contains an unexpected value",
			'minLength' => "The field '%s' has less characters than required",
			'maxLength' => "The field '%s' has more characters than required",			
			'minArrayCount' => "The field '%s' has less items than required",
			'maxArrayCount' => "The field '%s' has more items than required",
			'invalidMap' => "Validation map is invalid",
			'format' => "The file is in an unexpected format",
			'extension' => "The file is in an unexpected format",
			'maxSize' => "The file size must be smaller than %s",
			'minSize' => "The file size must be greater than %s",
			'maxSizeAll' => "The files upload size must be smaller than %s",
			'minSizeAll' => "The files upload size must be greater than %s",
			'maxDimension' => "The image dimensions must be up to %s pixels",
			'minDimension' => "The image dimensions must be at least %s pixels",
			'mutiple' => "Multiple files are not allowed",
			'maxMutiple' => "It is not allowed to upload more than %s file(s)",
			'minMutiple' => "It is required to be uploaded at least %s file(s)",
			'mandatoryFile' => "At least one file is expected",
			'uploadError' => "There was an error in the upload",
			'inArray' => "The field '%s' doesn't have a valid value",

			'noWritable' => "It is not possible to write in this folder",
			'noSpace' => "The folder has no enough space",
			'noFolder' => "The folder does not exist",
			'fileExists' => "The file '%s' already exists in the folder",
			'noValidExtension' => "The file '%s' has no valid extension",
			'unsuccessful' => "It was not possible to move the uploaded files",
			'notFoundAfterUpload' => "The file wasn't created successfully",
			'noCreatable' => "It was not possible to create the folder",

			'noValidImage' => "The file is not a valid image",
			'couldNotCreateImage' => "It was not possible to create the image",
			'fileDoesNotExist' => "The file does not exist",
			'biggerDimension' => "The new dimension is bigger than current image dimension",
			'invalidImageResource' => "The image is not a valid resource",
			'couldNotGenerateImageFile' => "It was not possible to generate and save the image",

			'invalidRecaptcha' => "Invalid Captcha. Try again",

			'userLoginNotFound' => "Wrong password or user not found",
			'userLoginWrongPassword' => "Wrong password or user not found",
			'addTagFile' => "The file you are trying to add in tag '%s' doesn't exist",
			'genericError' => "There has been an error. Please, try again later"
		);

		/**
		* Retorna erro na validação
		*
		* @param string $code Código do erro interno
		* @param string $fieldName Nome do campo
		* @param string $field Campo no qual o erro foi identificado
		* @param string $value Valor a ser colocado (no lugar de fieldname)
		* @param string $file Nome do arquivo que apresentou erro
		*
		* @return null
		*/
		public function _fault ($code, $value = null, $object = null, $details = null)
		{
			$message = $this->write(self::$faultList[$code], "validation", isset($value) ? $value : '');
			return new Fault($message, $code, $object, $details);
		}
	}

?>