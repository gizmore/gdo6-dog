<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Connector;

class Netcat extends DOG_Connector
{
	public function connect()
	{
	}

	public function disconnect()
	{
		
	}
    
	public function readMessage()
	{
	}
	public function sendTo($er, $text)
	{
		
	}
}

DOG_Connector::register(new Netcat());