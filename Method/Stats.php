<?php
namespace GDO\Dog\Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;

final class Stats extends DOG_Command
{
    public $trigger = 'stats';
    
    public function dogExecute(DOG_Message $message)
    {
        $servcount = 0;
        $roomcount = 0;
        $usercount = 0;
        foreach (Dog::instance()->servers as $server)
        {
            $servcount++;
            $roomcount += count($server->rooms);
            $usercount += count($server->users);
        }
        $message->rply('msg_dog_stats', [$servcount, $roomcount, $usercount]);
    }
    
}
