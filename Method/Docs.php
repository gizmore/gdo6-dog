<?php
namespace GDO\Dog\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\Method;

final class Docs extends Method
{

    public function isCLI(): bool
    {
        return true;
    }

    public function getCLITrigger(): string
    {
        return 'docs';
    }

    public function getTree(): array
    {
        return t('dog_docs');
    }

    public function gdoParameters(): array
    {
        return [
            GDT_String::make('topic'),
        ];
    }

    public function execute(): GDT
    {
    }

}
