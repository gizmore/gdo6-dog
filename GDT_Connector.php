<?php
namespace GDO\Dog;
use GDO\Form\GDT_Select;

class GDT_Connector extends GDT_Select
{
    public function __construct()
    {
        $this->choices($this->initChoices());
    }
    
    public function initChoices()
    {
        $choices = array();
        return $choices;
    }
    
}
