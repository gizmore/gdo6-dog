<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Float;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Token;
use GDO\Dog\Connector\Bash;

/**
 * Dog chatbot.
 *
 * @version 7.0.3
 * @since 6.8.0
 * @author gizmore
 */
final class Module_Dog extends GDO_Module
{

	public int $priority = 40;

	public function onInstall(): void { DOG_Install::onInstall($this); }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/dog'); }

	public function getDependencies(): array
	{
		return [
			'CLI',
			'Cronjob',
			'Net',
		];
	}

	public function getClasses(): array
	{
		return [
			DOG_Server::class,
			DOG_Room::class,
			DOG_User::class,
            DOG_RoomUser::class,
			DOG_ConfigBot::class,
			DOG_ConfigRoom::class,
			DOG_ConfigServer::class,
			DOG_ConfigUser::class,
		];
	}


	public function getConfig(): array
	{
		return [
			GDT_Name::make('default_nickname')->notNull()->initial('Dog'),
            GDT_Float::make('dog_idle_sleep')->notNull()->min(0.01)->max(2.0)->initial('0.2'),
		];
	}

    public function cfgIdleSleep(): float
    {
        return $this->getConfigValue('dog_idle_sleep');
    }

    public function cfgIdleSleepMicros(): int
    {
        return (int)round($this->cfgIdleSleep()*1000000);
    }

	public function cfgDefaultNickname(): string
	{
		return $this->getConfigVar('default_nickname');
	}

    public function getUserConfig(): array
    {
        return [
            GDT_Token::make('dog_token'),
        ];
    }

    public function onModuleInit(): void
    {
        DOG_Connector::register(Bash::instance());
    }

}
