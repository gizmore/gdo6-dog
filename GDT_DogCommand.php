<?php
namespace GDO\Dog;
use GDO\DB\GDT_ObjectSelect;

final class GDT_DogCommand extends GDT_ObjectSelect
{
	public function initChoices()
	{
		$this->choices = DOG_Command::$COMMANDS;
	}
}
