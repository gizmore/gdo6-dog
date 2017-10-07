<?php
namespace GDO\Dog;
use GDO\Core\WithName;

abstract class Connector
{
    use WithName;

    /**
     * @var Connector[]
     */
    private static $connectors = [];
    
    /**
     * @return \GDO\Dog\Connector[]
     */
    public static function connectors() { return self::$connectors; }
    public static function register(Connector $connector)
    {
        self::$connectors[$connector->gdoShortName()] = $connector;
    }

    public function displayName() { return t('connector_' . $this->gdoShortName()); }
    
    ###
    
    
    ###
    public $connected = false;
    public function connected($connected)
    {
        $this->connected = $connected;
        return $this;
    }
    
    public function connnect()
    {
        
    }
}
