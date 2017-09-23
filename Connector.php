<?php
namespace GDO\Dog;
use GDO\Util\Strings;
use GDO\File\Filewalker;

abstract class Connector
{
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
    public function gdoClassName() { return self::gdoClassNameS(); }
    public static function gdoClassNameS() { return get_called_class(); }
    public function gdoShortName() { return self::gdoShortNameS(); }
    public static function gdoShortNameS() { return Strings::rsubstrFrom(get_called_class(), '\\'); }
    
    ###
    
    
    ###
    public $connected = false;
    public function connected(bool $connected)
    {
        $this->connected = $connected;
        return $this;
    }
    
    public function connnect()
    {
        
    }
}
