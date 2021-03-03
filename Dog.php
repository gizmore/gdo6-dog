<?php
namespace GDO\Dog;

use GDO\Core\Application;
use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\File\Filewalker;

/**
 * Dog chatbot instance.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.08
 */
final class Dog extends Application
{
    const ADMIN = 'admin';
    const STAFF = 'staff';

    const OWNER = 'owner';
    const OPERATOR = 'operator';
    const HALFOP = 'halfop';
    const VOICE = 'voice';
    
    public function isCLI() { return true; }
    public function isHTML() { return false; }
    public function getFormat() { return 'cli'; }
    
    public $running = true;
    
    public static $INSTANCE;
    
    /**
     * @return self
     */
    public static function instance() { return self::$INSTANCE; }
    
    public function __construct()
    {
        self::$INSTANCE = $this;
    }
    
    private $loadedPlugins;
    public function loadPlugins()
    {
        $this->loadedPlugins = true;
        Filewalker::traverse('GDO', null, false, function($entry, $path){
            if (preg_match("/^Dog[A-Z]?/", $entry))
            {
                Filewalker::traverse(["$path/Connector", "$path/Method"], null, function($entry, $path){
                    $class_name = str_replace('/', "\\", $path);
                    $class_name = substr($class_name, 0, -4);
                    if (class_exists($class_name))
                    {
                        if (is_a($class_name, '\\GDO\\Dog\\DOG_Command', true))
                        {
                            DOG_Command::register(new $class_name());
                            if (defined('GWF_CONSOLE_VERBOSE'))
                            {
                                Logger::logCron("Loaded command $class_name");
                            }
                        }
                        
                        if (is_a($class_name, '\\GDO\\Dog\\DOG_Connector', true))
                        {
                            DOG_Connector::register(new $class_name());
                            if (defined('GWF_CONSOLE_VERBOSE'))
                            {
                                Logger::logCron("Loaded connector $class_name");
                            }
                        }
                        
                    }
                    else
                    {
                        $this->loadedPlugins = false;
                        Logger::logCron("Error loading $class_name");
                    }
                });
            }
        }, false);
        return $this->loadedPlugins;
    }
    
    /**
     * @var DOG_Server[]
     */
    public $servers;
    
    public function removeServer(DOG_Server $server)
    {
        if (false !== ($index = array_search($server, $this->servers)))
        {
            unset($this->servers[$index]);
            return true;
        }
        return false;
    }
    
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
            usleep(20);
        }
        
        while ($this->hasPendingConnections())
        {
            sleep(1);
        }
    }
    
    private function hasPendingConnections()
    {
        foreach ($this->servers as $server)
        {
            if ($server->isConnected())
            {
                return true;
            }
        }
        return false;
    }
    
    private function mainloopServer(DOG_Server $server)
    {
    	$connector = $server->getConnector();
    	
        if (!$connector->connected)
        {
            # Connect
            if ($server->shouldConnect())
            {
            	if ($connector->connect())
            	{
            	    Dog::instance()->event('dog_server_connected', $server);
            	    $server->resetConnectionAttempt();
            	}
            	else
            	{
            	    # Try again
            	    $server->nextAttempt();
            	}
            }
            # Give up
            elseif ($server->shouldGiveUp())
            {
                $server->setVar('serv_active', '0');
                Dog::instance()->event('dog_server_failed', $server);
            }
        }
        
        else # process a few messages
        {
            $processed = 0;
       	    try
        	{
            	while ($processed++ < 5)
        	    {
            	    if (!$connector->readMessage())
            	    {
            	        break;
            	    }
        	    }
        	}
    	    catch (\Error $e)
    	    {
    	        Debug::error($e);
    	    }
    	    catch (\Throwable $e)
    	    {
    	        Debug::exception_handler($e);
    	    }
        }
    }
    
    public function event($name, ...$args)
    {
        if (defined('GWF_CONSOLE_VERBOSE'))
        {
        	Logger::logCron("Dog::event($name)");
        }
    	
    	foreach ($this->servers as $server)
    	{
    	    $connector =  $server->getConnector();
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
