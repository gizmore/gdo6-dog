<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\Dog;

final class Quit extends DOG_Command
{
    public function getPermission() { return Dog::OWNER; }
    
}
