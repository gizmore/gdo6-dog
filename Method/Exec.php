<?php
declare(strict_types=1);
namespace GDO\Dog\Method;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Expression\Parser;
use GDO\Core\GDO_ArgException;
use GDO\Core\GDO_NoSuchCommandError;
use GDO\Core\GDO_NoSuchMethodError;
use GDO\Core\GDT_Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\UI\GDT_Page;


/**
 * Listens to the `dog_message` event and calls a command.
 *
 * @version 7.0.3
 * @since 6.10.2
 * @author gizmore
 */
final class Exec extends DOG_Command
{

//	public function isHiddenMethod() { return true; }

	protected function isRoomMethod(): bool { return false; }

	protected function isPrivateMethod(): bool { return false; }

	public function dog_message(DOG_Message $message)
	{
		$text = $message->text;

		Application::$MODE = $message->server->getConnector()->gdtRenderMode();

		# Remove trigger char if inside room.
		if (isset($message->room))
		{
			if (!str_starts_with($text, $message->room->getTrigger()))
			{
				return null;
			}
			$text = substr($text, 1);
		}

		try
		{
			$parser = new Parser();
			$exp = $parser->parse($text);
			$exp->method->runAs($message->user->getGDOUser());

			if (!$this->isMethodEnabled($exp->method, $message))
			{
				return $this->error('err_dog_disabled');
			}

			$result = $exp->execute();
//			$text = CLI::getTopResponse();
			$text = $result->render();
			if (Application::isError())
			{
				$text .= CLI::renderCLIHelp($exp->method->method);
			}


			return $message->reply($text);
		}
		catch (GDO_ArgException $ex)
		{
			global $me;
			$text = $ex->getMessage();
			$text .= CLI::renderCLIHelp($me);
			return $message->reply($text);
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
