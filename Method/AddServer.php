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
use GDO\Dog\Dog;

final class AddServer extends DOG_Command
{
    public $group = 'Config';
    public $trigger = 'add_server';
	
	public function gdoParameters()
	{
		return array(
			GDT_Connector::make('connector')->notNull(),
			GDT_Url::make('url'),
		    GDT_Username::make('user'),
		    GDT_Password::make('password'),
		);
	}
	
	public function dogExecute(DOG_Message $message, DOG_Connector $connector, URL $url=null, $username, $password)
	{
		if (!($server = DOG_Server::getByURL($url->raw)))
		{
		    $data = array(
				'serv_url' => $url->raw,
			    'serv_connector' => $connector->getName(),
		    );
		    
		    if ($username)
		    {
		        $data['serv_username'] = $username;
		    }

		    if ($password)
		    {
		        $data['serv_password'] = $password;
		    }
		    
			$server = DOG_Server::blank($data);
			
			$connector->setupServer($server);
			
			$server->insert();
			
			Dog::instance()->servers[] = $server;
			
			$message->rply('server_added', [$server->getID(), $url->raw]);
		}
	}
	
}
