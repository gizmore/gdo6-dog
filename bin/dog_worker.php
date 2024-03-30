<?php

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\DogTelegram\Module_DogTelegram;
use Longman\TelegramBot\Entities\Update;

if (PHP_SAPI !== 'cli')
{
    echo "This can only be run from the command line.\n";
    die(-1);
}
require __DIR__ . '/../../../protected/config.php';
require __DIR__ . '/../../../GDO7.php';

CLI::init();
Debug::init();
Logger::init('dog_worker', Logger::ALL, 'protected/logs_dog_worker');
Logger::disableBuffer();
Database::init();

final class dog_telegram extends Application
{

    public function isCLI(): bool
    {
        return true;
    }

}

$loader = ModuleLoader::instance();
$loader->loadModulesCache();

$worker = new \GDO\Dog\DogWorker();

while (true)
{
    $line = fgets(STDIN);
    fputs($worker->execute());
}
