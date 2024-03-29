<?php
namespace GDO\Dog;

use GDO\Core\GDT_Object;

/**
 * A chat server GDT.
 *
 * @author gizmore
 */
final class GDT_Server extends GDT_Object
{

	protected function __construct()
	{
		parent::__construct();
		$this->table = DOG_Server::table();
	}

}
