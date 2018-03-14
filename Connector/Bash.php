<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Connector;

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
		return DOG_Server::table()->select('*')->where("serv_connector='{$this->gdoShortName()}'")->first()->exec()->fetchObject();
	}
	
	public function getBashUser()
	{
		
	}
	
    public function connect()
    {
        return true;
    }
    
	public function readMessage()
	{
		
	}
	
	public function dog_cmdline(...$argv)
	{
		$msg = DOG_Message::make()->server($this->getBashServer())->user($this->getBashUser())->raw(implode(' ', $argv));
		Dog::instance()->event('dog_message', $msg);
	}

    
    
}

DOG_Connector::register(new Bash());
