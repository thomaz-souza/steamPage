<?php
/**
* Interface da página de CMS que recebe botões e eventos para compor a página
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMS;
	
	class InterfaceCMS extends CMS
	{
		/**
		* Verifica se o ID é único e válido
		*
		* @return bool
		*/
		private function _checkID ($id, $object)
		{
			if( !in_array($id, $object) && !isset($object[$id]) )
				return true;
			
			return false;
		}

		/**
		* Filtra os módulos de acordo com os permitidos pelo sistema
		*
		* @return array
		*/
		private function _filterModulesPermissions ($objects)
		{
			$list = [];
			//Para cada um dos objetos
			foreach ($objects as $id => $content)

				//Inserir na lista caso seja permitido
				if($this->getUserPermissions($id))
					$list[$id] = $content;
			
			return $list;
		}

		private $allModulesIds = [];

		/**
		* Resgata todos os módulos registrados
		*
		* @return array
		*/
		public function getAllModulesIds ($sourcesFilter = [])
		{
			$list = [];

			if(count($this->allModulesIds) == 0 || !empty($sourcesFilter))
			{
				$sources = [
					'sideMenu' => $this->sideMenu,
					'topMenu' => $this->topMenu,
					'topBarMenu' => $this->topBarMenu,
					'events' => $this->events,
					'userMenu' => $this->userMenu,
					'permissions' => $this->permissions,
					'blocks' => $this->blocks
				];

				foreach ($sources as $sourceName => $modules)
				{
					foreach ($modules as $id => $value)
					{
						if(!empty($sourcesFilter) && !in_array($sourceName, $sourcesFilter))
							continue;

						/*if(!isset($this->allModulesIds[$sourceName]))
							$this->allModulesIds[$sourceName] = []; [$sourceName]*/
						$list[$id] = $value;
					}
				}	
				
				if(!empty($sourcesFilter))
					return $list;

				$this->allModulesIds = $list;
			}

			return $this->allModulesIds;
		}

		/**
		* Resgata o id de um módulo de acordo com o modulo e bloco
		*
		* @param $module string Nome do módulo
		* @param $block string Nome do bloco
		*
		* @return array
		*/
		public function getModuleId ($module, $block)
		{
			foreach ($this->getAllModulesIds() as $id => $values)
			{
				$action = isset($values['action']) ? $values['action'] : [];
				if(count($action) > 1 && $action[0] == $module && $action[1] == $block)
					return ['id' => $id, 'values' => $values];
			}
			foreach ($this->getAllModulesIds() as $id => $values)
			{
				$action = isset($values['action']) ? $values['action'] : [];
				if(count($action) > 1 && $action[0] == $module)
					return ['id' => $id, 'values' => $values];
			}
			return [];
		}

/*PERMISSIONS -----------------------------------------------------------------------------------*/

		private $permissions = [];

		/**
		* Adiciona uma permissão específica
		*
		* @return null
		*/
		public function addPermission ($options, $id = null)
		{
			//Verificar se é um ID válido
			if(!$this->_checkID($id, $this->permissions))
				return false;

			//Criar novo registro do evento
			$this->permissions[$id] = [];

			//Inserir opções permitidas
			$allowedOptions = array('icon', 'title', 'parent');
			foreach ($allowedOptions as $option)
				$this->permissions[$id][$option] = (isset($options[$option])) ? $options[$option] : "";
		}

		/**
		* Resgatar permissões
		*
		* @return array
		*/
		public function getPermissions ()
		{
			$permissions = [];
			foreach ($this->permissions as $id => $value)
			{
				if($value['type'] === $type)
					$permissions[$id] = $value;
			}
			return $this->_filterModulesPermissions($permissions);
		}

