<?php
use GDO\Core\Debug;
use GDO\Core\GDT_Template;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\File\FileUtil;
use GDO\Install\Installer;
use GDO\Form\GDT_Form;
use GDO\Install\Config;
use GDO\Language\Trans;
use GDO\DB\Database;
use GDO\Dog\Dog;
use GDO\Dog\Module_Dog;
use GDO\Dog\DOG_User;
use GDO\Dog\DOG_Server;
use GDO\User\GDO_UserPermission;

@include 'protected/config_dog.php';
require 'GDO6.php';

Config::configure(); # fallback

$dog = new Dog();
Trans::$ISO = GWF_LANGUAGE;
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GWF_ERROR_DIE);
Debug::setMailOnError(GWF_ERROR_MAIL);
Database::init();

if (@$argv[1] === 'config')
{
    $configPath = GDO_PATH . 'protected/config_dog.php';
    if (!FileUtil::isFile($configPath))
    {
        echo "Writing config to protected/config_dog.php\n";
        $form = GDT_Form::make();
        foreach (Config::fields() as $gdt)
        {
            $form->addField($gdt);
        }
        $content = GDT_Template::php('Install', 'config.php', ['form' => $form]);
        FileUtil::createDir(dirname($configPath));
        file_put_contents($configPath, $content);
    }
    echo "You can now edit protected/config_dog.php manually.\n";
    
}
elseif (@$argv[1] === 'modules')
{
    echo "Installing modules...\n";
    ModuleLoader::instance()->loadModules(false, true);
    $modules = ModuleLoader::instance()->getInstallableModules();
    foreach ($modules as $module)
    {
        echo $module->getName() . ' ... ';
        Installer::installModule($module);
        echo "done\n";
    }
}
elseif (@$argv[1] === 'admin')
{
    if (!@$argv[2])
    {
        Logger::logError("You need to specify a server. Usage: dog_install admin <server> <username>.");
    }
    elseif (!@$argv[3])
    {
        Logger::logError("You need to specify a username. Usage: dog_install admin <server> <username>.");
    }
    elseif (!($server = DOG_Server::getByArg(@$argv[2])))
    {
        Logger::logError("Unknown server");
    }
    else
    {
        echo "Granting all permissions to {$argv[3]}.\n";
        $dog_user = DOG_User::getOrCreateUser($server, $argv[3]);
        $gdo_user = $dog_user->getGDOUser();
        GDO_UserPermission::grant($gdo_user, 'admin');
        GDO_UserPermission::grant($gdo_user, 'staff');
        $module = ModuleLoader::instance()->loadModuleFS('Dog');
        $module->onInstall();
    }
}
else
{
    echo "Usage: {$argv[0]} config|modules|admin\n";
    die(1);
}

echo "Done.\n";
