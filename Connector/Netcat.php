<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Connector;
use GDO\Dog\DOG_Room;
use GDO\Dog\DOG_User;

/**
 * This connector is raw tcp/ip which can be used with e.g. netcat.
 * @author gizmore
 */
class Netcat extends DOG_Connector
{
	public function connect()
	{
	}

	public function disconnect($reason)
	{
		
	}
    
	public function readMessage()
	{
	}
	
    public function sendToRoom(DOG_Room $room, $text)
    {
    }

    public function sendToUser(DOG_User $user, $text)
    {
    }
    
    public function sendNoticeToUser(DOG_User $user, $text)
    {
    }

}
