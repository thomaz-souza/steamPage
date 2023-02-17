<?php
/**
* Funções de Console
*
* @package	CMS
* @author 	Lucas/Postali
*/
	namespace CMS;

	use \Icecream\Icecream;

	class Tables extends \_Core
	{
		const TABLE_USER = 'cms_user';

		const TABLE_USER_SESSION = 'cms_user_session';

		const TABLE_USER_PERMISSIONS = 'cms_user_permissions';

		static public function user ()
		{
			return new Icecream(self::TABLE_USER);
		}

		static public function userSession ()
		{
			return new Icecream(self::TABLE_USER_SESSION);
		}

		static public function userPermissions ()
		{
			return new Icecream(self::TABLE_USER_PERMISSIONS);
		}

	}

?>