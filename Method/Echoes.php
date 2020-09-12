<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_DogString;

final class Echoes extends DOG_Command
{
    public $group = 'Chat';
    public $trigger = 'echo';

    public function isWebMethod() { return true; }
	
	public function gdoParameters()
	{
		return array(
			GDT_DogString::make('text')->notNull(),
		);
	}
	
	public function dogExecute(DOG_Message $message, $text)
	{
		$message->reply($text);
	}

}
