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
    
    
    ###
    public $connected = false;
    public function connected($connected)
    {
        $this->connected = $connected;
        return $this;
    }
    
    public function init() {}
    
    public abstract function sendTo($receiver, $message);
    public function reply(DOG_Message $message, $text)
    {
    	$receiver = $message->user;
    	if ($message->room)
    	{
    		$text = $message->user->displayName() . ": " . $text;
    		$receiver = $message->room;
    	}
    	$this->sendTo($receiver, $text);
    }
    
    public abstract function connect();
    /**
     * DOG_Message
     */
    public abstract function readMessage();

}
