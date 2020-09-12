<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_String;

final class DOG_ConfigServer extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_String::make('confs_key')->ascii()->primary()->notNull()->size(32),
            GDT_Server::make('confs_server')->primary()->notNull()->cascade(),
            GDT_String::make('confs_var'),
        );
    }
    
}
