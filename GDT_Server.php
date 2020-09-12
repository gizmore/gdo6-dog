<?php
namespace GDO\Dog;

use GDO\DB\GDT_Object;

final class GDT_Server extends GDT_Object
{
    public function __construct()
    {
        $this->table = DOG_Server::table();
    }
}
