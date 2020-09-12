<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_String;

final class DOG_ConfigRoom extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_String::make('confr_command')->primary()->ascii()->notNull()->size(128),
            GDT_String::make('confr_key')->ascii()->primary()->notNull()->size(32),
            GDT_Room::make('confr_room')->primary()->notNull()->cascade(),
            GDT_String::make('confr_var'),
        );
    }
    
}
