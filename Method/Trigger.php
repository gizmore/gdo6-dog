<?php
namespace GDO\Dog\Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;
use GDO\DB\GDT_Char;

/**
 * Change the trigger char for a room.
 * @author gizmore
 *
 */
final class Trigger extends DOG_Command
{
    public $group = 'Config';
    public $trigger = 'trigger';
    
    public function getPermission() { return Dog::HALFOP; }
    
    public function isPrivateMethod() { return false; }
    
    public function gdoParameters()
    {
        return array(
            GDT_Char::make('trigger')->size(1)->notNull(),
        );
    }
    
    public function dogExecute(DOG_Message $message, $trigger)
    {
        $room = $message->room;
        $old = $room->getTrigger();
        $room->saveVar('room_trigger', $trigger);
        $message->rply('msg_dog_trigger_changed', [$room->getName(), $old, $trigger]);
    }
    
}
