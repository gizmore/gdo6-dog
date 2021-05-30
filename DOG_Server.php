<?php
namespace GDO\Dog;
use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\User\GDT_Username;
use GDO\Core\GDT_Secret;
use GDO\Net\GDT_Url;
use GDO\DB\GDT_Checkbox;
use GDO\Date\GDT_Duration;
use GDO\Net\URL;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_UInt;
use GDO\Core\Application;
use GDO\Util\Common;

final class DOG_Server extends GDO
{
	/**
	 * @var DOG_Connector
	 */
	private $connector;
	
	##############
	### Online ###
	##############
	/**
	 * @var DOG_User[]
	 */
	public $users = [];
	
	/**
	 * @var DOG_Room[]
	 */
	public $rooms = [];
	
	###########
	### GDO ###
	###########
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('serv_id'),
        	GDT_Url::make('serv_url'),
            GDT_Checkbox::make('serv_tls')->initial('0')->notNull(),
            GDT_Connector::make('serv_connector')->notNull(),
            GDT_Username::make('serv_username')->initial('Dog')->notNull(),
            GDT_Secret::make('serv_password'),
            GDT_Duration::make('serv_connect_timeout')->initial('3s')->notNull(),
            GDT_UInt::make('serv_throttle')->initial('4')->notNull(),
            GDT_Checkbox::make('serv_active')->initial('1')->notNull(),
            GDT_CreatedAt::make('serv_created'),
            GDT_CreatedBy::make('serv_creator'),
        );
    }

    ##############
    ### Getter ###
    ##############
    public function isTLS() { return $this->getValue('serv_tls'); }
    public function isActive() { return $this->getVar('serv_active') === '1'; }
    
    public function getUsername() { return $this->getVar('serv_username'); }
    public function getPassword() { return $this->getVar('serv_password'); }
    
    public function displayName()
    {
        $b = $this->isConnected() ? "\x02" : '';
        $name = $this->getURL() ? $this->getDomain() : $this->getConnectorName();
        return sprintf('%s%s%s-%s', $b, $this->getID(), $b, $name);
    }
    
    public function isConnected()
    {
        return $this->connector && $this->connector->connected;
    }
    
    /**
     * @return URL
     */
    public function getDomain($short=false)
    {
        return $short ? $this->getURL()->getTLD() : $this->getURL()->getHost();
    }
    
    /**
     * @return URL
     */
    public function getURL() { return $this->getValue('serv_url'); }
    public function getConnectTimeout() { return $this->getValue('serv_connect_timeout'); }
    public function getThrottle() { return $this->getValue('serv_throttle'); }
    
    public function getConnectURL()
    {
        $url = $this->getURL();
        if ($url)
        {
            $host = $url->getHost();
            if ($port = $url->getPort())
            {
                $host .= ':' . $port;
            }
            return $host;
        }
    }

    ##################
    ### Connection ###
    ##################
    public $connectionAttemptMax = 10000;
    private $connectionAttemptNum = 0;
    private $connectionAttemptTime = 0;
    private $connectionAttemptNext = 0;
    
    public function resetConnectionAttempt()
    {
        $this->connectionAttemptNext = $this->connectionAttemptNum = $this->connectionAttemptTime = 0;
    }
    
    public function shouldConnect()
    {
        if ($this->shouldGiveUp())
        {
            return false;
        }
        return Application::$TIME >= $this->connectionAttemptNext;
    }
    
    public function shouldGiveUp()
    {
        return $this->connectionAttemptNum >= $this->connectionAttemptMax;
    }
    
    public function nextAttempt()
    {
        $this->connectionAttemptNum++;
        $wait = 5 * $this->connectionAttemptNum;
        $wait = Common::clamp($wait, 0, 600);
        $this->connectionAttemptNext =  Application::$TIME + $wait;
    }
    
    public function getConnectorName() { return $this->getVar('serv_connector'); }
    /**
     * @return \GDO\Dog\DOG_Connector
     */
    public function getConnector()
    {
    	if (!$this->connector)
    	{
	    	$this->setConnector(DOG_Connector::connector($this->getConnectorName()));
    	}
    	return $this->connector;
	}
	
	public function setConnector(DOG_Connector $connector)
	{
	    $this->connector = $connector;
	    $this->connector->server($this);
	    return $this;
	}
    
	##############
	### Static ###
	##############
    /**
     * @param string $url
     * @return self
     */
    public static function getByURL($url)
    {
        $url = GDO::escapeSearchS($url);
        return self::table()->getWhere("serv_url LIKE '%$url%'");
    }

    /**
     * @param string $url
     * @return self
     */
    public static function getByArg($url)
    {
        if ($server = self::getById($url))
        {
            return $server;
        }
        return self::getByURL($url);
    }
    
    #################
    ### Live Data ###
    #################
    public function disconnect($text)
    {
        foreach ($this->rooms as $room)
        {
            $room->disconnect($text);
        }
        $this->users = [];
        $this->rooms = [];
        $this->resetConnectionAttempt();
        $this->getConnector()->disconnect($text);
    }
    
    #############
    ### Rooms ###
    #############
    public function addRoom(DOG_Room $room)
    {
        if (!isset($this->rooms[$room->getID()]))
        {
            $this->rooms[$room->getID()] = $room;
            Dog::instance()->event('dog_room_added', $this, $room);
        }
    }
    
    public function hasRoom(DOG_Room $room=null)
    {
        return $room ? isset($this->rooms[$room->getID()]) : false;
    }
    
    public function getRoomByName($roomName)
    {
        foreach ($this->rooms as $room)
        {
            if ($room->getName() === $roomName)
            {
                return $room;
            }
        }
    }
    
    public function removeRoom(DOG_Room $room)
    {
        unset($this->rooms[$room->getID()]);
    }
    
    #############
    ### Users ###
    #############
    public function addUser(DOG_User $user)
    {
        if (!isset($this->users[$user->getID()]))
        {
            $this->users[$user->getID()] = $user;
            Dog::instance()->event('dog_user_added', $this, $user);
        }
    }
    
    public function hasUser(DOG_User $user)
    {
        return isset($this->users[$user->getID()]);
    }
    
    public function getUserByName($username)
    {
        foreach ($this->users as $user)
        {
            if ($user->getName() === $username)
            {
                return $user;
            }
        }
    }
    
    public function removeUser(DOG_User $user)
    {
        unset($this->users[$user->getID()]);
    }
    
    
}
