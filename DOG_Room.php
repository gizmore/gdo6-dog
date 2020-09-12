<?php
namespace GDO\Dog;
use GDO\Core\GDO;
use GDO\DB\GDT_Object;
use GDO\DB\GDT_AutoInc;
use GDO\DB\GDT_String;
use GDO\Core\GDT_Secret;
use GDO\DB\GDT_Char;
use GDO\DB\GDT_Checkbox;

class DOG_Room extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('room_id'),
            GDT_Object::make('room_server')->table(DOG_Server::table()),
            GDT_String::make('room_name'),
            GDT_Secret::make('room_password'),
            GDT_Checkbox::make('room_autojoin')->initial('1'),
            GDT_Char::make('room_trigger')->size(1)->initial('.'),
        );
    }

    public function getTrigger() { return $this->getVar('room_trigger'); }
    
    /**
     * @param DOG_Server $server
     * @param string $roomName
     * @return self
     */
    public static function getByName(DOG_Server $server, $roomName)
    {
        $name = GDO::quoteS($roomName);
        return self::table()->select()->where("room_server={$server->getID()} AND room_name={$name}")->first()->exec()->fetchObject();
    }
    
    public static function getOrCreate(DOG_Server $server, $roomName)
    {
        if ($room = self::getByName($server, $roomName))
        {
            return $room;
        }
        
        return self::blank(array(
            'room_server' => $server->getID(),
            'room_name' => $roomName,
            GDT_Secret::make('room_password'),
            GDT_Checkbox::make('room_autojoin')->initial('1'),
            GDT_Char::make('room_trigger')->size(1)->initial('.'),
            
        ))->insert();
    }
    
}
