<?php
namespace GDO\Dog;

use GDO\Form\GDT_Select;
use GDO\Util\Arrays;

class GDT_Connector extends GDT_Select
{
    protected function __construct()
    {
        parent::__construct();
        $this->choices($this->initChoices());
        $this->encoding = self::ASCII;
        $this->caseS();
    }
    
    public function toValue($var)
    {
    	return @DOG_Connector::connector($var);
    }
    
    public function initChoices()
    {
        $choices = array();
        foreach (DOG_Connector::connectors() as $name => $class)
        {
            $choices[$name] = $class;
        }
        return $choices;
    }
    
    protected function errorInvalidChoice()
    {
    	return $this->error(t('err_connector', [html($this->getVar()), html(Arrays::implodeHuman(array_keys($this->choices)))]));
    }

}
