<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_String;

final class DOG_ConfigServer extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_String::make('confs_command')->primary()->ascii()->notNull()->max(128)->index(),
            GDT_String::make('confs_key')->ascii()->primary()->notNull()->max(64)->index(),
            GDT_Server::make('confs_server')->primary()->notNull()->index(),
            GDT_String::make('confs_var'),
        );
    }
    
}
