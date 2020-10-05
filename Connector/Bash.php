<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Room;
use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Connector;
use GDO\Dog\DOG_User;
use GDO\User\GDO_User;

/**
 * This connector can be called by shell: "GDO/Dog/bin/dog <command> [<..parameters..>]"
 * It is the default and a required connector.
 * You can dog add_server IRC irc://irc.freenode.net:6667 with it.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
class Bash extends DOG_Connector
{
    private $bashServer;
    
	public function init()
	{
		if (!($this->bashServer = $this->getBashServer()))
		{
			$this->bashServer = DOG_Server::table()->blank(array(
				'serv_connector' => $this->gdoShortName(),
			))->insert();
		}
	}
	
	/**
	 * @return DOG_Server
	 */
	public function getBashServer()
	{
	    if ($this->bashServer)
	    {
	        return $this->bashServer;
	    }
		$query = DOG_Server::table()->select('*');
		$query->where("serv_connector='{$this->gdoShortName()}'")->first();
		return $query->exec()->fetchObject();
	}
	
	public function getBashUser()
	{
	    $user = DOG_User::getOrCreateUser($this->getBashServer(), get_current_user());
	    GDO_User::$CURRENT = $user->getGDOUser();
	    return $user;
	}
	
    public function connect()
    {
        $this->connected = true;
        return true;
    }
    
    public function disconnect($reason)
    {
        echo "Disconnecting: {$reason}\n";
        $this->connected = false;
    }
    
    
	public function readMessage()
	{
	    return false;
	}
	
	public function dog_cmdline(...$argv)
	{
		$text = implode(' ', $argv);
		$msg = DOG_Message::make()->
			server($this->getBashServer())->
			user($this->getBashUser())->
			text($text);
		Dog::instance()->event('dog_message', $msg);
	}

	public function sendToUser(DOG_User $user, $text)
	{
	    echo "$text\n";
	}
	
	public function sendToRoom(DOG_Room $room, $text)
	{
	    echo "$text\n";
	}
   
    public function sendNoticeToUser(DOG_User $user, $text)
    {
        echo "$text\n";
    }
    
}
