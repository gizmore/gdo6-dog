<?php
namespace GDO\Dog;

final class DOG_Timer
{
    public static $TIMERS = [];
    
    public static function addTimer(DOG_Timer $timer)
    {
        self::$TIMERS[] = $timer;
    }
    
    public $repeat = false;
    public function repeat($repeat=true)
    {
        $this->repeat = $repeat;
        return $this;
    }
    
    public function in($in)
    {
        
    }
    
    public function __construct()
    {
        
    }
    
}
