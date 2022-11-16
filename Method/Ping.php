<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;

/**
 * Users can send a ping and get a pong back.
 * 
 * @author gizmore
 * @version 7.0.1
 */
class Ping extends DOG_Command
{
    
    public function getCLITrigger()
    {
    	return 'ping';
    }
    
    public function dogExecute(DOG_Message $message)
    {
    	return $message->rply('dog_pong');
    }

}
