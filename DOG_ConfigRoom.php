<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_String;

final class DOG_ConfigRoom extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_String::make('confr_command')->primary()->ascii()->notNull()->max(128)->index(),
            GDT_String::make('confr_key')->ascii()->primary()->notNull()->max(64)->index(),
            GDT_Room::make('confr_room')->primary()->notNull()->index(),
            GDT_String::make('confr_var'),
        );
    }
    
}
