<?php
namespace GDO\Dog;

class DOG_Message
{
    public static $LAST_MESSAGE = null;
    
    public function __construct()
    {
        self::$LAST_MESSAGE = $this;
    }
    
	/**
	 * return self
	 */
	public static function make() { return new self(); }
	
	#################
	### Variables ###
	#################
	/**
	 * @var DOG_Server
	 */
	public $server;
	public function server(DOG_Server $server) { $this->server = $server; return $this; }
	
	/**
	 * @var DOG_Room
	 */
	public $room;
	public function room(DOG_Room $room=null) { $this->room = $room; return $this; }
	
	/**
	 * @var DOG_User
	 */
	public $user;
	public function user(DOG_User $user) { $this->user = $user; return $this; }

	public function getUser() { return $this->user->getGDOUser(); }
	
	public $text;
	public function text($text) { $this->text = $text; return $this; }
	
	
	###############
	### Methods ###
	###############
	public function rply($key, $args=null)
	{
		$this->reply(t($key, $args));
	}

	public function reply($text)
	{
		$this->server->getConnector()->reply($this, $text);
	}
}
