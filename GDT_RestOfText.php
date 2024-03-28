<?php
namespace GDO\Dog;

use GDO\Core\GDT_String;
use GDO\UI\GDT_Repeat;


final class GDT_RestOfText extends GDT_Repeat
{

    protected function __construct()
    {
        parent::__construct();
    }

    public static function make(string $name = null): static
    {
        return self::makeAs($name, GDT_String::make());
    }

    public function toValue(array|string|null $var): null|bool|int|float|string|object|array
    {
        return $var === null ? null : implode(',', $var);
    }

    public function getValue(): mixed
    {
        $var = parent::getValue();
        return $var === null ? null : implode(',', $var);
    }

}
