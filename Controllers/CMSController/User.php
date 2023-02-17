<?php
/**
* Métodos de criação de usuário
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMSController;

	use \CMS\CMS;
	use \CMS\Tables;
	use \CMS\InterfaceCMS;
	use \Datatable\Datatable;
	use \Dynamic\Dynamic;
	use \Navigation\JsonTransaction;
	use \Storm\Storm;

	class User extends CMS
	{
		/**
		* Lista de usuários
		*
		* @return array/dynamic
		*/
		public function list ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdminPermission('cms-user-panel');

			$dynamic = new Dynamic($data);
			
			$list = Tables::user()
				->whereNull('dateDelete')
				->columns(['id','name', 'login', 'dateCreate']);

			$filter = $dynamic->getFilters('filter');

			if($list)
				$list->search(['name', 'login'], $filter);
			
			return $dynamic->getDynamic($list);
		}

		/**
		* Seleciona usuário
		*
		* @return array/storm
		*/
		public function userSelect ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdminPermission('cms-user-panel');

			$storm = new Storm($data);

			//Tabela de usuários e tabela de permissões
			$userTable = Tables::user()
				->columns(['id', 'login', 'name'])
				->append(Tables::userPermissions()->alias('permissions'), 'id_user', 'id', 'module');

			$storm->setSource($userTable);
			return $storm->getStorm();
		}

		/**
		* Deleta usuário
		*
		* @return array/storm
		*/
		public function userDelete ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdminPermission('cms-user-panel');

			return Tables::user()
				->where('id', $data['id'])
				->whereNull('dateDelete')
				->updateDate('dateDelete');
		}
		
		public $userUpdate_map = [
				'name' => [
					'name' => 'Full name',
					'mandatory' => true,
					'minLength' => 3,
					'maxLength' => 255	
				],
				'login' => [
					'mandatory' => true,
					'minLength' => 3,
					'maxLength' => 255,
					'name' => 'Login'
				],
				'password' => [
					'replaceAfter' => 'userPassword'
				],
				'id' => [ 
					'insert' => false,
					'update' => false
				],
				'permissions' =>
				[
					'name' => 'Permissions',
					'mandatory' => true,
					'type' => 'array',
					'minArrayCount' => 1,
					'insert' => false,
					'update' => false,
					'ignore' => true
				]
			];

		/**
		* Atualização de dados do usuário
		*
		* @return array/storm
		*/
		public function userUpdate ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdminPermission('cms-user-panel');

			//Caso não tenha sido enviada nenhuma senha, ignorá-la
			if(empty($data['password']))
				$this->userUpdate_map['password']['update'] = false;

			$transaction->validateRequest($this->userUpdate_map, $data);

			$storm = new Storm($data);

			$storm->setSource(Tables::user());

			$process = $storm->process($this->userUpdate_map);

			if(isset($data['permissions']))
			{
				if(!is_array($data['permissions']))
					$data['permissions'] = [$data['permissions']];

				Tables::userPermissions()
					->where('id_user', $storm->getKeyValue())
					->delete();

				$permissions = [];

				foreach ($data['permissions'] as $permission)
				{
					$permissions[] = [
						'module' => $permission,
						'id_user' => $data['id']
					];
				}
				Tables::userPermissions()
					->insert($permissions);
			}

			return $storm->getStorm();
		}

		public $userInsert_map = [
				'name' => [
					'name' => 'Full name',
					'mandatory' => true,
					'minLength' => 3,
					'maxLength' => 255	
				],
				'login' => [
					'mandatory' => true,
					'minLength' => 3,
					'maxLength' => 255,
					'name' => 'Login'
				],
				'password' => [
					'replaceAfter' => 'userPassword',
					'mandatory' => true
				],
				'id' => [ 
					'insert' => false,
					'update' => false
				],
				'permissions' =>
				[
					'name' => 'Permissions',
					'mandatory' => true,
					'type' => 'array',
					'minArrayCount' => 1,
					'insert' => false,
					'update' => false,
					'ignore' => true
				]
			];

		/**
		* Inserção de usuário
		*
		* @return array/storm
		*/
		public function userInsert ($data, JsonTransaction $transaction)
		{
			$transaction->requireAdminPermission('cms-user-panel');
			
			$transaction->validateRequest($this->userInsert_map, $data);

			$storm = new Storm($data);

			$storm->setSource(Tables::user());

			$exist = Tables::user()
				->where('login', $data['login'])
				->exist();

			if($exist)
				return new \Fault("This username is already in use", 'cms_user_login_in_use');

			$process = $storm->process($this->userInsert_map);

			$result = $storm->getStorm();

			$permissions = [];

			if(!is_array($data['permissions']))
				$data['permissions'] = [$data['permissions']];

			foreach ($data['permissions'] as $permission)
			{
				$permissions[] = [
					'module' => $permission,
					'id_user' => $result
				];
			}

			Tables::userPermissions()
				->insert($permissions);
			
			return $result;
		}

	}