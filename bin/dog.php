<?php
use GDO\File\Filewalker;
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Connector;

require 'protected/config_dog.php';
require 'GDO6.php';

chdir(GDO_PATH);
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
$dog = new Dog();

if (defined('GWF_CONSOLE_VERBOSE'))
{
    Logger::logCron("Starting dog...\nLoading Modules...");
}

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
				Logger::logCron("Error loading $class_name");
			}
		});
    }
}, false);
    
Trans::$ISO = GWF_LANGUAGE;
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(false);
Debug::setMailOnError(GWF_ERROR_MAIL);
Database::init();

ModuleLoader::instance()->loadModules(true, true);

# All fine!
define('GWF_CORE_STABLE', 1);

$dog->init();

if ($argc === 1)
{
	$dog->mainloop();
}
else
{
	# The cmdline event is exactly used once; only here,
	array_shift($argv);
	$dog->event('dog_cmdline', ...$argv);
}


