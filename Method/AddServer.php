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

/**
 * Add a generic new server.
 * Useful via command line or connectors that are not like IRC.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class AddServer extends DOG_Command
{
    public $trigger = 'add_server';
    
    public function getPermission() { return Dog::OPERATOR; }
	
	public function gdoParameters()
	{
		return array(
			GDT_Connector::make('connector')->notNull(),
			GDT_Url::make('url')->allSchemes()->allowLocal()->allowExternal(),
		    GDT_Username::make('user'),
		    GDT_Password::make('password'),
		);
	}
	
	public function dogExecute(DOG_Message $message, DOG_Connector $connector, URL $url=null, $username, $password)
	{
	    if ($url)
	    {
	        $server = DOG_Server::getByURL($url->getTLD());
	    }
	    else
	    {
	        $server = DOG_Server::getBy('serv_connector', $connector->getName());
	    }
	    
	    if ($server)
	    {
	        return $message->rply('err_dog_server_already_added', [$server->displayName()]);
	    }

	    # Add
	    $data = array(
		    'serv_connector' => $connector->getName(),
	    );

	    if ($url)
	    {
	        $data['serv_url'] = $url->raw;
	    }

	    if ($username)
	    {
	        $data['serv_username'] = $username;
	    }
	    else
	    {
	        $data['serv_username'] = $this->getDefaultNickname();
	    }

	    if ($password)
	    {
	        $data['serv_password'] = $password;
	    }
	    
		$server = DOG_Server::blank($data);
		
		$connector->setupServer($server);
		
		$server->insert();
		
		Dog::instance()->servers[] = $server;
		
		$message->rply('msg_dog_server_added', [$server->getID(), $server->displayName()]);
	}
	
}
