<?php
namespace GDO\Dog\Test;

use GDO\Tests\TestCase;
use GDO\Dog\Dog;
use function PHPUnit\Framework\assertTrue;
use GDO\Dog\DOG_Connector;
use GDO\Dog\Connector\Bash;

final class DogTest extends TestCase
{
    public function testDogCreation()
    {
        $dog = new Dog();
        $result = $dog->loadPlugins();
        assertTrue($result, 'Assert that dog can load plugins.');
        $dog->init();
    }
    
    public function testBashConnector()
    {
        $bash = DOG_Connector::connector('Bash');
        assertTrue($bash instanceof Bash);
    }
    
}
