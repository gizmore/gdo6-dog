<?php
namespace GDO\Dog;
use GDO\Form\GDO_Select;

class GDO_Connector extends GDO_Select
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
