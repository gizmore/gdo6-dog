<?php
namespace GDO\Dog;

use GDO\DB\GDT_Object;

final class GDT_Room extends GDT_Object
{
    public function __construct()
    {
        $this->table = DOG_Room::table();
    }
}
