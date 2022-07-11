<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_String;

final class DOG_ConfigServer extends GDO
{
    public function gdoColumns() : array
    {
        return array(
            GDT_String::make('confs_command')->primary()->ascii()->caseS()->notNull()->max(128),
            GDT_String::make('confs_key')->ascii()->caseS()->primary()->notNull()->max(64),
            GDT_Server::make('confs_server')->primary()->notNull(),
            GDT_String::make('confs_var'),
        );
    }
    
}
