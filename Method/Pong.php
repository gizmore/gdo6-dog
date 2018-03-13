<?php
namespace GDO\Dog\Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;

class Pong extends DOG_Command
{
    public function dogExecute(DOG_Message $message, ...$argv)
    {
    	return $message->rply('dog_pong');
    }
}

DOG_Command::register(new Pong());
