<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;

/**
 * Make $end appear in command list.
 */
final class End extends DOG_Command
{

    public function getCLITrigger(): string
    {
        return 'end';
    }

}
