<?php
declare(strict_types=1);
namespace GDO\Dog\Test;

use GDO\Dog\Connector\Bash;
use GDO\Dog\Dog;
use GDO\User\GDO_UserPermission;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertTrue;

/**
 * Basic bash connector testing and dog user creation
 *
 * @author gizmore
 */
final class DogTest extends DogTestCase
{

	public function testDogCreation()
	{
		$dog = Dog::instance();
//		$result = $dog->loadPlugins();
//		$dog->init();
		Bash::instance();
		assertTrue($dog->running, 'Assert that dog can load plugins.');
	}

	public function testBashConnector()
	{
		$this->userGizmore1();
		$response = $this->bashCommand('ping');
		assertMatchesRegularExpression('/pong/is', $response, 'Test PING via Bash connector.');
	}

	public function testGizmoreOp()
	{
		$user = $this->userGizmore1();
		GDO_UserPermission::grant($user, Dog::VOICE);
		GDO_UserPermission::grant($user, Dog::HALFOP);
		GDO_UserPermission::grant($user, Dog::OPERATOR);
		$user->changedPermissions();
		assertTrue($user->hasPermission(Dog::OPERATOR), 'Test if gizmore{1} is owner.');
	}

	public function testCoreMethods()
	{
		$this->userGizmore1();
		$r = $this->bashCommand('user.whoami');
		assertStringContainsString('gizmore{1}', $r, 'Test the whoami command.');
	}

	public function testHelpCommand()
	{
		$this->userGizmore1();
		$r = $this->bashCommand('help help');
		assertStringContainsString('Print short help for a method', $r, 'Test the help for a single command.');
		$r = $this->bashCommand('help');
		assertStringContainsString('cc, ls, ', $r, 'Test the help command for all available methods.');
	}

	protected function setUp(): void
	{
		if (Bash::instance()->init())
		{
			parent::setUp();
		}
	}

}
