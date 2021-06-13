<?php
namespace GDO\Dog;

use GDO\Form\GDT_Select;

/**
 * Command selection GDT.
 * @author gizmore
 */
final class GDT_DogCommand extends GDT_Select
{
    protected function __construct()
    {
        parent::__construct();
        $this->initChoices();
    }
    
	public function initChoices()
	{
	    return $this->choices(DOG_Command::$COMMANDS_T);
	}
	
	public function toVar($value)
	{
	    return $value ? $value->getCLITrigger() : null;
	}

	public function toValue($var)
	{
	    return $var ? @$this->choices[strtolower($var)] : null;
	}
	
}
