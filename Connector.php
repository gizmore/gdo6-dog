<?php
namespace GDO\Dog;
use GDO\Util\Strings;
use GDO\File\Filewalker;

abstract class Connector
{
    private static $connectors = [];
    public static function register(Connector $connector)
    {
        self::$connectors[$connector->gdoShortName()] = $connector;
    }
    public static function init()
    {
        Filewalker::traverse(GWF_PATH.'GDO/Dog/Connector', false, function($entry, $path){
            include $path;
        });
    }

    public function gdoClassName() { return self::gdoClassNameS(); }
    public static function gdoClassNameS() { return get_called_class(); }
    public function gdoShortName() { return self::gdoShortNameS(); }
    public static function gdoShortNameS() { return Strings::rsubstrFrom(get_called_class(), '\\'); }
    
    
}
