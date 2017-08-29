<?php
use GDO\DB\GDO;
use GDO\DB\GDT_AutoInc;
use GDO\Dog\GDT_Connector;

final class DOG_Server extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_AutoInc::make('serv_id'),
            GDT_Connector::make('serv_connector'),
        );
    }

    
}
