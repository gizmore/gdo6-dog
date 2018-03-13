<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\DB\GDT_String;
use GDO\Dog\DOG_Message;

final class Echoes extends DOG_Command
{
	public function gdoParameters()
	{
		return array(
			GDT_String::make('text'),
		);
	}
	
	public function dogExecute(DOG_Message $message, $text)
	{
		$message->reply($text);
	}
}

DOG_Command::register(new Echoes());
