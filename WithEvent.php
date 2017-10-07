<?php
namespace GDO\Dog;
trait WithEvent
{
    public static $events = [];
    
    public function emit($event, ...$args)
    {
        foreach (self::$events as $event)
        {
            
        }
    }
    
    public function on($event, $callback)
    {
        
    }
    
}
