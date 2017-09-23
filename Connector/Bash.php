<?php
namespace GDO\Dog\Connector;

use GDO\Dog\Connector;

class Bash extends Connector
{
    public function connect()
    {
        
    }
    
    
}

Connector::register(new Bash());
