<?php
namespace GDO\Dog\bin;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Connector\Bash;
use GDO\Dog\Dog;
use GDO\Dog\DogApp;
use GDO\Language\Trans;
use GDO\UI\GDT_Page;
use Ratchet\App;

# Bootstrap
require 'GDO7.php';
require 'protected/config.php';
chdir(GDO_PATH);
Trans::$ISO = GDO_LANGUAGE;
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(false);
Debug::setMailOnError(GDO_ERROR_MAIL);
Database::init();
CLI::setServerVars();
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

# Args
/** @var int $argc * */
/** @var string[] $argv * */
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
