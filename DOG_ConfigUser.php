<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\DB\GDT_String;

final class DOG_ConfigUser extends GDO
{
    public function gdoColumns()
    {
        return array(
            GDT_String::make('confu_command')->primary()->ascii()->notNull()->max(128)->index(),
            GDT_String::make('confu_key')->ascii()->primary()->notNull()->max(64)->index(),
            GDT_DogUser::make('confu_user')->primary()->notNull()->cascade(),
            GDT_String::make('confu_var'),
        );
    }
    
}
