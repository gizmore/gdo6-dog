<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Connector;
use GDO\Dog\DOG_User;

class Bash extends DOG_Connector
{
	/**
	 * @var resource
	 */
	private $stdin;
	
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
//     	if (!$this->stdin)
//     	{
//     		$this->stdin = fopen('php://stdin', 'r');
//     	}
    }
    
    public function disconnect()
    {
//     	if ($this->stdin)
//     	{
//     		fclose($this->stdin);
//     		$this->stdin = null;
//     	}
    }
    
	public function readMessage()
	{
// 		$read = array($this->stdin);
// 		$write = NULL;
// 		$exept = NULL;
// 		if (stream_select($read, $write, $exept, 0) > 0){
// 			//something happened on our monitors. let's see what it is
// 			foreach ($read as $input => $fd){
// 				$line = fgets($fd);
// 				return $line;
// 			}
// 		}
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
		echo $message;
	}

	
    
    
}

DOG_Connector::register(new Bash());
