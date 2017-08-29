<?php
use GDO\DB\GDO;
use GDO\DB\GDO_AutoInc;
use GDO\Dog\GDO_Connector;

final class DOG_Server extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDO_AutoInc::make('serv_id'),
            GDO_Connector::make('serv_connector'),
        );
    }

    
}
