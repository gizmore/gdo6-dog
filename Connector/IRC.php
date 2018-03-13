<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Connector;
use GDO\Core\Logger;

class IRC extends DOG_Connector
{
	private $socket;
	private $context;
	
    public function connect()
    {
    	if (false === ($this->context = @stream_context_create()))
    	{
    		Logger::logError('IRC Connector cannot create stram context.');
    		return false;
    	}
    }
    
	public function readMessage()
	{
	}

    
}

DOG_Connector::register(new IRC());
