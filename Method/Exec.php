<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Util\Strings;

final class Exec extends DOG_Command
{
	public function dog_message(DOG_Message $message)
	{
		$trigger = Strings::substrTo($message->text, ' ', $message->text);
		if ($command = DOG_Command::byTrigger($trigger))
		{
			$command->onDogExecute($message);
		}
	}
}