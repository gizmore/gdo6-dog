<?php
namespace GDO\Dog\Test;

use GDO\Dog\Connector\Bash;
use GDO\Dog\Dog;
use GDO\User\GDO_UserPermission;
use function PHPUnit\Framework\assertEquals;
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
		$dog = new Dog();
		$result = $dog->loadPlugins();
		$dog->init();
		assertTrue($result, 'Assert that dog can load plugins.');
	}

	public function testBashConnector()
	{
		$bash = Bash::instance();
		assertTrue($bash instanceof Bash);
		$response = $this->bashCommand('ping');
		assertMatchesRegularExpression('/pong/is', $response, 'Test PING via Bash connector.');
	}

	public function testGizmoreOp()
	{
		$user = $this->userGizmore1();
		GDO_UserPermission::grant($user, Dog::VOICE);
		GDO_UserPermission::grant($user, Dog::HALFOP);
		GDO_UserPermission::grant($user, Dog::OPERATOR);
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
		$r = $this->bashCommand('help help');
		assertStringContainsString('Print short help for a method', $r, 'Test the help command for command overview.');
	}

	protected function setUp(): void
	{
		if (Bash::instance()->init())
		{
			parent::setUp();
		}
	}

}
