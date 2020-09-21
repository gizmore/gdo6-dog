<?php
namespace GDO\Dog;
use GDO\Core\Application;
use GDO\Core\Logger;

final class Dog extends Application
{
    const OWNER = 'owner';
    const OPERATOR = 'operator';
    const HALFOP = 'halfop';
    const VOICE = 'voice';
    
    public function isCLI() { return true; }
    public function isHTML() { return false; }
    public function getFormat() { return 'cli'; }
    
    public $running = true;
    
    /**
     * @var DOG_Server[]
     */
    public $servers;
    
    public function init()
    {
    	foreach (DOG_Connector::connectors() as $connector)
    	{
    		$connector->init();
    	}
    	DOG_Command::sortCommands();
    	foreach (DOG_Command::$COMMANDS as $command)
    	{
    	    $command->init();
    	}
    }
    
    public function mainloop()
    {
        if (defined('GWF_CONSOLE_VERBOSE'))
        {
            Logger::logCron("Entering mainloop.");
        }
        $this->servers = DOG_Server::table()->all();
        while ($this->running)
        {
            Application::updateTime();
            foreach ($this->servers as $server)
            {
                if ($server->isActive())
                {
                    $this->mainloopServer($server);
                }
            }
            usleep(1000);
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
        	while ($connector->readMessage())
        	{
        	}
        }
        
    }
    
    public function event($name, ...$args)
    {
        if (defined('GWF_CONSOLE_VERBOSE'))
        {
        	Logger::logCron("Dog::event($name)");
        }
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
