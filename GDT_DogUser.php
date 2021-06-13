<?php
namespace GDO\Dog;

use GDO\DB\GDT_Object;
use GDO\Core\GDO;
use GDO\Util\Strings;
use GDO\Util\Arrays;

/**
 * GDT_DogUser parameter is like an object but features a few options:
 * 
 * online - only online users are allowed
 * same_room - user has to be in the same room
 * same_server - user has to be on the same server
 * exact - username might not be abbreviated (deprecated)
 * thyself - user might choose thyself
 * @TODO deleted - also select deleted users
 * @TODO registrered - only select registered users
 * @TODO authenticated - only select registered and authenticated users
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.0
 */
final class GDT_DogUser extends GDT_Object
{
    protected function __construct()
    {
        parent::__construct();
        $this->table = DOG_User::table();
    }
    
    ###############
    ### Options ###
    ###############
    public $online = false;
    public function online($online=true)
    {
        $this->online = $online;
        return $this;
    }
    
    public $sameRoom = false;
    public function sameRoom($sameRoom=true)
    {
        $this->sameRoom = $sameRoom;
        return $this;
    }
    
    public $sameServer = false;
    public function sameServer($sameServer=true)
    {
        $this->sameServer = $sameServer;
        return $this;
    }
    
    public $exact = false;
    public function exact($exact=true)
    {
        $this->exact = $exact;
        return $this;
    }
    
    public $thyself = false;
    public function thyself($thyself=true)
    {
        $this->thyself = $thyself;
        return $this;
    }
    
    ######################
    ### Ambigious hack ###
    ######################
    private $ambigious = false;
    
    ##################
    ### GDT_Object ###
    ##################
    /**
     * Always use the findByName method.
     */
    public function toValue($var)
    {
        $_REQUEST['nocompletion_'.$this->name] = 1;
        return parent::toValue($var);
    }
    
    public function findByName($name)
    {
        $this->ambigious = false;
        
        $server = DOG_Message::$LAST_MESSAGE->server;

        if (!$this->sameServer)
        {
            $matches = null;
            if (preg_match('/\\{(\\d+)\\}$/iuD', $name, $matches))
            {
                $server = DOG_Server::findById($matches[1]);
            }
        }
        
        $_name = Strings::substrTo($name, '{', $name);
        $ename = GDO::escapeSearchS($_name);
        
        $query = $this->table->select()->
        where("doguser_name LIKE '%$ename%' ")->
        where("doguser_service = 0")->
        limit(10);
        
        if ($this->sameServer)
        {
            $query->where("doguser_server={$server->getID()}");
        }

        /**
         * @var DOG_User[] $users
         */
        $users = $query->exec()->fetchAllObjects();
        
        # Evaluate result set by size
        switch (count($users))
        {
            case 0:
                return null;
                
            case 1:
                return $users[0];
                
            default:
                foreach ($users as $user)
                {
                    if (!$this->sameServer)
                    {
                        if ($user->getFullName() === $name)
                        {
                            return $user; # exact match
                        }
                    }
                    else
                    {
                        if ($user->getName() === $name)
                        {
                            return $user;
                        }
                    }
                }
                
                # Try beginning of name
                $possible = [];
                foreach ($users as $user)
                {
                    if (stripos($user->displayFullName(), $name) === 0)
                    {
                        $possible[] = $user;
                    }
                }
                if (count($possible)===1)
                {
                    return $possible[0];
                }

                # Multiple matches!
                # Return a dummy user but mark as ambigous for validation.
                $this->ambigious = array_map(function($user){
                    return $user->displayFullName();}, $users);
                return $users[0];
        }
    }
        
    /**
     * Validate object first, then options.
     */
    public function validate($value)
    {
        if (!parent::validate($value))
        {
            return false;
        }

        if (!$value)
        {
            return true; # null
        }
        
        /**
         * @var DOG_User $user
         */
        $user = $value;
        
        if ($this->online)
        {
            if (!($user->isOnline()))
            {
                return $this->error('err_not_online', [$user->displayFullName()]);
            }
        }
        
        if ($this->sameServer)
        {
            if ($user->getServerID() != DOG_Message::$LAST_MESSAGE->server->getID())
            {
                return $this->error('err_not_same_server', [$user->displayFullName()]);
            }
        }
        
        if ($this->sameRoom)
        {
            if (!($room = DOG_Message::$LAST_MESSAGE->room))
            {
                return $this->error('err_not_same_room', [$user->displayFullName()]);
            }
            if (!$room->hasUser($user))
            {
                return $this->error('err_not_same_room', [$user->displayFullName()]);
            }
        }
        
        if ($this->exact)
        {
            $input = $this->getVar();
            $input = Strings::substrTo($input, '{', $input);
            if ($input !== $user->getName())
            {
                return $this->error('err_exact_username', [$user->displayFullName()]);
            }
        }
        
        if ($this->ambigious !== false)
        {
            return $this->error('err_username_ambigous', [Arrays::implodeHuman($this->ambigious)]);
        }
        
        if (!$this->thyself)
        {
            if ($user === DOG_Message::$LAST_MESSAGE->user)
            {
                return $this->error('err_user_thyself');
            }
        }
        
        return true;
    }
    
}
