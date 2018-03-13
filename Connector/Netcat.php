<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Connector;

class Netcat extends DOG_Connector
{
	public function readMessage()
	{
	}

	public function connect()
	{
	}


    
}

DOG_Connector::register(new Netcat());
