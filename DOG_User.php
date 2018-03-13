<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\User\GDT_User;
use GDO\User\GDO_User;

final class DOG_User extends GDO
{
	public function gdoColumns()
	{
		return array(
			GDT_AutoInc::make('doguser_id'),
			GDT_User::make('doguser_user_id'),
		);
	}
	
	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->getValue('doguser_user_id'); }
	public function getUserID() { return $this->getVar('doguser_user_id'); }
	
}
