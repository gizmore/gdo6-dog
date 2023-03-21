<?php
namespace GDO\Dog\bin;

use GDO\Core\Debug;
use GDO\Core\GDT_Template;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Dog\Connector\Bash;
use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_User;
use GDO\Form\GDT_Form;
use GDO\Install\Config;
use GDO\Install\Installer;
use GDO\Language\Trans;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\Util\FileUtil;

@include 'protected/config_dog.php';
require 'GDO6.php';

Config::configure(); # fallback

Trans::$ISO = GDO_LANGUAGE;
Logger::init(null, GDO_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GDO_ERROR_DIE);
Debug::setMailOnError(GDO_ERROR_MAIL);
Database::init();

/** @var $argc int * */
/** @var $argv string[] * */

if (@$argv[1] === 'config')
{
	$configPath = GDO_PATH . 'protected/config_dog.php';
	if (!FileUtil::isFile($configPath))
	{
		echo "Writing config to protected/config_dog.php\n";
		$form = GDT_Form::make('form');
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
elseif ((@$argv[1] === 'modules') || (@$argv[1] === 'upgrade'))
{
	echo "Installing modules...\n";
	$modules = ModuleLoader::instance()->loadModules($argv[1] === 'upgrade', true);
	foreach ($modules as $module)
	{
		echo $module->getName() . '... ';
		Installer::installModule($module);
		echo "done\n";
	}
	echo "\nCreating bash server... done\n";
	$bash = new Bash();
	$bash->init();
}
elseif (@$argv[1] === 'admin')
{
	if (!@$argv[2])
	{
		Logger::logError('You need to specify a server. Usage: dog_install admin <server> <username>.');
	}
	elseif (!@$argv[3])
	{
		Logger::logError('You need to specify a username. Usage: dog_install admin <server> <username>.');
	}
	elseif (!($server = DOG_Server::getByArg(@$argv[2])))
	{
		Logger::logError('Unknown server');
	}
	else
	{
		GDO_User::setCurrent(GDO_User::system());
		echo "Granting all permissions to {$argv[3]}.\n";
		$dog_user = DOG_User::getOrCreateUser($server, $argv[3]);
		$gdo_user = $dog_user->getGDOUser();
		GDO_UserPermission::grant($gdo_user, 'admin');
		GDO_UserPermission::grant($gdo_user, 'staff');
		$module = ModuleLoader::instance()->loadModuleFS('Dog');
		$module->onInstall();
	}
}
elseif (@$argv[1] === 'module')
{
	if (!($moduleName = @$argv[2]))
	{
		Logger::logError('You need to specify a module name. Usage: dog_install module <name>.');
	}
	else
	{
		ModuleLoader::instance()->loadModules(true, false);
		if (!($module = ModuleLoader::instance()->getModule($moduleName)))
		{
			if (!($module = ModuleLoader::instance()->loadModuleFS($moduleName)))
			{
				Logger::logError('Module not found!');
				die(2);
			}
		}
		echo "Installing module {$moduleName}...\n";
		Installer::installModule($module);
	}
}
else
{
	echo "Usage: {$argv[0]} config|modules|upgrade|admin|module\n";
	die(1);
}

echo "Done.\n";
