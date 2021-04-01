<?php
namespace GDO\Dog;

use GDO\Tests\MethodTest;
use GDO\Tests\TestCase;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\Dog\Connector\Bash;
use GDO\Util\Strings;

class DogTestCase extends TestCase
{
    protected $doguser;
    
    protected function getServer()
    {
        return Bash::instance();
    }
    
    public function user(GDO_User $user)
    {
        $username = Strings::substrTo($user->getName(), '{', $user->getName());
        $this->doguser = DOG_User::getOrCreateUser($this->getServer(), $username);
        return parent::user($user);
    }
    
    public function userGizmore1()
    {
        $g1 = GDO_User::getByName('gizmore{1}');
        return $this->user($g1);
    }
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->restoreUserPermissions($this->userGizmore1());
    }
    
    /**
     * Restore gizmore because auto coverage messes with him a lot.
     * @param GDO_User $user
     */
    protected function restoreUserPermissions(GDO_User $user)
    {
        if (count(MethodTest::$USERS))
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
                $table->grant($user, Dog::OWNER);
                $user->changedPermissions();
            }
        }
    }
    
    /**
     * Execute a bash command and return output.
     * @param string $line
     * @return string
     */
    public function bashCommand($line)
    {
        try
        {
            ob_implicit_flush(false);
            ob_start();
            Dog::instance()->event('dog_cmdline2', $line);
            $response = ob_get_contents();
        }
        catch (\Throwable $ex)
        {
            $response = $ex->__toString();
        }
        finally
        {
            ob_end_clean();
            ob_implicit_flush(true);
            return $response;
        }
    }
    
}
