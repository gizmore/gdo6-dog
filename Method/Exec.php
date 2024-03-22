<?php
declare(strict_types=1);
namespace GDO\Dog\Method;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Expression\Parser;
use GDO\Core\GDO_NoSuchCommandError;
use GDO\Core\GDT;
use GDO\Core\GDT_Method;
use GDO\Core\GDT_Response;
use GDO\DB\Database;
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


	public function dog_message(DOG_Message $message): GDT
	{
		$text = $message->text;

		Application::$MODE = $message->server->getConnector()->gdtRenderMode();

		# Remove trigger char if inside room.
		if (isset($message->room))
		{
			if (!str_starts_with($text, $message->room->getTrigger()))
			{
                return GDT_Response::make();
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

            GDT_Page::instance()->reset();
            Application::$RESPONSE_CODE = 200;
            Database::instance()->transactionBegin();
            $result = $exp->execute();
            $text = GDT_Page::instance()->topResponse()->render();
			$text .= $result->render();
			if (Application::isError())
			{
				$text .= ' ' . CLI::renderCLIHelp($exp->method->method);
			}
            if (trim($text))
            {
                return $message->reply($text);
            }
            return GDT_Response::make();
		}
        catch (GDO_NoSuchCommandError $ex)
        {
            $message->reply($ex->getMessage());
            Database::instance()->transactionRollback();
        }
		catch (\Throwable $ex)
		{
			echo Debug::debugException($ex);
            $message->reply($ex->getMessage());
            Database::instance()->transactionRollback();
		}
        finally
        {
            Database::instance()->transactionEnd();
        }
        return GDT_Response::make();
    }

	private function isMethodEnabled(GDT_Method $method, DOG_Message $message): bool
	{
		return true;
	}

	protected function isRoomMethod(): bool { return false; }

	protected function isPrivateMethod(): bool { return false; }


}
