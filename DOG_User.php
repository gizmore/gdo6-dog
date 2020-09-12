<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\DB\GDT_Object;
use GDO\DB\GDT_String;

final class DOG_User extends GDO
{
	public function gdoColumns()
	{
		return array(
			GDT_AutoInc::make('doguser_id'),
			GDT_String::make('doguser_name')->utf8()->max(64),
			GDT_Object::make('doguser_server')->table(DOG_Server::table()),
			GDT_User::make('doguser_user_id'),
		);
	}
	
	/**
	 * @return GDO_User
	 */
	public function getGDOUser() { return $this->getValue('doguser_user_id'); }
	public function getGDOUserID() { return $this->getVar('doguser_user_id'); }
	
	/**
	 * @param DOG_Server $server
	 * @param string $name
	 * @return self
	 */
	public static function getOrCreateUser(DOG_Server $server, $name)
	{
		if ($user = self::getUser($server, $name))
		{
			return $user;
		}
		return self::createUser($server, $name);
	}
	
	public static function getUser(DOG_Server $server, $name)
	{
		return self::table()->select()->
			where(sprintf("doguser_server=%s AND doguser_name=%s", $server->getID(), GDO::quoteS($name)))->
			first()->exec()->fetchObject();
	}
	
	public static function createUser(DOG_Server $server, $name)
	{
		$sid = $server->getID();
		$user = GDO_User::blank(array(
			'user_type' => GDO_User::MEMBER,
			'user_name' => "__dog_{$sid}_{$name}",
		))->insert();
		return self::blank(array(
			'doguser_name' => $name,
			'doguser_server' => $sid,
			'doguser_user_id' => $user->getID(),
		))->insert();
	}
}
