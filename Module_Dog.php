<?php
namespace GDO\Dog;

use GDO\Core\GDO_Module;

/**
 * Port of Lamb3...
 * but not even started yet.
 *
 * @author gizmore
 */
final class Module_Dog extends GDO_Module
{
	public function defaultEnabled() { return false; }

	public function onInit() {}
	public function onInstall() {}
	
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
			'GDO\\Dog\\DOG_User'
		);
	}
}
