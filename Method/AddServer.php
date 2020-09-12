<?php
namespace GDO\Dog\Method;
use GDO\Net\GDT_Url;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Server;
use GDO\Dog\GDT_Connector;
use GDO\Dog\DOG_Connector;
use GDO\Net\URL;
use GDO\User\GDT_Username;
use GDO\User\GDT_Password;

final class AddServer extends DOG_Command
{
    public function getGroup() { return 'Config'; }
	public function getTrigger() { return 'add_server'; }
	
	public function gdoParameters()
	{
		return array(
			GDT_Connector::make('connector')->notNull(),
			GDT_Url::make('url')->notNull(),
		    GDT_Username::make('user'),
		    GDT_Password::make('password'),
		);
	}
	
	public function dogExecute(DOG_Message $message, DOG_Connector $connector, URL $url, $username, $password)
	{
		if (!($server = DOG_Server::getByURL($url->raw)))
		{
			$server = DOG_Server::blank(array(
				'serv_url' => $url->raw,
			    'serv_connector' => $connector->getName(),
			    'serv_username' => $username,
			    'serv_password' => $password,
			));
			
			$connector->setupServer($server);
			
			$server->insert();
			
			$message->rply('server_added', [$server->getID(), $url->raw]);
		}
	}
	
}
