<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_Char;
use GDO\Core\GDT_Secret;
use GDO\Core\GDT_String;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;

/**
 * A chatroom.
 *
 * @author gizmore
 */
class DOG_Room extends GDO
{

	/**
	 * @var DOG_User[]
	 */
	public $users = [];

	###########
	### GDO ###
	###########

	public static function getOrCreate(DOG_Server $server, $roomName, $description = null)
	{
		if ($room = self::getByName($server, $roomName))
		{
			return $room->saveVar('room_description', $description);
		}
		return self::create($server, $roomName, $description);
	}

	##############
	### Getter ###
	##############

	public static function getByName(DOG_Server $server, string $roomName): ?DOG_Room
	{
		if ($room = $server->getRoomByName($roomName))
		{
			return $room;
		}
		$name = GDO::quoteS($roomName);
		return self::table()->select()->
		where("room_server={$server->getID()} AND room_name={$name}")->
		first()->exec()->fetchObject();
	}

	public static function create(DOG_Server $server, string $roomName, string $description = null): DOG_Room
	{
		return self::blank([
			'room_server' => $server->getID(),
			'room_name' => $roomName,
			'room_description' => $description,
		])->insert();
	}

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('room_id'),
			GDT_Server::make('room_server')->notNull(),
			GDT_String::make('room_name')->notNull()->max(64),
			GDT_Secret::make('room_password')->max(64),
			GDT_Char::make('room_trigger')->length(1)->initial('$')->notNull(),
			GDT_String::make('room_description')->max(512),
			GDT_Language::make('room_lang')->notNull()->initial(GDO_LANGUAGE),
		];
	}

	public function getName(): string { return $this->gdoVar('room_name'); }

	public function getServerID(): string { return $this->gdoVar('room_server'); }

	public function getPassword(): string { return $this->gdoVar('room_password'); }

	public function getTrigger(): string { return $this->gdoVar('room_trigger'); }

	############
	### Send ###
	############

	public function getLanguage(): GDO_Language
	{
		return $this->gdoValue('room_lang');
	}

	##############
	### Static ###
	##############

	public function getLanguageISO(): string { return $this->gdoVar('room_lang'); }

	public function send($text)
	{
		$this->getServer()->getConnector()->sendToRoom($this, $text);
	}

	/**
	 * @return DOG_Server
	 */
	public function getServer() { return $this->gdoValue('room_server'); }

	##############
	### Events ###
	##############

	public function disconnect($text)
	{
		$this->users = [];
	}

	#############
	### Users ###
	#############
	public function hasUser(DOG_User $user = null)
	{
		return $user ? isset($this->users[$user->getID()]) : false;
	}

	public function addUser(DOG_User $user)
	{
		$userId = $user->getID();
		if (!isset($this->users[$userId]))
		{
			$this->users[$userId] = $user;
		}
	}

	public function removeUser(DOG_User $user)
	{
		unset($this->users[$user->getID()]);
	}

}
