<?php
namespace GDO\Dog;

use GDO\User\GDO_User;

/**
 * A message for the chatbot.
 */
class DOG_Message
{

	public static $LAST_MESSAGE = null;
	public ?DOG_Server $server = null;
	public ?DOG_Room $room = null;

	public ?string $reply = null;


	#################
	### Variables ###
	#################
	public ?DOG_User $user = null;
	public $text;

	public function __construct()
	{
		self::$LAST_MESSAGE = $this;
	}

	public static function make(): static { return new static(); }

	public function server(DOG_Server $server): static
	{
		$this->server = $server;
		return $this;
	}

	public function room(DOG_Room $room = null): static
	{
		$this->room = $room;
		return $this;
	}

	public function user(DOG_User $user): static
	{
		$this->user = $user;
		$gdoUser = $user->getGDOUser();
		GDO_User::setCurrent($gdoUser);
		return $this;
	}

	public function getGDOUser(): GDO_User { return $this->user->getGDOUser(); }

	public function text(string $text): static
	{
		$this->text = $text;
		return $this;
	}

	###############
	### Methods ###
	###############
	public function rply(string $key, array $args = null): bool
	{
		return $this->reply($this->t($key, $args));
	}

	public function reply(string $text): bool
	{
		return $this->server->getConnector()->reply($this, $text);
	}

	public function t(string $key, array $args = null): array|string
	{
		$text = t($key, $args);
		$bot = $this->server->getUsername();
		$trigger = isset($this->room) ? $this->room->getTrigger() : '';
		$text = str_replace(['#BOT#', '#CMD#'], [$bot, $trigger], $text);
		return $text;
	}

	public function getReply(): ?string
	{
		return $this->reply;
	}

}
