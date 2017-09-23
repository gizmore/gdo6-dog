<?php
use GDO\File\Filewalker;
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Dog;
use GDO\Util\Strings;

require 'protected/config.php';
require 'GDO6.php';

Logger::logDebug("Starting dog...\nLoading Connectors");
Filewalker::traverse('GDO/Dog/Module', function($entry, $path){
    if ( (false === strpos($path, 'lang')) &&
         (Strings::endsWith($entry, '.php')) )
    {
        $class_name = str_replace('/', "\\", $path);
        $class_name = substr($class_name, 0, -4);
        if (class_exists($class_name))
        {
            Logger::logDebug("Loaded $class_name");
        }
        else
        {
            Logger::logError("Error loading $class_name");
        }
    }
});
    
$dog = new Dog();
    
Trans::$ISO = GWF_LANGUAGE;
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(false);
Debug::setMailOnError(GWF_ERROR_MAIL);
Database::init();
ModuleLoader::instance()->loadModulesCache();

# All fine!
define('GWF_CORE_STABLE', 1);

$dog->mainloop();