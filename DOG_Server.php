<?php
namespace GDO\Dog;
use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\Dog\GDT_Connector;
use GDO\User\GDT_Username;
use GDO\User\GDT_Password;
use GDO\Core\GDT_Secret;
use GDO\Net\GDT_Url;
use GDO\DB\GDT_Checkbox;
use GDO\User\GDO_User;
use GDO\DB\GDT_Char;
use GDO\Date\GDT_Duration;
use GDO\Net\URL;

final class DOG_Server extends GDO
{
	/**
	 * @var DOG_Connector
	 */
	private $connector;
	
	/**
	 * @var GDO_User[]
	 */
	private $users = [];
	
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('serv_id'),
        	GDT_Url::make('serv_url'),
        	GDT_Char::make('serv_trigger')->utf8()->max(1),
//         	GDT_Checkbox::make('serv_tls')->initial('0'),
            GDT_Connector::make('serv_connector'),
            GDT_Username::make('serv_username'),
            GDT_Secret::make('serv_password'),
        	GDT_Duration::make('serv_connect_timeout')->initial('30'),
        );
    }

    public function isTLS() { return $this->getValue('serv_tls'); }
    /**
     * @return URL
     */
    public function getURL() { return $this->getValue('serv_url'); }
    public function getConnectURL() { return ($url = $this->getURL()) ? $url->getHost() . ':' . $url->getPort() : null; }
    public function getConnectTimeout() { return $this->getValue('serv_connect_timeout'); }
    
    
    public function getConnectorName() { return $this->getVar('serv_connector'); }
    /**
     * @return \GDO\Dog\DOG_Connector
     */
    public function getConnector()
    {
    	if (!$this->connector)
    	{
	    	$connector = DOG_Connector::connector($this->getConnectorName());
	    	$classname = get_class($connector);
	    	$this->connector = new $classname();
	    	$this->connector->server($this);
    	}
    	return $this->connector;
	}
    
    /**
     * 
     * @param string $url
     * @return \GDO\Dog\DOG_Server
     */
    public static function getByURL($url) { $url = GDO::escapeS($url); return self::table()->findWhere("serv_url LIKE '%$url%'"); }
    
}
