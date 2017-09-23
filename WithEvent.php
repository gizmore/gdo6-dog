<?php
namespace GDO\Dog;
trait WithEvent
{
    public static $events = [];
    
    public function emit(string $event, ...$args)
    {
        foreach (self::$events as $event)
        {
            
        }
    }
    
    public function on(string $event, $callback)
    {
        
    }
    
}
