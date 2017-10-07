<?php
namespace GDO\Dog;
use GDO\Core\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\Dog\GDT_Connector;
use GDO\User\GDT_Username;
use GDO\User\GDT_Password;
use GDO\Core\GDT_Secret;

final class DOG_Server extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('serv_id'),
            GDT_Connector::make('serv_connector'),
            GDT_Username::make('serv_username'),
            GDT_Secret::make('serv_password'),
        );
    }
    
    public function getConnectorName() { return $this->getVar('serv_connector'); }
    public function getConnector() { return Connector::connector($this->getConnectorName()); }
    
    
}
