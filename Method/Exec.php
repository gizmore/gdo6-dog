<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Util\Strings;

/**
 * Listens to the `dog_message` event and calls a command.
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.2
 */
final class Exec extends DOG_Command
{
	public function dog_message(DOG_Message $message)
	{
	    $text = $message->text;
	    
	    # Remove trigger char if inside room.
	    if ($message->room)
	    {
	        if (!Strings::startsWith($text, $message->room->getTrigger()))
	        {
	            return;
	        }
	        $text = substr($text, 1);
	    }
	    
		$trigger = strtolower(Strings::substrTo($text, ' ', $text));
		
		if ($command = DOG_Command::byTrigger(trim($trigger, '.')))
		{
			$command->onDogExecute($message);
		}
		
		# Private message with accidental trigger char. Just try with 1 chop.
		elseif ($command = DOG_Command::byTrigger(substr($trigger, 1)))
		{
		    $command->onDogExecute($message);
		}
	}

}
