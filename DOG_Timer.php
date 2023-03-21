<?php
namespace GDO\Dog;

final class DOG_Timer
{

	public static $TIMERS = [];
	public $repeat = false;

	public function __construct() {}

	public static function addTimer(DOG_Timer $timer)
	{
		self::$TIMERS[] = $timer;
	}

	public function repeat($repeat = true)
	{
		$this->repeat = $repeat;
		return $this;
	}

	public function in($in) {}

}
