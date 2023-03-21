<?php
namespace GDO\Dog;

use GDO\User\GDO_User;

class DOG_Message
{

	public static $LAST_MESSAGE = null;
	public ?DOG_Server $server = null;
	public ?DOG_Room $room = null;

	#################
	### Variables ###
	#################
	public ?DOG_User $user = null;
	public $text;

	public function __construct()
	{
		self::$LAST_MESSAGE = $this;
	}

	/**
	 * return self
	 */
	public static function make() { return new self(); }

	public function server(DOG_Server $server)
	{
		$this->server = $server;
		return $this;
	}

	public function room(DOG_Room $room = null)
	{
		$this->room = $room;
		return $this;
	}

	public function user(DOG_User $user)
	{
		$this->user = $user;
		$gdoUser = $user->getGDOUser();
		GDO_User::setCurrent($gdoUser);
		return $this;
	}

	/**
	 * @return GDO_User
	 */
	public function getGDOUser() { return $this->user->getGDOUser(); }

	public function text($text)
	{
		$this->text = $text;
		return $this;
	}

	###############
	### Methods ###
	###############
	public function rply($key, $args = null)
	{
		return $this->reply($this->t($key, $args));
	}

	public function reply($text)
	{
		$this->server->getConnector()->reply($this, $text);
		return true;
	}

	public function t($key, $args = null)
	{
		$text = t($key, $args);
		$bot = $this->server->getUsername();
		$trigger = isset($this->room) ? $this->room->getTrigger() : '';
		$text = str_replace(['#BOT#', '#CMD#'], [$bot, $trigger], $text);
		return $text;
	}

}
