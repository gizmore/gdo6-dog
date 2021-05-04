<?php
namespace GDO\Dog\Test;

use GDO\Dog\Dog;
use function PHPUnit\Framework\assertTrue;
use GDO\Dog\DOG_Connector;
use GDO\Dog\Connector\Bash;
use GDO\Dog\DogTestCase;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use GDO\User\GDO_UserPermission;
use function PHPUnit\Framework\assertEquals;

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
        $bash = DOG_Connector::connector('Bash');
        assertTrue($bash instanceof Bash);
        $response = $this->bashCommand("ping");
        assertMatchesRegularExpression('/pong/is', $response);
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
        $r = $this->bashCommand("core.whoami");
        assertEquals('gizmore{1}', $r, 'Test the whoami command.');
    }
    
}
