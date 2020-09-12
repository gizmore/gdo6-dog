<?php
namespace GDO\Dog\Method;

use GDO\Core\GDT_Hook;
use GDO\DB\Cache;
use GDO\Dog\DOG_Command;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Message;
use GDO\DB\GDT_String;

final class ClearCache extends DOG_Command
{
    public $trigger = 'cache';
    
    public function getPermission() { return Dog::OPERATOR; }
    
    public function gdoParameters()
    {
        return array(
            GDT_String::make('arg'),
        );
    }
    
    public function dogExecute(DOG_Message $message, $arg)
    {
        if ($arg === 'clear')
        {
            # Flush memcached and gdo cache.
            Cache::flush();
            # Call hook
            GDT_Hook::callWithIPC('ClearCache');
            
            $message->rply('msg_cache_cleared');
        }
        
        else
        {
            $message->rply('msg_cache_stats');
        }
        
    }

}
