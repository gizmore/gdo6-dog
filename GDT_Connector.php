<?php
namespace GDO\Dog;

use GDO\Core\GDT_Select;
use GDO\Util\Arrays;

class GDT_Connector extends GDT_Select
{
    protected function __construct()
    {
        parent::__construct();
        $this->initChoices();
        $this->encoding = self::ASCII;
        $this->caseS();
    }
    
    public function toValue(string $var=null)
    {
    	return @DOG_Connector::connector($var);
    }
    
    public function getChoices()
    {
        $choices = [];
        foreach (DOG_Connector::connectors() as $name => $class)
        {
            $choices[$name] = $class;
        }
        return $choices;
    }
    
    protected function errorInvalidChoice()
    {
    	return $this->error('err_connector', [
    		html($this->getVar()),
    		html(Arrays::implodeHuman(array_keys($this->choices)))]);
    }

}
