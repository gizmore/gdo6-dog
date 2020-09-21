<?php
namespace GDO\Dog;
use GDO\Core\WithName;

abstract class DOG_Connector
{
    use WithName;
    
    /**
     * @var DOG_Connector[]
     */
    private static $connectors = [];
    
    /**
     * @return DOG_Connector[]
     */
    public static function connectors() { return self::$connectors; }
    
    /**
     * @return DOG_Connector
     */
    public static function connector($name) { return @self::$connectors[$name]; }
    public static function register(DOG_Connector $connector)
    {
        self::$connectors[$connector->gdoShortName()] = $connector;
    }

    public function displayName() { return t('connector_' . $this->gdoShortName()); }
    
    ###
    
    /**
     * @var DOG_Server
     */
    public $server;
    public function server(DOG_Server $server) { $this->server = $server; return $this; }
    
    public function setupServer(DOG_Server $server) {}
    
    public function getName() { return $this->gdoShortName(); }
    
    ###
    public $connected = false;
    public function connected($connected)
    {
        $this->connected = $connected;
        return $this;
    }
    
    public function init() {}
    public function obfuscate($string) { return $string; }
    
    public abstract function sendToUser(DOG_User $user, $text);
    public abstract function sendToRoom(DOG_Room $room, $text);
    public abstract function sendNoticeToUser(DOG_User $user, $text);
    
    public function reply(DOG_Message $message, $text)
    {
    	if ($message->room)
    	{
    		$text = $message->user->getName() . ": " . $text;
    		$this->sendToRoom($message->room, $text);
    	}
    	else
    	{
        	$this->sendToUser($message->user, $text);
    	}
    }
    
    public abstract function connect();

    public abstract function disconnect($reason);

    /**
     * DOG_Message
     */
    public abstract function readMessage();

}
