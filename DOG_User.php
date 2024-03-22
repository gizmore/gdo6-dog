<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_String;
use GDO\Core\GDT_UInt;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\User\GDT_User;
use GDO\User\GDT_UserType;

/**
 *
 * @author gizmore
 */
final class DOG_User extends GDO
{

	private bool $authenticated = false;

	public static function getOrCreateUser(DOG_Server $server, string $name, ?string $displayName=null): self
	{
		if (!($user = self::getUser($server, $name)))
		{
			$user = self::createUser($server, $name, $displayName);
		}
		$server->addUser($user);
		return $user;
	}

	##############
	### Getter ###
	##############

	public static function getUser(DOG_Server $server, $name): ?self
	{
		if ($user = $server->getUserByName($name))
		{
			return $user;
		}
		return self::table()->select()->
		where(sprintf('doguser_server=%s AND doguser_name=%s', $server->getID(), GDO::quoteS($name)))->
		first()->exec()->fetchObject();
	}

	public function getID(): ?string
	{
		return $this->gdoVar('doguser_id');
	}

	public static function createUser(DOG_Server $server, string $name, ?string $displayName=null): DOG_User
	{
		$sid = $server->getID();
		$user = GDO_User::blank([
			'user_type' => GDT_UserType::MEMBER,
			'user_name' => sprintf('%s{%s}', $displayName?:$name, $sid),
		])->insert();
		return self::blank([
            'doguser_name' => $name,
            'doguser_displayname' => $displayName?:$name,
			'doguser_server' => $sid,
			'doguser_user' => $user->getID(),
		])->insert();
	}

    public static function getForWithConnector(GDO_User $user): self
    {
        self::table()->select('dog_user.*')->where();
    }

    public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('doguser_id'),
            GDT_String::make('doguser_name')->ascii()->max(64)->caseI()->notNull()->unique(false),
            GDT_Name::make('doguser_displayname')->utf8()->max(64)->caseI()->notNull(false)->unique(false),
			GDT_Server::make('doguser_server')->notNull(),
			GDT_User::make('doguser_user')->notNull(),
			GDT_Checkbox::make('doguser_service')->notNull()->initial('0'),
		];
	}

    /**
     * @throws GDO_DBException
     */
    public static function getFor(GDO_User $user): ?self
	{
		return self::getBy('doguser_user', $user->getID());
	}

	/**
	 * Get all users with a permission.
	 *
	 * @return self[]
	 */
	public static function withPermission(string $permission): array
	{
		return GDO_UserPermission::table()->select('dog_user.*')->
		joinObject('perm_user_id')->joinObject('perm_perm_id')->
		join('JOIN dog_user ON dog_user.doguser_user = gdo_user.user_id')->
		where('perm_name=' . self::quoteS($permission))->
		exec()->fetchAllObjectsAs(self::table());
	}

	public function getGDOUserID(): string { return $this->gdoVar('doguser_user'); }

	public function getFullName(): string { return sprintf('%s{%s}', $this->getName(), $this->getServerID()); }

	public function getName(): ?string { return $this->gdoVar('doguser_name'); }

	public function getServerID(): string { return $this->gdoVar('doguser_server'); }

	public function displayFullName(): string { return sprintf('%s{%s}', $this->renderName(), $this->getServerID()); }

    public function getDisplayName(): string
    {
        if ($name = $this->gdoVar('doguser_displayname'))
        {
            return $name;
        }
        return $this->getName();
    }

	public function renderName(): string
	{
		if ($name = $this->getDisplayName())
		{
			return $this->getServer()->getConnector()->obfuscate($name);
		}
		return GDO_User::ghost()->renderUserName();
	}

	############
	### Send ###
	############


	public function getServer(): DOG_Server { return $this->gdoValue('doguser_server'); }

	public function isOnline(): bool { return $this->getServer()->hasUser($this); }


	##############
	### Static ###
	##############


	public function isService(): string
	{
		return $this->gdoVar('doguser_service');
	}

	public function send($text): bool
	{
		return $this->getServer()->getConnector()->sendToUser($this, $text);
	}

	public function sendNotice($text): bool
	{
		return $this->getServer()->getConnector()->sendNoticeToUser($this, $text);
	}

	public function isRegistered(): bool
	{
		return !!$this->settingVar('Login', 'password');
	}

	################
	### Settings ###
	################

	public function settingVar(string $moduleName, string $key): ?string
	{
		$user = $this->getGDOUser();
		return $user->settingVar($moduleName, $key);
	}

	############
	### Auth ###
	############

	public function getGDOUser(): GDO_User
	{
		return $this->gdoValue('doguser_user');
	}

	public function isAuthenticated(): bool
	{
		return $this->authenticated;
	}

	public function login(): void
	{
		$this->authenticated = true;
		Dog::instance()->event('dog_authenticated', $this);
	}

	public function logout(): void
	{
		$this->authenticated = false;
	}

    public function renderFullName(): string
    {
        if (!($name = $this->gdoVar('doguser_displayname')))
        {
            $name = $this->getName();
        }
        return $name;
    }

}
