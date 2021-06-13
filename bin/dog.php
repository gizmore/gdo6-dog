<?php
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Dog;
use GDO\Dog\DogApp;
use GDO\UI\GDT_Page;
use GDO\Util\CLI;

require 'protected/config_dog.php';
require 'GDO6.php';
$dog = new DogApp();
$dog = new Dog();
chdir(GDO_PATH);
Logger::init(null, GDO_ERROR_LEVEL); # 1st init as guest

if (defined('GDO_CONSOLE_VERBOSE'))
{
    Logger::logCron("Starting dog...\nLoading Modules...");
}

Trans::$ISO = GDO_LANGUAGE;
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(false);
Debug::setMailOnError(GDO_ERROR_MAIL);
Database::init();
CLI::setServerVars();
GDT_Page::make();

$modules = (new ModuleLoader(GDO_PATH.'GDO/'))->loadModules(true);

if (GDO_CONSOLE_VERBOSE)
{
    printf("Loaded %s modules.\n", count($modules));
}

$dog->loadPlugins();

# All fine!
define('GDO_CORE_STABLE', 1);

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


