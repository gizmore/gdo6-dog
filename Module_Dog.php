<?php
namespace GDO\Dog;

use GDO\Core\GDO_Module;
use GDO\DB\GDT_Name;

/**
 * Dog chatbot.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.8.0
 */
final class Module_Dog extends GDO_Module
{
    public $module_priority = 40;
    
	public function defaultEnabled() { return true; }

	public function onInstall() { DOG_Install::onInstall($this); }
	public function onLoadLanguage() { return $this->loadLanguage('lang/dog'); }
	
	public function getConfig()
	{
		return [
		    GDT_Name::make('default_nickname')->initial('Dog'),
		];
	}
	public function cfgDefaultNickname() { return $this->getConfigVar('default_nickname'); }

	public function getClasses()
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
