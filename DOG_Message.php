<?php
namespace GDO\Dog;

use GDO\Language\Trans;
use GDO\User\GDO_User;

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
	public function user(DOG_User $user)
	{
	    $this->user = $user;
	    GDO_User::setCurrent($user->getGDOUser());
	    Trans::setISO($user->getGDOUser()->getLangISO());
	    return $this;
	}

	/**
	 * @return \GDO\User\GDO_User
	 */
	public function getGDOUser() { return $this->user->getGDOUser(); }
	
	public $text;
	public function text($text) { $this->text = $text; return $this; }
	
	###############
	### Methods ###
	###############
	public function rply($key, $args=null)
	{
		return $this->reply($this->t($key, $args));
	}
	
	public function reply($text)
	{
		$this->server->getConnector()->reply($this, $text);
		return true;
	}

	public function t($key, $args=null)
	{
        $text = t($key, $args);
        $bot = $this->server->getUsername();
        $trigger = $this->room ? $this->room->getTrigger() : '';
        $text = str_replace(['#BOT#', '#CMD#'], [$bot, $trigger], $text);
        return $text;
	}

}
