<?php
namespace GDO\Dog\Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;

class Ping extends DOG_Command
{
    public $trigger = 'ping';
    
    public function dogExecute(DOG_Message $message, ...$argv)
    {
    	return $message->rply('dog_pong');
    }

}
