<?php
/**
* Métodos do CMS
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMSController;

	use \CMS\CMS;
	use \CMS\InterfaceCMS;
	use \Navigation\Page;
	use \Navigation\JsonTransaction;
	use \Dynamic\Dynamic;
	use \Storm\Storm;

	class Edit extends CMS
	{
		public function getConfig ($data, JsonTransaction $transaction = null)
		{
			$transaction->requireAdmin();
			return $this->_getConfig($data['config']);
		}

		public function getConfigDynamic ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdmin();

			$dynamic = new Dynamic($data);
			
			$dynamic->setResults($this->_getConfig($dynamic->getFilters('config')));

			return $dynamic->getDynamic();
		}

		public function getConfigStorm ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdmin();

			$storm = new Storm($data);
			
			$config = $this->_getConfig($storm->getFilters('config'));


			$results = $config[$storm->getKeyValue()];

			$results[$storm->getKey()] = $storm->getKeyValue();

			$storm->setResults($results);

			return $storm->getStorm();
		}

		public $setConfig_map = [
			'config' => [
				'type' => 'string',
				'mandatory' => true
			],
			'values' => [
				'mandatory' => true
			]
		];

		/**
		* Define configurações manualmente
		*
		* @return array
		*/
		public function setConfig ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdmin();

			$transaction->validateRequest($this->setConfig_map);

			return $this->_setConfig($data['config'], $data['values'], isset($data['key']) ? $data['key'] : null, isset($data['delete']));
		}

		/**
		* Storm para resgatar configurações
		*
		* @return array
		*/
		public function setConfigStorm ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdmin();

			$storm = new Storm($data);

			//Atualizar dados
			if($storm->getAction() == "update")
				$this->_setConfig($storm->getFilters('config'), $storm->getValues(), $storm->getKeyValue());

			//Inserir dados
			if($storm->getAction() == "insert")
				$this->_setConfig($storm->getFilters('config'), $storm->getValues(), $storm->getValues($storm->getFilters('keySource')));

			//Deletar dados
			if($storm->getAction() == "delete")
				$this->_setConfig($storm->getFilters('config'), null, $storm->getKeyValue(), true);

			$storm->setResults([]);

			return $storm->getStorm();
		}

		public function call ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdmin();
			$call = $data['_call'];
			unset($data['_call']);
			return $this->parseMethod($call, true, $data, $transaction);
		}

	}