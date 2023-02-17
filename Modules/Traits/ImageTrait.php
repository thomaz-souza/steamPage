<?php
/**
* Manipulação de Imagens
*
* @package	Traits
* @author 	Lucas/Postali
*/
Trait ImageTrait
{
		/**
		 * Ajusta a orientação da imagem de acordo com os dados de orientação do arquivo de imagem
		 * 
		 * @param  string 		$imagePath     Caminho da imagem
		 * @param  resource 	$imageResource Recurso de imagem
		 * 
		 * @return resource
		 */
		public function adjustImageOrientation ($imagePath, $imageResource)
		{
			$exif = exif_read_data($imagePath);

			if(isset($exif['Orientation']))
			{
				$deg = null;

				switch ($exif['Orientation'])
				{
					case 3:
						$deg = 180;
					break;

					case 6:
						$deg = 270;
					break;

					case 8:
						$deg = 90;
					break;
				}

				if($deg)
					return imagerotate($imageResource, $deg, 0);
			}

			return $imageResource;
		}

		/**
		* Carrega e cria a imagem
		*
		* @param string $code Código do erro interno
		* @param string $values Recebe o valor a ser colocado na mensagem de erro
		* @param string $values Campo no qual o erro foi identificado
		*
		* @return resource
		*/
		protected function _loadCreateImage (string $imagePath, $normalize = true)
		{
			//Normalizar caminho
			if($normalize === true)
				$imagePath = $this->getPath($imagePath);

			//Checa se o arquivo existe
			if(!file_exists($imagePath))
				return $this->_fault('fileDoesNotExist', null, $imagePath);

			//Obter tamanhos das imagens
			$getImageInfo = @getimagesize($imagePath);

			//Checar se foi possível carregar a imagem corretamente
			if(!$getImageInfo)
				return $this->_fault('noValidImage', null, $imagePath); 

			//Buscar por tipo de imagem
			switch ($getImageInfo['mime'])
			{
				case 'image/gif':
				@$image = imagecreatefromgif($imagePath);
				break;

				case 'image/jpeg':
				@$image = imagecreatefromjpeg($imagePath);
				break;

				case 'image/png':
				@$image = imagecreatefrompng($imagePath);
				break;

				case 'image/bmp':
				@$image = imagecreatefrombmp($imagePath);
				break;

				case 'image/webp':
				@$image = imagecreatefromwebp($imagePath);
				break;

				default:
				return $this->_fault('noValidImage', null, $imagePath);
				break;
			}

			//Se a criação da imagem não ocorreu corretamente, retornar erro
			if(!$image)
				return $this->_fault('couldNotCreateImage', null, $imagePath);

			$image = $this->adjustImageOrientation($imagePath, $image);

			//Retornar imagem
			return $image;
		}

		/**
		* Retorna um array com Largura e Altura da imagem em recurso
		*
		* @param resource $image Recurso de imagem a ser medido
		*
		* @return array
		*/
		protected function _getImageResourceSize ($image)
		{
			//Retorna os tamanhos
			return array(
				imagesx($image),
				imagesy($image),
			);
		}

		/**
		* Calcula a dimensão proporcional da imagem
		*
		* @param array $imageSize Largura e Altura atual da imagem
		* @param array $newSize Novos Largura e Altura
		* @param boolean $forceIncreaseSize Se TRUE permite que a imagem seja ampliada se necessário
		*
		* @return array
		*/
		protected function _calculateProportionImage (array $imageSize, array $newSize, $forceIncreaseSize = false)
		{
			//Calcular razão
			$ratio = $imageSize[0] / $imageSize[1];
			
			//Se a largura (width) for maior que a altura (height)
			if($ratio > 1)
			{
				$rate = $newSize[0];

				if ($forceIncreaseSize === false && ($imageSize[0] < $rate || $imageSize[1] < ($rate / $ratio)))
					return $imageSize;

				$width = $rate;
				$height = $rate / $ratio;
			}
			//Se a altura (height) for maior que a largura (width)
			else
			{
				$rate = $newSize[1];

				if ($forceIncreaseSize === false && ($imageSize[1] < $rate || $imageSize[0] < ($rate * $ratio)))
					return $imageSize;

				$height = $rate;
				$width = $rate * $ratio;			    
			}

			return array($width, $height);
		}

		/**
		* Cria um novo Canvas para imagem
		*
		* @param array $dimension Lagura e Altura do canvas desejado
		*
		* @return object
		*/
		protected function _createCanvasImage (array $dimension, $transparent = false)
		{
			//Criar novo Canvas
			$canvas = imagecreatetruecolor($dimension[0], $dimension[1]);

			//Habilitar transparência
			imagealphablending($canvas, true);

			//Salvar canal alpha
			imagesavealpha($canvas, false);

			//Gerar e definir cor transparente
			$bgcolor = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
			imagefill($canvas, 0, 0, $bgcolor);

			$background = imagecolorallocate($canvas, 0, 0, 0);

			//if($transparent)
			//	ImageColorTransparent($canvas, $background);

			//Retornar canvas pronto
			return $canvas;
		}

		/**
		* Salvar uma imagem
		*
		* @param object $image Imagem carregada
		* @param string $path Caminho + Nome do arquivo
		* @param integer $quality Qualidade da imagem de 0 a 100
		*
		* @return mixed
		*/
		protected function _saveImage ($image, string $path, $quality = null)
		{
			//Checa se a imagem é válida
			if(!is_resource($image))
				if($image instanceof Fault)
					return $image;
				else
					return $this->_fault('invalidImageResource');

			//Normalizar caminho do arquivo
				$path = $this->getPath($path);

			//Buscar informações do caminho
				$pathinfo = pathinfo($path);

			//Avaliar se a pasta é válida
				$availFolder = $this->_availFolder($pathinfo['dirname']);
				if($availFolder !== true)
					return $availFolder;

			//Se a qualidade não foi definida, definí-la como máxima
				if($quality === null)
					$quality = 100;

				switch ($pathinfo['extension'])
				{
					case 'png':
					$quality =  round(9 * ($quality/100));
					$save = imagepng($image, $path, $quality);
					break;

					case 'gif':
					$save = imagegif($image, $path);
					break;

					case 'bmp':
					$save = imagebmp($image, $path);
					break;

					case 'webp':
					$save = imagewebp($image, $path, $quality);
					break;

					case 'wbmp':
					$save = imagewbmp($image, $path);
					break;

					case 'jpg':
					$save = imagejpeg($image, $path, $quality);
					break;

					case 'jpeg':
					$save = imagejpeg($image, $path, $quality);
					break;		
					
					default:
					return $this->_fault('noValidExtension', $pathinfo['basename'], $pathinfo['basename']);
					break;
				}

			//Se não foi possível salvar, retornar erro
				if(!$save)
					return $this->_fault('couldNotGenerateImageFile', null, $pathinfo['basename']);

				return true;
			}

		/**
		* Converter formato de uma imagem
		*
		* @param string $imagePath Caminho da imagem a ser convertida
		* @param string $newImagePath Caminho a ser salva a nova imagem (deve receber a extensão desejada)
		* @param integer $quality Qualidade da imagem de 0 a 100
		*
		* @return mixed
		*/
		public function convertImage (string $imagePath, string $newImagePath, $quality = null)
		{
			//Carregar imagem
			$image = $this->_loadCreateImage($imagePath);

			//Se retornou um erro, retorná-lo para não seguir o código
			if($image instanceof Fault)
				return $image;

			//Salvar nova imagem
			return $this->_saveImage($image, $newImagePath, $quality);
		}

		/**
		* Método para redimensionar uma imagem
		*
		* @param object $image Imagem carregada
		* @param mixed $dimension Lagura e Altura desejadas / Porcentagem para redimensionamento
		* @param array $forceStretch Se TRUE força a imagem a ficar no tamanho definido
		* @param array $forceIncreaseSize Se TRUE permite que a imagem seja ampliada se necessário
		*
		* @return resource
		*/
		protected function _resizeImage ($image, $dimension, $forceStretch = false, $forceIncreaseSize = false)
		{
			//Obter tamanho atual da imagem
			$currentDimensions = array(
				imagesx($image), //Width
				imagesy($image) //Height
			);

			//Se o valor de 'dimensão' for um número, calculá-lo como porcentagem
			if(is_integer($dimension))
			{
				$rate = $currentDimensions[0] * ($dimension / 100);
				$dimension = array($rate, $rate);
			}

			//Se for obrigatório 'estender' a imagem, usar novos tamanhos
			if($forceStretch === true)
				$newDimensions = $dimension;
			
			//Se os tamanhos forem 'máximos', calcular proporção
			else
				$newDimensions = $this->_calculateProportionImage($currentDimensions, $dimension, $forceIncreaseSize);

			//Criar nova Prancheta
			$canvas = $this->_createCanvasImage($newDimensions);

			//Copiar imagem redimensionada para nova Prancheta
			imageCopyResampled($canvas, $image, 0, 0, 0, 0, $newDimensions[0], $newDimensions[1], $currentDimensions[0], $currentDimensions[1]);

			return $canvas;
		}

		/**
		* Redimensionar uma imagem
		*
		* @param string $imagePath Caminho da imagem a ser convertida
		* @param mixed $dimension Lagura e Altura desejadas / Porcentagem para redimensionamento
		* @param string $newImagePath Caminho a ser salva a nova imagem (deve receber a extensão desejada)
		* @param array $forceStretch Se TRUE força a imagem a ficar no tamanho definido
		* @param array $forceIncreaseSize Se TRUE permite que a imagem seja ampliada se necessário
		* @param integer $quality Qualidade da imagem de 0 a 100
		*
		* @return mixed
		*/
		public function resizeImage (string $imagePath, $dimension, string $newImagePath = null, $forceStretch = false, $forceIncreaseSize = false, $quality = null)
		{
			//Se não foi passado o novo caminho, sobrescrever imagem
			if($newImagePath === null)
				$newImagePath = $imagePath;

			//Carregar imagem
			$image = $this->_loadCreateImage($imagePath);

			//Se retornou um erro, retorná-lo para não seguir o código
			if($image instanceof Fault)
				return $image;

			//Redimensionar
			$resized = $this->_resizeImage($image, $dimension, $forceStretch, $forceIncreaseSize);

			//Se retornou um erro, retorná-lo para não seguir o código
			if($resized instanceof Fault)
				return $resized;

			//Salvar nova imagem
			return $this->_saveImage($resized, $newImagePath, $quality);
		}

		/**
		* Calcula a posição de uma imagem dentro de outra
		*
		* @param integer $sizeBase Tamanho da base
		* @param integer $sizeImage Tamanho do elemento a ser centralizado
		* @param string $position Posição a ser tomada
		*
		* @return integer
		*/
		protected function _calculatePositionImage ($sizeBase, $sizeImage, string $position)
		{
			//Calcular posicionamento ao centro
			if($position == 'center')
				return ($this->_getMaxSide([$sizeBase, $sizeImage]) - $this->_getMinSide([$sizeBase, $sizeImage])) / 2;
			//Calcular posicionamento à direita ou fundo
			else if($position == 'right' || $position == 'bottom')
				return $this->_getMaxSide([$sizeBase, $sizeImage]) - $this->_getMinSide([$sizeBase, $sizeImage]);

			//Se não foi definido nenhum dos acima, retornar posição inicial (esquerda/topo)
			return 0;
		}

		/**
		* Recurso para fundir imagens
		*
		* @param integer $imageBase Recurso da imagem base
		* @param integer $imageOver Recurso da imagem de sobreposição
		* @param mixed $dimension Dimensão que a imagem de sobreposição irá ocupar
		* @param array $position Posição da sobreposição na base. Pode conter um valor numérico, 'center', 'right', 'left', 'top' ou 'bottom'
		* @param integer $opacity Opacidade do elemento. Evitar para arquivos PNG com transparência
		*
		* @return integer
		*/
		protected function _mergeImage ($imageBase, $imageOver, $dimension = 50, $position = array('center','center'), $opacity = 100)
		{
			//Checa se a Imagem Base é válida
			if(!is_resource($imageBase))
				if($imageBase instanceof Fault)
					return $imageBase;
				else
					return $this->_fault('invalidImageResource');

			//Checa se a Imagem de Sobreposição é válida
				if(!is_resource($imageOver))
					if($imageOver instanceof Fault)
						return $imageOver;
					else
						return $this->_fault('invalidImageResource');


			//Resgatar tamanho da Imagem Base
					$imageBaseSizes = $this->_getImageResourceSize($imageBase);

			//Criar nova Prancheta
					$canvas = $this->_createCanvasImage($imageBaseSizes);

			//Copiar Imagem Base para a nova Prancheta
					imagecopyresampled($canvas, $imageBase, 0, 0, 0, 0, $imageBaseSizes[0], $imageBaseSizes[1], $imageBaseSizes[0], $imageBaseSizes[1]);

			//Redimensionar a Imagem de Sobreposição

					$imageOverResized = ($dimension > 0) ? $this->_resizeImage($imageOver, $dimension, false, true) : $imageOver;

					$imageOverResizedSizes = $this->_getImageResourceSize($imageOverResized);

			//Se a posição enviada for uma string, calcular posição
					if(is_string($position[0]))
						$position[0] = $this->_calculatePositionImage( $imageBaseSizes[0], $imageOverResizedSizes[0], $position[0] );

			//Se a posição enviada for uma string, calcular posição
					if(is_string($position[1]))
						$position[1] = $this->_calculatePositionImage( $imageBaseSizes[1], $imageOverResizedSizes[1], $position[1] );

			//Se a opacidade foi alterada, realizar um imageCopyMerge
					if(is_integer($opacity) && $opacity < 100)
					{
						imagecopymerge(
					$canvas,					//Canvas
					$imageOverResized,			//Imagem de Sobreposição
					$position[0],				//Posição Horizontal da Imagem de Sobreposição
					$position[1],				//Posição Vertical da Imagem de Sobreposição
					0, 							//Posição Horizontal da Imagem Base / Canvas
					0, 							//Posição Vertical da Imagem Base / Canvas
					$imageOverResizedSizes[0],	//Largura da Imagem de Sobreposição
					$imageOverResizedSizes[1],	//Altura da Imagem de Sobreposição
					$opacity 					//Opacidade da Imagem
				);
					}
			//Se a opacidade NÃO foi alterada, realizar um imageCopy (que preseva a transparência do PNG)
					else
					{
				imagecopy($canvas,				//Canvas
					$imageOverResized,			//Imagem de Sobreposição
					$position[0],				//Posição Horizontal da Imagem de Sobreposição
					$position[1],				//Posição Vertical da Imagem de Sobreposição
					0, 							//Posição Horizontal da Imagem Base / Canvas
					0, 							//Posição Vertical da Imagem Base / Canvas
					$imageOverResizedSizes[0],	//Largura da Imagem de Sobreposição
					$imageOverResizedSizes[1]	//Altura da Imagem de Sobreposição
				);
			}

			$this->newImage = $canvas;
			return $this->newImage;
		}

		/**
		* Fundir imagens
		*
		* @param integer $imageBasePath Caminho da imagem base
		* @param integer $imageOverPath Caminho da imagem de sobreposição
		* @param integer $newPath Caminho onde a nova imagem será salva
		* @param integer $quality Qualidade da imagem de 0 a 100
		* @param mixed $dimension Array com Lagura e Altura desejadas / Número de porcentagem para redimensionamento
		* @param array $position Array com posição da sobreposição na base. Pode conter um valor numérico, 'center', 'right', 'left', 'top' ou 'bottom'
		* @param integer $opacity Opacidade do elemento. Evitar para arquivos PNG com transparência
		*
		* @return integer
		*/
		public function mergeImage (string $imageBasePath, string $imageOverPath, string $newPath = null, $quality = null, $dimension = 50, $position = array('center','center'), $opacity = 100)
		{
			//Carregar imagem
			$imageBase = $this->_loadCreateImage($imageBasePath);

			//Se retornou um erro, retorná-lo para não seguir o código
			if($imageBase instanceof Fault)
				return $imageBase;

			//Carregar imagem
			$imageOver = $this->_loadCreateImage($imageOverPath);

			//Se retornou um erro, retorná-lo para não seguir o código
			if($imageOver instanceof Fault)
				return $imageOver;

			//Fundir imagens
			$mergedImage = $this->_mergeImage($imageBase, $imageOver, $dimension, $position, $opacity);

			//Se retornou um erro, retorná-lo para não seguir o código
			if($mergedImage instanceof Fault)
				return $mergedImage;

			//Se não foi enviado um novo caminho, sobrescrever imagem
			if($newPath === null)
				$newPath = $imageBasePath;

			return $this->_saveImage($mergedImage, $newPath, $quality);
		}

		private function _getMaxSide ( $array )
		{
			$max = 0;
			foreach( $array as $k => $v )
				$max = max(array($max,$v));		    
			return $max;
		}

		private function _getMinSide ( $array )
		{
			$min = pow(999,999);
			foreach( $array as $k => $v )
				$min = min(array($min,$v));		    
			return $min;
		}

		private function _cropImage ($imageBase, $newDimension = null, $position = array('center','center'))
		{
			$dimensions = $this->_getImageResourceSize($imageBase);

			if($newDimension === null)
			{
				$minSide = $this->_getMinSide($dimensions);
				$newDimension = array($minSide, $minSide);
			}

			if(is_string($position[0]))
				$position[0] = $this->_calculatePositionImage($newDimension[0], $dimensions[0], $position[0]);

			if(is_string($position[1]))
				$position[1] = $this->_calculatePositionImage($newDimension[1], $dimensions[1], $position[1]);

			return imagecrop($imageBase, ['x' => $position[0], 'y' => $position[1], 'width' => $newDimension[0], 'height' => $newDimension[1]]);
		}

		private $imageTempPath = "Var/";

		/**
		* Cortar em círuclo
		*
		* @param integer $imageBasePath Caminho da imagem base
		* @param integer $newPath Caminho onde a nova imagem será salva
		* @param integer $quality Qualidade da imagem de 0 a 100
		*
		* @return null
		*/
		public function cropCircle (string $imageBasePath, string $newPath, $quality = null)
		{
			//Carregar imagem
			$imageBase = $this->_loadCreateImage($imageBasePath);

			//Dimensões da imagem
			$dimensions = $this->_getImageResourceSize($imageBase);

			//Pegar o menor lado
			$minSide = $this->_getMinSide($dimensions);

			//Criar as dimensões da nova imagem
			$canvasDimension = array($minSide, $minSide);

			//Cortar a imagem de base e salvar
			$cropedImage = $this->_cropImage($imageBase, $canvasDimension);
			$temp_cropedFile = $this->getPath($this->imageTempPath . "/image_temp_" . time() . ".png");
			$this->_saveImage($cropedImage, $temp_cropedFile, 100);

			//Carregar a imagem cortada
			$imageBase = $this->_loadCreateImage($temp_cropedFile);

			//Apagar a imagem temporária
			unlink($temp_cropedFile);

			//Criar a máscara de recorte
			$maskCanvas = $this->_createCanvasImage($canvasDimension);

			//Alocar cor transparente na máscara
			$transparent = imagecolorallocate($maskCanvas, 255, 0, 0);
			imagecolortransparent($maskCanvas, $transparent);

			//Criar a elipse transparente
			imagefilledellipse($maskCanvas, $minSide/2, $minSide/2, $minSide, $minSide, $transparent);

			//Copiar para o novo canvas
			imagecopymerge($imageBase, $maskCanvas, 0, 0, 0, 0, $minSide, $minSide, 100);

			//Criar e incluir transparência
			$red = imagecolorallocate($maskCanvas, 0, 0, 0);
			imagecolortransparent($imageBase,$red);
			imagefill($imageBase, 0, 0, $red);

			$this->_saveImage($imageBase, $this->getPath($newPath), $quality);
		}	

		private function _convertHexColor ($hexColor)
		{
			$hexColor = preg_replace("/^#/", "", $hexColor);
			return sscanf($hexColor, "%02x%02x%02x");
		}

		protected function _writeOnImage ($imageBase, $text, $fontSize, $hexColor, $fontPath, $position = array('center', 'center'))
		{
			$dimension = $this->_getImageResourceSize($imageBase);

			$textColorConverted = $this->_convertHexColor($hexColor === null ? "#000000": $hexColor);

			//Converter cor
			$textColor = imagecolorallocate($imageBase, $textColorConverted[0], $textColorConverted[1], $textColorConverted[2]);

			//Criar o box de texto e calcular o tamanho
			$textBox = imagettfbbox($fontSize, 0, $fontPath, $text);
			$textSize = [ ($textBox[2] + ($textBox[0] * -1)),  ($textBox[5] * -1) ];

			//Resgatar posições
			if(is_string($position[0]))
				$position[0] = $this->_calculatePositionImage($dimension[0], $textSize[0], $position[0]);

			if(is_string($position[1]))
				$position[1] = $this->_calculatePositionImage($dimension[1], $textSize[1], $position[1]);

			//Incluir texto
			imagettftext($imageBase, $fontSize, 0, $position[0], $position[1] , $textColor, $fontPath, $text);

			return $imageBase;
		}

		/**
		* Escrever na imagem
		*
		* @param integer $imageBasePath Caminho da imagem base
		* @param integer $newPath Caminho onde a nova imagem será salva
		* @param integer $text Texto a ser escrito
		* @param integer $fontSize Tamanho da fonte
		* @param integer $hexColor Cor em hexadecimal
		* @param integer $fontPath Caminho do arquivo fonte (.ttf)
		* @param array $position Array com posição (x,y). Pode conter um valor numérico, 'center', 'right', 'left', 'top' ou 'bottom'
		* @param integer $quality Qualidade da imagem de 0 a 100
		*
		* @return null
		*/
		public function writeOnImage (string $imageBasePath, string $newPath, string $text, $fontSize, $hexColor = "#000000", $fontPath = null, $position = array('center', 'center'), $quality = 100)
		{
			//Carregar imagem
			$imageBase = $this->_loadCreateImage($imageBasePath);

			$imageBase = $this->_writeOnImage($imageBase, $text, $fontSize, $hexColor, $fontPath, $position);

			$this->_saveImage($imageBase, $this->getPath($newPath), $quality);
		}

		protected function _removeBackground ($imageBase, $hexColor)
		{
			//Define qual a cor de fundo que vai ser removida
			$colorRemove = $this->_convertHexColor($hexColor);

			//Base de cor a ser removida
			$baseColorRemove = imagecolorallocate($imageBase, $colorRemove[0], $colorRemove[1], $colorRemove[2]);

			//Definir a cor acima como transparente
			imagecolortransparent($imageBase, $baseColorRemove);

			return $imageBase;
		}

		/**
		* Remover fundo da imagem
		*
		* @param integer $imageBasePath Caminho da imagem base
		* @param integer $newPath Caminho onde a nova imagem será salva
		* @param integer $hexColor Cor do fundo em hexadecimal
		* @param integer $quality Qualidade da imagem de 0 a 100
		*
		* @return null
		*/
		public function removeBackground (string $imageBasePath, string $newPath, $hexColor = "#FFFFFF", $quality = 100)
		{
			//Carregar imagem
			$imageBase = $this->_loadCreateImage($imageBasePath);

			//Remover fundo
			$imageBase = $this->_removeBackground($imageBase, $hexColor);
			
			$this->_saveImage($imageBase, $this->getPath($newPath), $quality);
		}


		public function batchImage ($imageBasePath, $map, $folder, $filenameReplace = null)
		{
			//Separar partes do nome do arquivo
			$imageParts = pathinfo(is_null($filenameReplace) ? $imageBasePath : $filenameReplace);

			//Lista de arquivos
			$filesList = array();

			foreach ($map as $rules)
			{
				//Nome do arquivo
				$filename = $imageParts['filename'];

				//Verificar se usuário deseja que as alterações sejam identificadas no nome do arquivo
				$appendFilename = isset($rules['appendFilename']) ? $rules['appendFilename'] : true ;

				//Carregar imagem
				$image = $this->_loadCreateImage($imageBasePath, is_null($filenameReplace) ? true : false );

				//Se retornou um erro, retorná-lo para não seguir o código
				if($image instanceof Fault)
					return $image;

				foreach ($rules as $ruleName => $rule)
				{
					//Se foi solicitado remover o fundo
					if($ruleName == 'removeBackground' && is_string($rule))
					{
						$image = $this->_removeBackground($image, $rule);

						//Se retornou um erro, retorná-lo para não seguir o código
						if($image instanceof Fault)
							return $image;

						if($appendFilename === true)
							$filename .= "_" . "nobg";

						continue;
					}

				    //Se foi solicitada uma dimensão máxima
					if($ruleName == 'maxDimension' && is_array($rule))
					{
						$image = $this->_resizeImage($image, $rule, false, false);
						
						//Se retornou um erro, retorná-lo para não seguir o código
						if($image instanceof Fault)
							return $image;

						if($appendFilename === true)
							$filename .= "_" . implode("x", $rule);

						continue;
					}

					//Se foi solicitada uma dimensão mínima
					if($ruleName == 'minDimension' && is_array($rule))
					{
						$image = $this->_resizeImage($image, $rule, false, true);

						//Se retornou um erro, retorná-lo para não seguir o código
						if($image instanceof Fault)
							return $image;

						if($appendFilename === true)
							$filename .= "_" . implode("x", $rule);

						continue;
					}

					//Escrita na imagem
					if($ruleName == 'write' && is_array($rule))
					{
						$image = $this->_writeOnImage(
							$image,
							$rule['text'],
							$rule['fontSize'],
							isset($rule['color']) ? $rule['color'] : null,
							$rule['fontPath'],
							isset($rule['position']) ? $rule['position'] : null
						);

						//Se retornou um erro, retorná-lo para não seguir o código
						if($image instanceof Fault)
							return $image;

						if($appendFilename === true)
							$filename .= "_text";

						continue;
					}

					//Fundir imagens
					if($ruleName == 'merge' && is_array($rule))
					{
						$overImage = $this->_loadCreateImage($rule['overImage']);
						$image = $this->_mergeImage(
							$image,
							$overImage,
							isset($rule['dimension']) ? $rule['dimension'] : 50,
							isset($rule['position']) ? $rule['position'] : array('center','center'),
							isset($rule['opacity']) ? $rule['opacity'] : 100
						);

						//Se retornou um erro, retorná-lo para não seguir o código
						if($image instanceof Fault)
							return $image;

						if($appendFilename === true)
							$filename .= "_merged";

						continue;
					}

					//Cortar imagens
					if($ruleName == 'crop' && is_array($rule))
					{
						$image = $this->_cropImage(
							$image,
							isset($rule['dimension']) ? $rule['dimension'] : null,
							isset($rule['position']) ? $rule['position'] : array('center','center')
						);

						//Se retornou um erro, retorná-lo para não seguir o código
						if($image instanceof Fault)
							return $image;

						if($appendFilename === true)
							$filename .= "_cropped";

						continue;
					}					
				}

				//Se o atributo de appendFilename for uma string, incluí-la no final do nome do arquivo
				if(is_string($appendFilename))
					$filename .= $appendFilename;

				//Selecionar a qualidade do arquivo
				$quality = isset($rules['quality']) ? $rules['quality'] : 100;

				//Incluir qualidade no nome do arquivo
				if(isset($rules['quality']) && $appendFilename === true)
					$filename .= "_quality".$rules['quality'];

				//Pegar a extensão (se não definido, manter como está)
				$extension = isset($rules['convert']) ? $rules['convert'] : $imageParts['extension'];

				//Definir caminho da imagem a ser salva
				$path = $this->getPath($folder . "/" . $filename . "." . $extension);

				//Salvar imagem
				$image = $this->_saveImage($image, $path, $quality);

				//Se retornou um erro, retorná-lo para não seguir o código
				if($image instanceof Fault)
					return $image;

				//Incluir o arquivo na lista
				$filesList[] = $filename . "." . $extension;
			}
			return $filesList;
		}



	}