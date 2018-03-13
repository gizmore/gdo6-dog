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

final class DOG_Server extends GDO
{
	/**
	 * @var GDO_User[]
	 */
	private $users = [];
	
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('serv_id'),
        	GDT_Url::make('serv_url'),
        	GDT_Checkbox::make('serv_tls')->initial('0'),
            GDT_Connector::make('serv_connector'),
            GDT_Username::make('serv_username'),
            GDT_Secret::make('serv_password'),
        );
    }

    public function isTLS() { return $this->getValue('serv_tls'); }
    
    
    public function getConnectorName() { return $this->getVar('serv_connector'); }
    public function getConnector() { return DOG_Connector::connector($this->getConnectorName()); }
    
    /**
     * 
     * @param string $url
     * @return \GDO\Dog\DOG_Server
     */
    public static function getByURL($url) { $url = GDO::escapeS($url); return self::table()->findWhere("serv_url LIKE '%$url%'"); }
    
}
