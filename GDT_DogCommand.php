<?php
namespace GDO\Dog;
use GDO\Form\GDT_Select;

final class GDT_DogCommand extends GDT_Select
{
	public function initChoices()
	{
	    $this->choices = [];
	    foreach (DOG_Command::$COMMANDS as $command)
	    {
	        if ($command->trigger)
	        {
	            $this->choices[$command->trigger] = $command;
	        }
	    }
	}
}