/*BLOCOS DO DASHBOARD -----------------------------------------------------------------------------------*/

		private $blocks = [];

		/**
		* Adiciona um novo bloco ao dashboard
		*
		* @return null
		*/
		public function addBlock ($options, $id = null)
		{
			//Verificar se é um ID válido
			if(!$this->_checkID($id, $this->blocks))
				return false;

			//Criar novo registro do evento
			$this->blocks[$id] = [];

			//Inserir opções permitidas
			$allowedOptions = [
				'icon' => '',
				'title' => '',
				'board' => 'empty',
				'order' => 100,
				'size' => '6'
			];

			foreach ($allowedOptions as $option => $default)
				$this->blocks[$id][$option] = (isset($options[$option])) ? $options[$option] : $default;
		}

		/**
		* Resgatar blocos do dashboard
		*
		* @return array
		*/
		public function getBlocks ()
		{
			$blocks = [];
			foreach ($this->blocks as $id => $value)
			{
				//if($value['type'] === $type)
					$blocks[$id] = $value;
			}

			$filtered = $this->_filterModulesPermissions($blocks);

			if(empty($filtered))
			{
				$filtered['cms_default'] = [
					'board' => 'default',
					'order' => 1,
					'size' => 7
				];
			}

			if($this->getEvent('new'))
			{
				$filtered['cms_events'] = [
					'board' => 'events',
					'order' => 0,
					'size' => 5
				];
			}

			$order = array_column($filtered, 'order');
			array_multisort($order, SORT_ASC, $filtered);			

			return $filtered;
		}

/*SIDE MENU -----------------------------------------------------------------------------------*/
	
		private $sideMenu = [];

		/**
		* Adiciona um novo botão na barra lateral
		*
		* @return null
		*/
		public function addSideMenu ($options, $id = null)
		{
			//Verificar se é um ID válido
			if(!$this->_checkID($id, $this->sideMenu))
				return false;

			//Criar novo registro do menu
			$this->sideMenu[$id] = [];
			
			$children = [];

			if(!empty($options['child']) && is_array($options['child']))
			{
				foreach ($options['child'] as $idChild => $childOptions)
				{
					$newId = $id . "-" . $idChild;					

					//Verificar se é um ID válido
					if(!$this->_checkID($newId, $this->sideMenu))
						return false;

					$allowedChildOptions = [
						'icon'=> '',
						'title' => $this->write('Untitled', 'admin'),
						'url' => false,
						'counter' => false,
						'action' => [],
						'parent' => $id
					];

					$childOptions['parent'] = $id;

					foreach ($allowedChildOptions as $option => $default)
						$children[$newId][$option] = (isset($childOptions[$option])) ? $childOptions[$option] : $default;
				}

				$options['child'] = $children;
			}

			//Inserir opções permitidas
			$allowedOptions = [
				'icon'=> '',
				'title' => $this->write('Untitled', 'admin'),
				'url' => false,
				'counter' => false,
				'action' => [],
				'child' => []
			];
			foreach ($allowedOptions as $option => $default)
				$this->sideMenu[$id][$option] = (isset($options[$option])) ? $options[$option] : $default;

			$this->sideMenu = array_merge($this->sideMenu, $children);
		}

		/**
		* Resgatar botões da barra lateral
		*
		* @return array
		*/
		public function getSideMenu ()
		{	
			return $this->_filterModulesPermissions($this->sideMenu);
		}

/*TOPBAR MENU -----------------------------------------------------------------------------------*/

		private $topBarMenu = [];

		/**
		* Adiciona um novo botão na barra superior
		*
		* @return null
		*/
		public function addTopBarMenu ($options, $id = null)
		{
			//Verificar se é um ID válido
			if(!$this->_checkID($id, $this->topBarMenu))
				return false;

			//Criar novo registro do menu
			$this->topBarMenu[$id] = [];

			//Inserir opções permitidas
			$allowedOptions = array('icon', 'title', 'url', 'counter', 'action');
			foreach ($allowedOptions as $option)
				$this->topBarMenu[$id][$option] = (isset($options[$option])) ? $options[$option] : "";
		}

		/**
		* Resgatar botões da barra superior
		*
		* @return array
		*/
		public function getTopBarMenu ()
		{
			return $this->_filterModulesPermissions($this->topBarMenu);
		}

