<?php
declare(strict_types=1);
namespace GDO\Dog\Test;

use GDO\Core\Application;
use GDO\Dog\Connector\Bash;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_User;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\Util\Strings;

/**
 *
 * @author gizmore
 *
 */
class DogTestCase extends TestCase
{

	protected DOG_User $doguser;

	public function dogUser(string $username): GDO_User
	{
		$user = GDO_User::getByName($username);
		return $this->user($user);
	}

	protected function user(GDO_User $user): GDO_User
	{
		$username = Strings::substrTo($user->getName(), '{', $user->getName());
		$this->doguser = DOG_User::getOrCreateUser($this->getServer(), $username);
		return parent::user($user);
	}

	protected function getServer(): DOG_Server
	{
		Bash::instance();
		return Bash::$BASH_SERVER;
	}

//	protected function setUp(): void
//	{
//		parent::setUp();
//	}

	/**
	 * Restore gizmore because auto coverage messes with him a lot.
	 */
	protected function restoreUserPermissions(GDO_User $user): void
	{
		$this->user($user);
		if (count(GDT_MethodTest::$TEST_USERS))
		{
			$g1 = GDO_User::getByName('gizmore{1}');
			if ($user->getID() === $g1->getID())
			{
				GDO_UserPermission::grant($user, 'admin');
				GDO_UserPermission::grant($user, 'staff');
				GDO_UserPermission::grant($user, 'cronjob');
				GDO_UserPermission::grant($user, Dog::VOICE);
				GDO_UserPermission::grant($user, Dog::HALFOP);
				GDO_UserPermission::grant($user, Dog::OPERATOR);
				GDO_UserPermission::grant($user, Dog::ADMIN);
				$user->changedPermissions();
			}
		}
	}

	public function userGizmore1(): GDO_User
	{
		$g1 = GDO_User::getByName('gizmore{1}');
		$this->restoreUserPermissions($g1);
		return $this->user($g1);
	}

	protected function tearDown(): void
	{
	}

	/**
	 * Execute a bash command and return output.
	 */
	public function bashCommand(string $line): string
	{
		try
		{
			ob_start();
			Application::$INSTANCE->reset();
			Dog::instance()->event('dog_cmdline2', $line);
			return ob_get_contents();
		}
		finally
		{
			ob_end_clean();
		}
	}

}
