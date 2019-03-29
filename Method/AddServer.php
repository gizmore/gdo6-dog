<?php
namespace GDO\Dog\Method;
use GDO\Net\GDT_Url;
use GDO\Net\GDT_Port;
use GDO\Dog\DOG_Command;
use GDO\DB\GDT_Checkbox;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Server;
use GDO\Dog\GDT_Connector;
use GDO\Dog\DOG_Connector;
use GDO\Net\URL;

final class AddServer extends DOG_Command
{
	public function getTrigger() { return 'add_server'; }
	
	public function gdoParameters()
	{
		return array(
			GDT_Connector::make('connector')->notNull(),
			GDT_Url::make('url')->notNull(),
		);
	}
	
	public function dogExecute(DOG_Message $message, DOG_Connector $connector, URL $url)
	{
		var_dump($connector);
		if (!($server = DOG_Server::getByURL($url->raw)))
		{
			$server = DOG_Server::blank(array(
				
			));
		}
	}
	
}

DOG_Command::register(new AddServer());
