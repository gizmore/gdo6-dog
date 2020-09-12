<?php
namespace GDO\Dog\Method;

use GDO\Core\GDT_Hook;
use GDO\DB\Cache;
use GDO\Dog\DOG_Command;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Message;

final class ClearCache extends DOG_Command
{
    public $trigger = 'clear cache';
    
    public function getPermission() { return Dog::OPERATOR; }
    
    public function dogExecute(DOG_Message $message)
    {
        # Flush memcached and gdo cache.
        Cache::flush();
        # Call hook
        GDT_Hook::callWithIPC('ClearCache');
        
        $message->rply('msg_cache_cleared');
    }

}
