<?php
namespace GDO\Dog;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Name;

/**
 * Dog chatbot.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.8.0
 */
final class Module_Dog extends GDO_Module
{
    public int $priority = 40;
    
	public function defaultEnabled() : bool { return true; }

	public function onInstall() : void { DOG_Install::onInstall($this); }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/dog'); }
	public function getDependencies() : array
	{
		return ['Cronjob'];
	}
	
	public function getConfig() : array
	{
		return [
		    GDT_Name::make('default_nickname')->notNull()->initial('Dog'),
		];
	}
	public function cfgDefaultNickname() { return $this->getConfigVar('default_nickname'); }

	public function getClasses() : array
	{
		return [
		    DOG_Server::class,
		    DOG_Room::class,
		    DOG_User::class,
		    DOG_ConfigBot::class,
		    DOG_ConfigRoom::class,
		    DOG_ConfigServer::class,
		    DOG_ConfigUser::class,
		];
	}
	
}
