<?php
namespace GDO\Dog;
use GDO\Core\Application;
use GDO\Core\Logger;

final class Dog extends Application
{
    public function isCLI() { return true; }
    public function getFormat() { return 'cli'; }
    
    public function init()
    {
    	foreach (DOG_Connector::connectors() as $connector)
    	{
    		$connector->init();
    	}
    }
    
    public function mainloop()
    {
        $servers = DOG_Server::table();
        foreach ($servers->all() as $server)
        {
            $this->mainloopServer($server);
            usleep(100);
        }
    }
    
    private function mainloopServer(DOG_Server $server)
    {
    	$connector = $server->getConnector();
        if (!$connector->connected)
        {
        	$connector->connect();
        }
        else
        {
        	while ($msg = $connector->readMessage())
        	{
        		$this->processMessage($msg);
        	}
        }
        
    }
    
    private function processMessage(DOG_Message $msg)
    {
    	
    }
    
    public function event($name, ...$args)
    {
    	Logger::logCron("Dog::event($name)");
    	foreach (DOG_Connector::connectors() as $connector)
    	{
    		if (method_exists($connector, $name))
    		{
    			call_user_func([$connector, $name], ...$args);
    		}
    	}
    	foreach (DOG_Command::$COMMANDS as $command)
    	{
    		if (method_exists($command, $name))
    		{
    			call_user_func([$command, $name], ...$args);
    		}
    	}
    }
    
    

}
