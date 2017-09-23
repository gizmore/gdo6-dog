<?php
namespace GDO\Dog;
use GDO\Core\GDO_Module;
use GDO\Dog\Connector;

final class Module_Dog extends GDO_Module
{
    public function onInit()
    {
//         Connector::init();
    }
    
    public function getClasses()
    {
        return array(
            'GDO\\Dog\\DOG_Server',
            'GDO\\Dog\\DOG_Room',
        );
    }
}
