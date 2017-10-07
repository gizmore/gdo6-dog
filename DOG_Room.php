<?php
namespace GDO\Dog;
use GDO\Core\GDO;
use GDO\DB\GDT_Object;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_String;
use GDO\Core\GDT_Secret;

class DOG_Room extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('room_id'),
            GDT_Object::make('room_server')->table(DOG_Server::table()),
            GDT_String::make('room_name'),
            GDT_Secret::make('room_password'),
        );
    }

    
}
