<?php
namespace GDO\Dog;

use GDO\Core\GDO_Module;

/**
 * Dog chatbot.
 * @author gizmore
 */
final class Module_Dog extends GDO_Module
{
    public $module_priority = 40;
    
	public function defaultEnabled() { return false; }

	public function onInit() {}
	public function onInstall() { DOG_Install::onInstall($this); }
	public function onLoadLanguage() { return $this->loadLanguage('lang/dog'); }
	
	public function getConfig()
	{
		return array(
		);
	}

	public function getClasses()
	{
		return array(
			'GDO\\Dog\\DOG_Server',
			'GDO\\Dog\\DOG_Room',
		    'GDO\\Dog\\DOG_User',
		    'GDO\\Dog\\DOG_ConfigBot',
		    'GDO\\Dog\\DOG_ConfigRoom',
		    'GDO\\Dog\\DOG_ConfigServer',
		    'GDO\\Dog\\DOG_ConfigUser',
		);
	}
}
