<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Util\Strings;

final class Exec extends DOG_Command
{
	public function dog_message(DOG_Message $message)
	{
	    $text = $message->text;
	    if ($message->room)
	    {
	        if (!Strings::startsWith($text, $message->room->getTrigger()))
	        {
	            return;
	        }
	        $text = substr($text, 1);
	    }
		$trigger = Strings::substrTo($text, ' ', $text);
		if ($command = DOG_Command::byTrigger($trigger))
		{
			$command->onDogExecute($message);
		}
	}

}
