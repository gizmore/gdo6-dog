<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_String;

final class DOG_ConfigBot extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_String::make('confb_command')->primary()->ascii()->notNull()->max(128),
            GDT_String::make('confb_key')->primary()->ascii()->notNull()->max(64),
            GDT_String::make('confb_var'),
        );
    }
    
}
