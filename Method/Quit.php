<?php
namespace GDO\Dog\Method;

use GDO\Dog\Dog;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Server;
use GDO\Dog\GDT_DogString;

final class Quit extends DOG_Command
{

	public $trigger = 'die';

	public function getPermission(): ?string { return Dog::ADMIN; }

	public function gdoParameters(): array
	{
		return [
			GDT_DogString::make('text'),
		];
	}

	public function dogExecute(DOG_Message $message, $text)
	{
		foreach (DOG_Server::table()->all() as $server)
		{
			$server->disconnect($text);
		}
		Dog::instance()->running = false;
	}

}
