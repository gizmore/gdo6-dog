<?php
use GDO\Core\Module;
use GDO\Dog\Connector;

final class Module_Dog extends Module
{
    public function onInit()
    {
        Connector::init();
    }
    
    public function getClasses()
    {
        return array(
            'GDO\\Dog\\DOG_Server',
            'GDO\\Dog\\DOG_Room',
        );
    }
}