/*EVENTS -----------------------------------------------------------------------------------*/

		private $events = [];

		/**
		* Adiciona um novo evento
		*
		* @return null
		*/
		public function addEvent ($options, $id = null)
		{
			//Verificar se é um ID válido
			if(!$this->_checkID($id, $this->events))
				return false;

			//Criar novo registro do evento
			$this->events[$id] = [];

			//Inserir opções permitidas
			$allowedOptions = array('icon', 'title', 'time', 'color', 'action', 'url', 'type', 'visible');
			foreach ($allowedOptions as $option)
				$this->events[$id][$option] = (isset($options[$option])) ? $options[$option] : "";
		}

		/**
		* Resgatar eventos por tipo
		*
		* @param $type string Tipo do evento
		*
		* @return array
		*/
		public function getEvent ($type = '')
		{
			$events = [];
			foreach ($this->events as $id => $value)
			{
				if($value['type'] === $type)
					$events[$id] = $value;
			}
			return $this->_filterModulesPermissions($events);
		}

		/**
		* Resgatar quantidade de eventos de um determinado tipo
		*
		* @param $type string Tipo do evento
		*
		* @return array
		*/
		public function getEventCount ($type = '', $requireVisible = true)
		{
			$total = 0;

			foreach($this->getEvent($type) as $event)
			{
				if($event['visible'] || (!$requireVisible))
					$total++;
			}

			return $total;
		}

/*USER MENU -----------------------------------------------------------------------------------*/

		private $userMenu = [];

		/**
		* Adiciona um novo botão no Menu de usuário
		*
		* @return null
		*/
		public function addUserMenu ($options, $id = null)
		{
			//Verificar se é um ID válido
			if(!$this->_checkID($id, $this->userMenu))
				return false;

			//Criar novo registro do menu
			$this->userMenu[$id] = [];

			//Inserir opções permitidas
			$allowedOptions = array('icon', 'title', 'color', 'sub-title', 'action', 'url');
			foreach ($allowedOptions as $option)
				$this->userMenu[$id][$option] = isset($options[$option]) ? $options[$option] : '';
		}

		/**
		* Resgatar botões do menu de usuário
		*
		* @return array
		*/
		public function getUserMenu ()
		{
			return $this->_filterModulesPermissions($this->userMenu);
		}

/*Dependências -----------------------------------------------------------------------------------*/
	
		private $dependencyCSS = [];

		/**
		* Adicionar CSS
		*
		* @param $link string "Link a ser inserido"
		*
		* @return array
		*/
		public function addCSS ($link)
		{
			//Conferir se o link já existe
			if(in_array($link, $this->dependencyCSS))
				return false;

			//Criar novo registro
			$this->dependencyCSS[] = $link;
		}

		/**
		* Resgatar links de CSS a serem inseridos
		*
		* @return array
		*/
		public function getCSS ()
		{
			return $this->dependencyCSS;
		}

		private $dependencyJS = [];

		/**
		* Adicionar JS
		*
		* @param $link string "Link a ser inserido"
		*
		* @return array
		*/
		public function addJS ($link)
		{
			//Conferir se o link já existe
			if(in_array($link, $this->dependencyJS))
				return false;

			//Criar novo registro
			$this->dependencyJS[] = $link;
		}

		/**
		* Resgatar links de JS a serem inseridos
		*
		* @return array
		*/
		public function getJS ()
		{
			return $this->dependencyJS;
		}





		
		

		

		private $topMenu = [];

		public function addTopMenu ($options, $id = null)
		{
			//Verificar ou criar ID valido
			$id = $this->_setID($id, $this->topMenu);
			if($id === false)
				return false;

			//Criar novo registro do menu
			$this->topMenu[$id] = [];

			//Inserir opções permitidas
			$allowedOptions = array('icon', 'title', 'url', 'counter', 'action');
			foreach ($allowedOptions as $option)
				if(isset($options[$option]))
					$this->topMenu[$id][$option] = $options[$option];
		}

		public function getTopMenu ()
		{
			return $this->_filterModulesPermissions($this->topMenu);
		}
	}

		


?>