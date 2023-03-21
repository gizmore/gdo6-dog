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
	 * @var DOG_Server
	 */
	public $server;
	public $connected = false;

	/**
	 * @return DOG_Connector[]
	 */
	public static function connectors() { return self::$connectors; }

	/**
	 * @return DOG_Connector
	 */
	public static function connector($name)
	{
		/** @var $conn DOG_Connector * */
		if ($connectorName = @self::$connectors[$name])
		{
			$conn = new $connectorName();
			$conn->init();
			return $conn;
		}
	}

	public function init() {}

	###

	public static function register(DOG_Connector $connector)
	{
		self::$connectors[$connector->gdoShortName()] = $connector->gdoClassName();
	}

	public function getID(): ?string
	{
		return $this->gdoShortName();
	}

	public function renderName(): string { return t('connector_' . $this->gdoShortName()); }

	public function server(DOG_Server $server)
	{
		$this->server = $server;
		return $this;
	}

	###

	public function setupServer(DOG_Server $server) {}

	public function connected($connected)
	{
		$this->connected = $connected;
		return $this;
	}

	public function obfuscate($string) { return $string; }

	public function send(string $text): bool
	{
		Dog::instance()->event('dog_send', $text);
		return true;
	}

	public function sendNoticeToUser(DOG_User $user, $text)
	{
		Dog::instance()->event('dog_send_notice_to_user', $user, $text);
	}

	public function reply(DOG_Message $message, $text)
	{
		if (isset($message->room))
		{
			$text = $message->user->getName() . ': ' . $text;
			$this->sendToRoom($message->room, $text);
		}
		else
		{
			$this->sendToUser($message->user, $text);
		}
	}

	public function getName(): ?string { return $this->gdoShortName(); }

	public function sendToRoom(DOG_Room $room, $text)
	{
		Dog::instance()->event('dog_send_to_room', $room, $text);
	}

	public function sendToUser(DOG_User $user, $text)
	{
		Dog::instance()->event('dog_send_to_user', $user, $text);
	}

	public function getNickname()
	{
		return Module_Dog::instance()->cfgDefaultNickname();
	}

	abstract public function connect();

	abstract public function disconnect($reason);

	/**
	 * DOG_Message
	 */
	abstract public function readMessage();

}
