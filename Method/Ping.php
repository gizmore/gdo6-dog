<?php
declare(strict_types=1);
namespace GDO\Dog\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;

/**
 * Users can send a ping and get a pong back.
 *
 * @version 7.0.3
 * @author gizmore
 */
class Ping extends DOG_Command
{

	public function getCLITrigger(): string
	{
		return 'ping';
	}

	public function dogExecute(DOG_Message $message): GDT
    {
		$message->rply('dog_pong');
        return GDT_Response::make();
	}

}
