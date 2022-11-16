<?php
namespace GDO\Dog\Test;

use GDO\Dog\Dog;
use function PHPUnit\Framework\assertTrue;
use GDO\Dog\Connector\Bash;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use GDO\User\GDO_UserPermission;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsString;

/**
 * Basic bash connector testing and dog user creation
 * @author gizmore
 */
final class DogTest extends DogTestCase
{
    protected function setUp() : void
    {
        if (Bash::instance())
        {
            parent::setUp();
        }
    }
    
    public function testDogCreation()
    {
        $dog = new Dog();
        $result = $dog->loadPlugins();
        $dog->init();
        assertTrue($result, 'Assert that dog can load plugins.');
    }
    
    public function testBashConnector()
    {
        $bash = Bash::instance()->getConnector();
        assertTrue($bash instanceof Bash);
        $response = $this->bashCommand("ping");
        assertMatchesRegularExpression('/pong/is', $response, "Test PING via Bash connector.");
    }
    
    public function testGizmoreOp()
    {
        $user = $this->userGizmore1();
        GDO_UserPermission::table()->grant($user, DOG::VOICE);
        GDO_UserPermission::table()->grant($user, DOG::HALFOP);
        GDO_UserPermission::table()->grant($user, DOG::OPERATOR);
        GDO_UserPermission::table()->grant($user, DOG::OWNER);
        assertTrue($user->hasPermission(Dog::OWNER), 'Test if gizmore{1} is owner.');
    }
    
    public function testCoreMethods()
    {
    	$this->userGizmore1();
    	$r = $this->bashCommand("user.whoami");
        assertStringContainsString('gizmore{1}', $r, 'Test the whoami command.');
    }
    
    public function testHelpCommand()
    {
        $r = $this->bashCommand("help");
        assertStringContainsString('Print short help for a method', $r, 'Test the help command for command overview.');
    }
    
}
