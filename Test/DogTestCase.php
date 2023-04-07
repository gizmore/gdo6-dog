<?php
namespace GDO\Dog\Test;

use GDO\Core\Application;
use GDO\Dog\Connector\Bash;
use GDO\Dog\Dog;
use GDO\Dog\DOG_User;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\Util\Strings;
use Throwable;

/**
 *
 * @author gizmore
 *
 */
class DogTestCase extends TestCase
{

	protected $doguser;

	public function dogUser($username)
	{
		$user = GDO_User::getByName($username);
		return $this->user($user);
	}

	public function user(GDO_User $user): GDO_User
	{
		$username = Strings::substrTo($user->getName(), '{', $user->getName());
		$this->doguser = DOG_User::getOrCreateUser($this->getServer(), $username);
		return parent::user($user);
	}

	protected function getServer()
	{
		return Bash::$BASH_SERVER;
	}

	protected function setUp(): void
	{
		Dog::instance()->init();
		parent::setUp();
		Application::instance()->cli(true);
		$this->restoreUserPermissions($this->userGizmore1());
	}

	/**
	 * Restore gizmore because auto coverage messes with him a lot.
	 *
	 * @param GDO_User $user
	 */
	protected function restoreUserPermissions(GDO_User $user): void
	{
		if (count(GDT_MethodTest::$TEST_USERS))
		{
			$g1 = GDO_User::getByName('gizmore{1}');
			if ($user->getID() === $g1->getID())
			{
				$table = GDO_UserPermission::table();
				$table->grant($user, 'admin');
				$table->grant($user, 'staff');
				$table->grant($user, 'cronjob');
				$table->grant($user, Dog::VOICE);
				$table->grant($user, Dog::HALFOP);
				$table->grant($user, Dog::OPERATOR);
				$table->grant($user, Dog::ADMIN);
				$user->changedPermissions();
			}
		}
	}

	public function userGizmore1()
	{
		$g1 = GDO_User::getByName('gizmore{1}');
		return $this->user($g1);
	}

	protected function tearDown(): void
	{
		Application::instance()->cli(false);
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
