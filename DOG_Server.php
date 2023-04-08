<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Secret;
use GDO\Core\GDT_UInt;
use GDO\Date\GDT_Duration;
use GDO\Net\GDT_Url;
use GDO\Net\URL;
use GDO\UI\TextStyle;
use GDO\User\GDT_Username;
use GDO\Util\Math;
use GDO\Util\Random;

final class DOG_Server extends GDO
{

	/**
	 * @var DOG_User[]
	 */
	public array $users = [];
	/**
	 * @var DOG_Room[]
	 */
	public array $rooms = [];

	##############
	### Online ###
	##############
	public int $connectionAttemptMax = 100;

	private DOG_Connector $connector;

	###########
	### GDO ###
	###########
	private int $connectionAttemptNum = 0;

	##############
	### Getter ###
	##############
//	private float $connectionAttemptTime = 0.0;
	private float $connectionAttemptNext = 0.0;

	public static function getByArg(string $url): ?self
	{
		if ($server = self::getById($url))
		{
			return $server;
		}
		return self::getByURL($url);
	}

	public static function getByURL(string $url): self
	{
		$url = GDO::escapeSearchS($url);
		return self::table()->getWhere("serv_url LIKE '%$url%'");
	}

	public function isTestable(): bool
	{
		return false;
	}

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('serv_id'),
			GDT_Url::make('serv_url'),
			GDT_Checkbox::make('serv_tls')->initial('0')->notNull(),
			GDT_Connector::make('serv_connector')->notNull(),
			GDT_Username::make('serv_username')->initial('Dog')->notNull()->unique(false),
			GDT_Secret::make('serv_password'),
			GDT_Duration::make('serv_connect_timeout')->initial('3s')->notNull(),
			GDT_UInt::make('serv_throttle')->initial('4')->notNull(),
			GDT_Checkbox::make('serv_active')->initial('1')->notNull(),
			GDT_CreatedAt::make('serv_created'),
			GDT_CreatedBy::make('serv_creator'),
		];
	}

	public function renderName(): string
	{
		$name = $this->getURL() ? $this->getDomain() : $this->getConnectorName();
		return sprintf('%s-%s', TextStyle::bold($this->getID(), $this->isConnected()), $name);
	}

	public function isConnected(): bool
	{
		return $this->connector->connected;
	}

	public function getURL(): ?URL
	{
		return $this->gdoValue('serv_url');
	}

	public function getDomain($short = false): string
	{
		return $short ? $this->getURL()->getTLD() : $this->getURL()->getHost();
	}

	public function getConnectorName(): string { return $this->gdoVar('serv_connector'); }

	public function isTLS(): bool { return $this->gdoValue('serv_tls'); }

	public function isActive(): bool { return $this->gdoVar('serv_active') === '1'; }

	public function getPassword(): ?string { return $this->gdoVar('serv_password'); }

	##################
	### Connection ###
	##################

	public function nextUsername(): string { return $this->getUsername() . '_' . Random::randomKey(4, Random::NUMERIC); }

	public function getUsername(): string { return $this->gdoVar('serv_username'); }

	public function getDog(): DOG_User { return DOG_User::getUser($this, $this->getNickname()); }

	public function getNickname(): string { return $this->getConnector()->getNickname(); }

	public function getConnector(): DOG_Connector
	{
		if (!isset($this->connector))
		{
			$this->setConnector(DOG_Connector::connector($this->getConnectorName()));
		}
		Application::$MODE = $this->connector->gdtRenderMode();
		return $this->connector;
	}

	public function setConnector(DOG_Connector $connector): self
	{
		$this->connector = $connector;
		$this->connector->server($this);
		return $this;
	}

	public function getConnectTimeout(): float { return $this->gdoValue('serv_connect_timeout'); }

	public function getThrottle(): int { return $this->gdoValue('serv_throttle'); }

	public function getConnectURL() :?string
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
		return null;
	}

	public function shouldConnect(): bool
	{
		if ($this->shouldGiveUp())
		{
			return false;
		}
		return Application::$MICROTIME >= $this->connectionAttemptNext;
	}

	public function shouldGiveUp(): bool
	{
		return $this->connectionAttemptNum >= $this->connectionAttemptMax;
	}

	##############
	### Static ###
	##############

	public function nextAttempt(): void
	{
		$this->connectionAttemptNum++;
		$wait = 5.0 * $this->connectionAttemptNum;
		$wait = Math::clampFloat($wait, 5.0, 600.0);
		$this->connectionAttemptNext = Application::$MICROTIME + $wait;
	}

	public function disconnect($text): void
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

	#################
	### Live Data ###
	#################

	public function resetConnectionAttempt(): void
	{
		$this->connectionAttemptNum = 0;
	}

	#############
	### Rooms ###
	#############

	public function addRoom(DOG_Room $room): void
	{
		if (!isset($this->rooms[$room->getID()]))
		{
			$this->rooms[$room->getID()] = $room;
			Dog::instance()->event('dog_room_added', $this, $room);
		}
	}

	public function hasRoom(DOG_Room $room = null): bool
	{
		return $room && isset($this->rooms[$room->getID()]);
	}

	public function getRoomByName($roomName): ?DOG_Room
	{
		foreach ($this->rooms as $room)
		{
			if ($room->getName() === $roomName)
			{
				return $room;
			}
		}
		return null;
	}

	public function removeRoom(DOG_Room $room): void
	{
		unset($this->rooms[$room->getID()]);
	}

	#############
	### Users ###
	#############
	public function addUser(DOG_User $user): void
	{
		$uid = $user->getID();
		if (!isset($this->users[$uid]))
		{
			$this->users[$uid] = $user;
			Dog::instance()->event('dog_user_added', $this, $user);
		}
	}

	public function hasUser(DOG_User $user): bool
	{
		return isset($this->users[$user->getID()]);
	}

	public function getUserByName(string $username): ?DOG_User
	{
		foreach ($this->users as $user)
		{
			if ($user->getName() === $username)
			{
				return $user;
			}
		}
		return null;
	}

	public function removeUser(DOG_User $user): void
	{
		unset($this->users[$user->getID()]);
	}


}
