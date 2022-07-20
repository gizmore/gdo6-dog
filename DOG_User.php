<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\User\GDO_UserPermission;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Checkbox;

final class DOG_User extends GDO
{
    private $authenticated = false;
    
    public function gdoColumns() : array
	{
		return array(
			GDT_AutoInc::make('doguser_id'),
			GDT_String::make('doguser_name')->utf8()->max(64)->caseI()->notNull(),
		    GDT_Server::make('doguser_server')->notNull(),
		    GDT_User::make('doguser_user_id')->notNull(),
		    GDT_Checkbox::make('doguser_service')->notNull()->initial('0'),
		);
	}
	
	##############
	### Getter ###
	##############
	public function getID() : ?string { return $this->gdoVar('doguser_id'); }
	/**
	 * @return GDO_User
	 */
	public function getGDOUser() { return $this->gdoValue('doguser_user_id'); }
	public function getGDOUserID() { return $this->gdoVar('doguser_user_id'); }
	
	/**
	 * @return DOG_Server
	 */
	public function getServer() { return $this->gdoValue('doguser_server'); }
	public function getServerID() { return $this->gdoVar('doguser_server'); }

	public function getName() { return $this->gdoVar('doguser_name'); }
	public function getFullName() { return sprintf('%s{%s}', $this->getName(), $this->getServerID()); }
	public function displayName() { return $this->getServer()->getConnector()->obfuscate($this->getName()); }
	public function displayFullName() { return sprintf('%s{%s}', $this->displayName(), $this->getServerID()); }

	public function isOnline() { return $this->getServer()->hasUser($this); }
	public function isService() { return $this->gdoVar('doguser_service'); }
	
	############
	### Send ###
	############
	public function send($text)
	{
	    return $this->getServer()->getConnector()->sendToUser($this, $text);
	}
	
	public function sendNotice($text)
	{
	    return $this->getServer()->getConnector()->sendNoticeToUser($this, $text);
	}
	
	##############
	### Static ###
	##############
	/**
	 * @param DOG_Server $server
	 * @param string $name
	 * @return self
	 */
	public static function getOrCreateUser(DOG_Server $server, $name)
	{
		if (!($user = self::getUser($server, $name)))
		{
    		$user = self::createUser($server, $name);
		}
		$server->addUser($user);
		return $user;
	}
	
	public static function getUser(DOG_Server $server, $name)
	{
	    if ($user = $server->getUserByName($name))
	    {
	        return $user;
	    }
		return self::table()->select()->
			where(sprintf("doguser_server=%s AND doguser_name=%s", $server->getID(), GDO::quoteS($name)))->
			first()->exec()->fetchObject();
	}
	
	public static function createUser(DOG_Server $server, $name)
	{
		$sid = $server->getID();
		$user = GDO_User::blank(array(
			'user_type' => GDO_User::MEMBER,
			'user_name' => sprintf('%s{%s}', $name, $sid),
		))->insert();
		return self::blank(array(
			'doguser_name' => $name,
			'doguser_server' => $sid,
			'doguser_user_id' => $user->getID(),
		))->insert();
	}
	
	/**
	 * Get all users with a permission.
	 * @param string $permission
	 * @return self[]
	 */
	public static function withPermission($permission)
	{
	    return GDO_UserPermission::table()->select('dog_user.*')->
	    joinObject('perm_user_id')->joinObject('perm_perm_id')->
	    join('JOIN dog_user ON dog_user.doguser_user_id = gdo_user.user_id')->
	    where("perm_name=".self::quoteS($permission))->
	    exec()->fetchAllObjectsAs(self::table());
	}
	
	############
	### Auth ###
	############
	public function isRegistered()
	{
	    return !!$this->getGDOUser()->gdoVar('user_password');
	}
	
	public function isAuthenticated()
	{
	    return $this->authenticated;
	}
	
	public function login()
	{
	    $this->authenticated = true;
	    Dog::instance()->event('dog_authenticated', $this);
	}
	
	public function logout()
	{
	    $this->authenticated = false;
	}

}
