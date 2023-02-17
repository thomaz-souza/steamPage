<?php
/**
* Classe para CMS 
*
* @package		Culture
* @author 		Lucas/Postali
*/
	namespace Culture;

	class CMS extends \_Core
	{
		public function sideMenu (\CMS\InterfaceCMS $interface)
		{
			$interface->addSideMenu(array(
				"icon" => "fas fa-globe-americas",
				"title" => $this->write("Culture", "admin"),
				"child" => [
					'locales' => 
						[
							"icon" => "fas fa-flag",
							"title" => $this->write("Locales", "admin"),
							"action" => ["Culture","Locales"],
						],
					'language' =>
						[
							"icon" => "fas fa-language",
							"title" => $this->write("Language", "admin"),
							"action" => ["Culture","Language"],
						],
					'timezone' =>
						[
							"icon" => "far fa-calendar-alt",
							"title" => $this->write("Timezone", "admin"),
							"action" => ["Culture","Timezone"],
						],
					'currency' =>
						[
							"icon" => "fas fa-comment-dollar",
							"title" => $this->write("Currency", "admin"),
							"action" => ["Culture","Currency"],
						]
				]
			), 'culture');

		}

		/**
		* Buscar línguas disponíveis
		*
		* @return bool
		*/
		public function getAvailableLanguages ()
		{
			$languages = [];

			foreach ($this->_config('culture')['languages'] as $langId => $lang)
			{
				$folder = $this->getPath('Resources/Languages/' . $langId);

				if(!is_dir($folder) || count(scandir($folder)) < 3)
					continue;

				$languages[] = $lang;
			}

			return $languages;
		}

		/**
		* Copia dados de uma língua para outra
		*
		* @return bool
		*/
		public function copyLanguage ($data, $transaction)
		{
			$language = preg_replace('/\\/|\\\/', "", $data['language']);
			$from = preg_replace('/\\/|\\\/', "", $data['from']);

			$languageFolder = $this->getPath('Resources/Languages/' . $language);

			if(!is_dir($languageFolder))
				if(!mkdir($languageFolder))
					return false;
			
			$fromLanguage = $this->getPath('Resources/Languages/' . $from);

			foreach (scandir($fromLanguage) as $file)
			{
				if(preg_match("/\.json$/i", $file))
					copy($this->getPath($fromLanguage . '/' . $file), $this->getPath($languageFolder . '/' . $file));
			}

			$loaded = $this->languageLoadFile($language);
			unset($loaded['_']);
			return $loaded;
		}

		/**
		* Salva dados da língua
		*
		* @return array
		*/
		public function saveLanguage ($data, $transaction)
		{
			$language = preg_replace('/\\/|\\\/', "", $data['language']);
			$scope = preg_replace('/\\/|\\\/', "", $data['scope']);

			$folder = $this->getPath('Resources/Languages/' . $language);

			if(!is_dir($folder))
				if(!mkdir($folder))
					return false;

			$file = $this->getPath($folder . '/' . $scope . ".json");

			$json = json_encode($data['text'], JSON_PRETTY_PRINT);

			file_put_contents($file, $json);

			return json_decode(file_get_contents($file), true);
		}

		const TRANSLATION_BRING_NEW = 1;
		const TRANSLATION_BRING_ENTIRELY = 2;

		const TRANSLATION_NEW_BLANK = 1;
		const TRANSLATION_NEW_FROM = 2;
		const TRANSLATION_NEW_ORIGINAL = 3;
		const TRANSLATION_ALL_FROM = 4;
		const TRANSLATION_ALL_ORIGINAL = 5;
		const TRANSLATION_ALL_BLANK = 6;

		public function reflectScope ($data, $transaction)
		{
			//Retirar possíveis termos perigosos
			$language = preg_replace('/\\/|\\\/', "", $data['language']);
			$scope = preg_replace('/\\/|\\\/', "", $data['scope']);
			$from = preg_replace('/\\/|\\\/', "", $data['from']);

			//Resgatar língua fonte
			$fromContent = $this->languageLoadFile($from);
			$fromContent = isset($fromContent[$scope]) ? $fromContent[$scope] : [];

			//Resgatar língua atual
			$toContent = $this->languageLoadFile($language);
			$toContent = isset($toContent[$scope]) ? $toContent[$scope] : [];

			//Se deve buscar apenas novas
			if($data['bring'] == self::TRANSLATION_BRING_NEW)					
				$newKeys = array_merge(array_keys($toContent), array_keys($fromContent));

			//Se deve buscar tudo (ignorando a língua atual)
			else
				$newKeys = array_keys($fromContent);

			$newContent = [];

			//Tipo de tradução
			$transMode = $data['translation'];

			foreach ($newKeys as $key)
			{
				if($transMode == self::TRANSLATION_ALL_BLANK || $transMode == self::TRANSLATION_NEW_BLANK)
					$content = '';
				
				else if($transMode == self::TRANSLATION_ALL_ORIGINAL || $transMode == self::TRANSLATION_NEW_ORIGINAL)
					$content = $key;
				
				else if($transMode == self::TRANSLATION_ALL_FROM || $transMode == self::TRANSLATION_NEW_FROM)
					$content = isset($fromContent[$key]) ? $fromContent[$key] : '';				

				if($transMode == self::TRANSLATION_NEW_BLANK
					|| $transMode == self::TRANSLATION_NEW_ORIGINAL
					|| $transMode == self::TRANSLATION_NEW_FROM)
					$content = isset($toContent[$key]) ? $toContent[$key] : $content;				

				$newContent[$key] = $content;			
			}

			return $newContent;
		}
	}