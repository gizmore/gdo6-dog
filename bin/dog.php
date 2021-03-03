<?php
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Dog;

require 'protected/config_dog.php';
require 'GDO6.php';

chdir(GDO_PATH);
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
$dog = new Dog();

if (defined('GWF_CONSOLE_VERBOSE'))
{
    Logger::logCron("Starting dog...\nLoading Modules...");
}

$dog->loadPlugins();
    
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

/** @var $argc int **/
/** @var $argv string[] **/

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


