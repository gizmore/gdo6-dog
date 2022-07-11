<?php
namespace GDO\Dog;

use GDO\Core\GDT_Object;

final class GDT_Room extends GDT_Object
{
    protected function __construct()
    {
        parent::__construct();
        $this->table = DOG_Room::table();
    }
}
