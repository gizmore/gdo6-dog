<?php
namespace GDO\Dog\Method;

use GDO\CLI\CLI;
use GDO\Core\Expression\Parser;
use GDO\Core\GDO_NoSuchCommandError;
use GDO\Core\GDO_NoSuchMethodError;
use GDO\Core\GDT_Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\UI\GDT_Page;

/**
 * Listens to the `dog_message` event and calls a command.
 *
 * @version 6.10.4
 * @since 6.10.2
 * @author gizmore
 */
final class Exec extends DOG_Command
{

	public function isHiddenMethod() { return true; }

	public function isRoomMethod() { return false; }

	public function isPrivateMethod() { return false; }

	public function dog_message(DOG_Message $message)
	{
		$text = $message->text;

		# Remove trigger char if inside room.
		if (isset($message->room))
		{
			if (!str_starts_with($text, $message->room->getTrigger()))
			{
				return;
			}
			$text = substr($text, 1);
		}

		$parser = new Parser();

		try
		{
			$exp = $parser->parse($text);

			if (!$this->isMethodEnabled($exp->method, $message))
			{
				return $this->error('err_dog_disabled');
			}

			$exp->method->runAs($message->user->getGDOUser());

			$result = $exp->execute();

			return $message->reply($result->render());
		}
		catch (GDO_NoSuchCommandError $ex)
		{
			return $message->reply($ex->getMessage());
		}
	}

	private function isMethodEnabled(GDT_Method $method, DOG_Message $message): bool
	{
		return true;
	}


}
