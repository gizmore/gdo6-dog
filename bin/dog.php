<?php
namespace GDO\Dog\bin;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Connector\Bash;
use GDO\Dog\Dog;
use GDO\Language\Trans;
use GDO\UI\GDT_Page;

define('GDO_TIME_START', microtime(true));

# Bootstrap
require 'protected/config.php';
require 'GDO7.php';
chdir(GDO_PATH);
Trans::$ISO = GDO_LANGUAGE;
Logger::init(null, Logger::ALL, 'protected/logs/__dog');
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(false);
Debug::setMailOnError(GDO_ERROR_MAIL);
Database::init();
$app = Application::init();
$app->cli()->modeDetected(GDT::RENDER_CLI);
GDT_Page::make();

# Modules
$loader = ModuleLoader::instance();
$loader->loadModulesCache();
Trans::inited();
define('GDO_CORE_STABLE', 1);

# Dog
$dog = Dog::instance();
$dog->init();
$dog->loadPlugins();

# User
Bash::instance()->getBashUser();
CLI::setServerVars();

# Args
/** @var int $argc * */
/** @var string[] $argv * */
if ($argc === 1)
{
//    if (Worker::isChild())
//    {
//        Worker::run();
//    }
//    else
//    {
        $dog->mainloop();
//    }
}
else
{
	# The cmdline event is exactly used once; only here,
	array_shift($argv);
	$dog->event('dog_cmdline', ...$argv);
}
