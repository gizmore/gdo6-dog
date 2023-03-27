<?php
namespace GDO\Dog;

use GDO\Core\GDT_Select;

/**
 * Command selection GDT.
 *
 * @author gizmore
 */
final class GDT_DogCommand extends GDT_Select
{

	protected function __construct()
	{
		parent::__construct();
		$this->initChoices();
	}

	public function getChoices(): array
	{
		return DOG_Command::$COMMANDS_T;
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $value ? $value->getCLITrigger() : null;
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var ? @$this->choices[strtolower($var)] : null;
	}

}
