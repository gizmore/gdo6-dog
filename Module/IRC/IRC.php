<?php
namespace GDO\Dog\Module\IRC;

use GDO\Dog\Connector;

class IRC extends Connector
{
    public function connect()
    {
        
    }
    
}

Connector::register(new IRC());
