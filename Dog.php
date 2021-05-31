<?php
namespace GDO\Dog;

use GDO\Core\Application;
use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\File\Filewalker;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO_Module;
use GDO\Install\Installer;
use GDO\Cronjob\MethodCronjob;
use GDO\Core\GDT_Error;
use GDO\Dog\Connector\Bash;

/**
 * Dog chatbot instance.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.8.0
 */
final class Dog
{
    const ADMIN = 'admin';
    const STAFF = 'staff';

    const OWNER = 'owner';
    const OPERATOR = 'operator';
    const HALFOP = 'halfop';
    const VOICE = 'voice';
    
    public $running = true;
    
    private static $INSTANCE;
    public static function instance() { return self::$INSTANCE; }
    
    public function __construct()
    {
        self::$INSTANCE = $this;
    }
    
    private $loadedPlugins;
    public function loadPlugins()
    {
        if ($this->loadedPlugins)
        {
            return;
        }
        $this->loadedPlugins = true;
        Filewalker::traverse('GDO', null, false, function($entry, $path){
            if (preg_match("/^Dog[A-Z0-9]*$/i", $entry))
            {
//                 if (!module_enabled($entry)) { return; } # else bug in tests
                Filewalker::traverse(["$path/Connector", "$path/Method"], null, function($entry, $path){
                    $class_name = str_replace('/', "\\", $path);
                    $class_name = substr($class_name, 0, -4);
                    if (class_exists($class_name))
                    {
                        if (is_a($class_name, '\\GDO\\Dog\\DOG_Command', true))
                        {
                            DOG_Command::register(new $class_name());
                            if (defined('GDO_CONSOLE_VERBOSE'))
                            {
                                Logger::logCron("Loaded command $class_name");
                            }
                        }
                        
                        if (is_a($class_name, '\\GDO\\Dog\\DOG_Connector', true))
                        {
                            DOG_Connector::register(new $class_name());
                            if (defined('GDO_CONSOLE_VERBOSE'))
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
        
        if ($this->loadedPlugins)
        {
            $this->loadedPlugins = $this->autoCreateCommands();
        }
        
        return $this->loadedPlugins;
    }
    
    /**
     * Create dog commands automatically from methods.
     * Certain method types are skipped, as well those not shown in sitemap.
     * @return boolean
     */
    private function autoCreateCommands()
    {
        if (GDO_CONSOLE_VERBOSE)
        {
            printf("Loading normal GDO methods as dog commands.\n");
        }
        $modules = ModuleLoader::instance()->getEnabledModules();
        foreach ($modules as $module)
        {
            $this->autoCreateCommandsForModule($module);
        }
        return true;
    }
    
    private function autoCreateCommandsForModule(GDO_Module $module)
    {
        Installer::loopMethods($module, function($entry, $fullpath, GDO_Module $module) {
            $method = Installer::loopMethod($module, $fullpath);
            if ( ($method instanceof MethodCronjob) || # skip cronjobs
                 ($method instanceof DOG_Command) ||  # skip real dog commands
//                  (!$method->showInSitemap()) || # skip non sitemap
                 (!$method->isCLI()) || # skip non cli
                 ($method->isAjax()) ) # skip ajax
            {
                return;
            }
            
            if (GDO_CONSOLE_VERBOSE)
            {
                printf("Loaded normal command {$method->gdoClassName()}\n");
            }

            DOG_Command::register(new DOG_CommandWrapper($method));
        });
        
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
        (new Bash())->init();
        
        $this->servers = DOG_Server::table()->all();

    	DOG_Command::sortCommands();
    	foreach (DOG_Command::$COMMANDS as $command)
    	{
    	    $command->init();
    	}
    }
    
    public function mainloop()
    {
        while ($this->running)
        {
            $this->mainloopStep();
            usleep(20);
        }
        
        while ($this->hasPendingConnections())
        {
            sleep(1);
        }
    }
    
    public function mainloopStep()
    {
        Application::updateTime();
        foreach ($this->servers as $server)
        {
            if ($server->isActive())
            {
                $this->mainloopServer($server);
            }
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
            	while ($processed < 5)
        	    {
            	    if (!$connector->readMessage())
            	    {
            	        break;
            	    }
            	    $processed++;
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
        if (defined('GDO_CONSOLE_VERBOSE'))
        {
        	Logger::logCron("Dog::event($name) " . count($args));
        }
        
        $this->eventB(array_map(function(DOG_Server $s){
            return $s->getConnector(); }, $this->servers), $name, ...$args);
        $this->eventB(DOG_Command::$COMMANDS, $name, ...$args);
    }
    
    private function eventB(array $objects, $name, ...$args)
    {
    	foreach ($objects as $object)
    	{
    		if (method_exists($object, $name))
    		{
    		    try
    		    {
        			call_user_func([$object, $name], ...$args);
    		    }
    		    catch (\Throwable $ex)
    		    {
    		        echo GDT_Error::responseException($ex)->renderCLI();
//     		        ob_flush();
    		        Logger::logException($ex);
    		    }
    		}
    	}
    }

}
