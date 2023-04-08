<?php
declare(strict_types=1);
namespace GDO\Dog\Connector;

use GDO\Dog\Dog;
use GDO\Dog\DOG_Connector;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Room;
use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_User;
use GDO\User\GDO_User;

/**
 * This connector can be called by shell: "GDO/Dog/bin/dog <command> [<..parameters..>]"
 * It is the default and a required connector.
 * You can dog add_server IRC irc://irc.freenode.net:6667 with it.
 *
 * @version 7.0.3
 * @since 6.10.0
 * @author gizmore
 */
class Bash extends DOG_Connector
{

	public static function instance(): self
	{
		if (!isset(self::$INSTANCE))
		{
			self::$INSTANCE = new self();
			self::$INSTANCE->init();
		}
		return self::$INSTANCE;
	}


	public static self $INSTANCE;



	public static DOG_Server $BASH_SERVER;


	public function init(): bool
	{
		if (!isset(self::$BASH_SERVER))
		{
			if (!($srv = $this->getBashServer()))
			{
				$srv = DOG_Server::blank([
					'serv_connector' => $this->gdoShortName(),
				])->insert();
			}
			self::$BASH_SERVER = $srv;
			self::$BASH_SERVER->setConnector($this);
		}
		return true;
	}

	private function getBashServer(): ?DOG_Server
	{
		return DOG_Server::table()->select()->
		where("serv_connector='{$this->gdoShortName()}'")->
		first()->exec()->fetchObject();
	}

	public function connect(): bool
	{
		$this->connected = true;
		return true;
	}

	public function disconnect($reason): void
	{
		echo "Disconnecting: {$reason}\n";
		$this->connected = false;
	}

	public function readMessage(): ?DOG_Message
	{
		return null;
	}

	public function send(string $text): bool
	{
		echo "{$this->server->renderName()} >> {$text}\n";
		return parent::send($text);
	}

	public function sendToUser(DOG_User $user, string $text): bool
	{
		echo "{$user->renderName()} >> {$text}\n";
		return parent::sendToUser($user, $text);
	}

	public function sendToRoom(DOG_Room $room, string $text): bool
	{
		echo "{$this->server->renderName()} >> {$text}\n";
		return parent::sendToRoom($room, $text);
	}

	public function sendNoticeToUser(DOG_User $user, string $text): bool
	{
		echo "{$user->renderName()} >> NOTE: {$text}\n";
		return parent::sendNoticeToUser($user, $text);
	}

	public function dog_cmdline(string...$argv): void
	{
		$text = implode(' ', $argv);
		$this->dog_cmdline2($text);
	}

	public function dog_cmdline2(string $text): void
	{
		$msg = DOG_Message::make()->
		server(self::$BASH_SERVER)->
		user($this->getBashUser())->
		text($text);
		Dog::instance()->event('dog_message', $msg);
	}

	public function getBashUser(): DOG_User
	{
		$user = DOG_User::getOrCreateUser(self::$BASH_SERVER, get_current_user());
		GDO_User::setCurrent($user->getGDOUser());
		$user->login();
		return $user;
	}

	public function setupServer(DOG_Server $server): void
	{
	}

}
