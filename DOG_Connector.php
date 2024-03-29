<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\GDT;
use GDO\Core\WithName;

/**
 * An abstract connector for the dog chatbot.
 */
abstract class DOG_Connector
{

	use WithName;

//    public function getGDOUsername(DOG_User $user): string
//    {
//        return "{$user->displayFullName()}";
//    }

	/**
	 * @var DOG_Connector[]
	 */
	private static array $connectors = [];

	public DOG_Server $server;

	public bool $connected = false;

	/**
	 * @return DOG_Connector[]
	 */
	public static function connectors(): array
	{
		return self::$connectors;
	}

	public static function connector(string $name): ?DOG_Connector
	{
		if (isset(self::$connectors[$name]))
		{
            $connectorName = self::$connectors[$name];
            $conn = new $connectorName();
			if ($conn->init())
			{
				return $conn;
			}
		}
		return null;
	}

	###

	abstract public function init(): bool;

	public static function register(DOG_Connector $connector): void
	{
		self::$connectors[$connector->gdoShortName()] = $connector->gdoClassName();
	}

	public function getID(): string
	{
		return $this->gdoShortName();
	}

	public function renderName(): string
	{
		return t('connector_' . $this->gdoShortName());
	}

    public function getDog(): DOG_User
    {
        return $this->server->getDog();
    }

	###

	public function server(DOG_Server $server): static
	{
		$this->server = $server;
		return $this;
	}

	public function connected(bool $connected): static
	{
		$this->connected = $connected;
        $this->onConnected();
		return $this;
	}

    protected function onConnected(): void
    {

    }

	public function obfuscate(string $string): string
	{
		return $string;
	}

	public function gdtRenderMode(): int
	{
		return GDT::RENDER_CLI;
	}

	public function send(string $text): bool
	{
		return Dog::instance()->event('dog_send', $text);
	}

	public function sendNoticeToUser(DOG_User $user, string $text): bool
	{
		return Dog::instance()->event('dog_send_notice_to_user', $user, $text);
	}

	public function reply(DOG_Message $message, string $text): bool
	{
		if (isset($message->room))
		{
			$text = $message->user->renderFullName() . ': ' . $text;
			return $this->sendToRoom($message->room, $text);
		}
		else
		{
			return $this->sendToUser($message->user, $text);
		}
	}

	public function getName(): ?string
	{
		return $this->gdoShortName();
	}

	public function sendToRoom(DOG_Room $room, string $text): bool
	{
		return Dog::instance()->event('dog_send_to_room', $room, $text);
	}

	public function sendToUser(DOG_User $user, string $text): bool
	{
		return Dog::instance()->event('dog_send_to_user', $user, $text);
	}

	public function getNickname(): string
	{
		return Module_Dog::instance()->cfgDefaultNickname();
	}

	abstract public function connect(): bool;

	abstract public function disconnect(string $reason): void;

	abstract public function readMessage(): bool;

	abstract public function setupServer(DOG_Server $server): void;

    public function getTrigger(): string
    {
        return "@{$this->server->getDog()->getName()} ";
    }

    public function hasUserSubscribedRoom(DOG_User $user, DOG_Room $param): bool
    {
        return false;
    }

    public function getSubscriberModule(): ?string
    {
        return null;
    }

    public function getGDOUserName(string $username, DOG_Server $server): string
    {
        return sprintf('%s{%s}', $username, $server->getID());
    }

}
