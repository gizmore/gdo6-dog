<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\Dog;
use GDO\Dog\GDT_DogString;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Server;

final class Quit extends DOG_Command
{
    public $group = 'Maintainance';
    public $trigger = 'quit';
    
    public function getPermission() { return Dog::OWNER; }
    
    public function gdoParameters()
    {
        return array(
            GDT_DogString::make('text'),
        );
    }
    
    public function dogExecute(DOG_Message $message, $text)
    {
        foreach (DOG_Server::table()->all() as $server)
        {
            $server->getConnector()->disconnect($text);
        }
        Dog::instance()->running = false;
    }
    
}
