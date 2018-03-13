<?php
namespace GDO\Dog;
use GDO\Form\GDT_Select;

class GDT_Connector extends GDT_Select
{
    public function __construct()
    {
        $this->choices($this->initChoices());
        $this->encoding = self::ASCII;
        $this->caseS();
    }
    
    public function initChoices()
    {
        $choices = array();
        foreach (DOG_Connector::connectors() as $connector)
        {
            $choices[$connector->gdoShortName()] = $connector->displayName();
            
        }
        return $choices;
    }
    
}
