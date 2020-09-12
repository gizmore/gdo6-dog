<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Connector;
use GDO\Dog\DOG_User;

/**
 * This connector can be called by "dog <..parameters..>".
 * @author gizmore
 */
class Bash extends DOG_Connector
{
	public function init()
	{
		if (!($this->getBashServer()))
		{
			DOG_Server::table()->blank(array(
				'serv_connector' => $this->gdoShortName(),
			))->insert();
		}
	}
	
	/**
	 * @return DOG_Server
	 */
	public function getBashServer()
	{
		$query = DOG_Server::table()->select('*');
		$query->where("serv_connector='{$this->gdoShortName()}'")->first();
		return $query->exec()->fetchObject();
	}
	
	public function getBashUser()
	{
		return DOG_User::getOrCreateUser($this->getBashServer(), get_current_user());
	}
	
    public function connect()
    {
    }
    
    public function disconnect($reason)
    {
        echo "Disconnecting: {$reason}\n";
    }
    
    
	public function readMessage()
	{
	}
	
	public function dog_cmdline(...$argv)
	{
		$text = implode(' ', $argv);
		$msg = DOG_Message::make()->
			server($this->getBashServer())->
			user($this->getBashUser())->
			raw($text)->
			text($text);
		Dog::instance()->event('dog_message', $msg);
	}

	public function sendTo($receiver, $message)
	{
		echo "$message\n";
	}

	
    
    
}
