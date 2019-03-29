<?php
use GDO\File\Filewalker;
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Dog;
use GDO\Util\Strings;
use GDO\Dog\DOG_Connector;
use GDO\Core\Application;
require 'protected/config.php';
require 'GDO6.php';

$dog = new Dog();

Logger::logDebug("Starting dog...\nLoading Modules...\n");
Filewalker::traverse('GDO', null, false, function($entry, $path){
	if (Strings::startsWith($entry, 'Dog'))
	{
		Filewalker::traverse(["$path/Method", "$path/Connector"], null, function($entry, $path){
			$class_name = str_replace('/', "\\", $path);
			$class_name = substr($class_name, 0, -4);
			if (class_exists($class_name))
			{
				Logger::logCron("Loaded $class_name");
			}
			else
			{
				Logger::logCron("Error loading $class_name");
			}
		});
    }
}, false);
    
Trans::$ISO = GWF_LANGUAGE;
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
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


