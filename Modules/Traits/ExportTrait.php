<?php
/**
* Funções de exportação simples
*
* @package	Traits
* @author 	Lucas/Postali
*/
	use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
	use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

	trait ExportTrait
	{
		
		/**
		 * Salva dados em formato CSV
		 * 
		 * @param string $file Nome do arquivo
		 * @param array $data Dados
		 * @param array $titles (Opcional) Títulos
		 * 
		 * @return string
		 */
		public function saveCSV ($file, $data, $titles = [])
		{
			$content = $this->arrayToCSV($data, $titles);
			file_put_contents($file, $content);			
			return $content;
		}

		/**
		 * Exibir dados em formato CSV
		 * 
		 * @param Navigation $navigation Objeto do navigation
		 * @param string $name Nome do arquivo a ser baixado
		 * @param array $data Dados
		 * @param array $titles (Opcional) Títulos
		 * 
		 * @return string
		 */
		public function exportCSV ($name, $data, $titles = [])
		{
			$content = $this->arrayToCSV($data, $titles);
			$this->outputBinary($content, 'text/csv', $name . ".csv");	
		}

		/**
		 * Salva dados em formato XLSX
		 * 
		 * @param string $file Nome do arquivo
		 * @param array $data Dados
		 * @param array $titles (Opcional) Títulos
		 * 
		 * @return string
		 */
		public function saveXLSX ($file, $data, $titles = [])
		{
			//Iniciar planilha
			$writer = WriterEntityFactory::createXLSXWriter();
			$writer->openToFile($this->getPath($file));

			if(!empty($titles))
			{
				//Estilo dos títulos
				$style = (new StyleBuilder())
		           ->setFontBold()
		           ->build();

				//Adicionar títulos
				$row = WriterEntityFactory::createRowFromArray(array_values($titles), $style);
				$writer->addRow($row);
			}

			//Inserir cada um dos dados na planilha
			foreach ($data as $values)
			{
				$row = WriterEntityFactory::createRowFromArray(array_values($values));
				$writer->addRow($row);
			}

			//Fechar o arquivos
			$writer->close();

			return true;
		}

		/**
		 * Exibir dados em formato XLSX
		 * 
		 * @param Navigation $navigation Objeto do navigation
		 * @param string $name Nome do arquivo a ser baixado
		 * @param array $data Dados
		 * @param array $titles (Opcional) Títulos
		 * 
		 * @return string
		 */
		public function exportXLSX ($name, $data, $titles = [])
		{
			$tempFile = $this->getPath('Var/xlsx_temp_' . uniqid(rand()) . ".tmp");
			$this->saveXLSX($tempFile, $data, $titles);
			$content = file_get_contents($tempFile);
			unlink($tempFile);
			$this->outputBinary($content, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $name . ".xlsx");			
		}

	}

?>