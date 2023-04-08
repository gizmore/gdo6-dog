<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\GDT_Field;
use GDO\Core\GDT_String;
use GDO\UI\GDT_Repeat;

/**
 * This is like a string but allows spaces / rest of message for the chatbot.
 *
 * @version 7.0.3
 * @author gizmore
 */
final class GDT_DogString extends GDT_Repeat
{

	public static function make(string $name = null): static
	{
		return parent::makeAs($name, GDT_String::make());
	}

	public function toVar(float|object|int|bool|array|string|null $value): ?string
	{
		return $value === null ? null : implode(',', $value);
	}


}
