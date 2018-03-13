<?php
namespace GDO\Dog;
use GDO\Core\Method;

class DOG_Command extends Method
{
	public $priority = 50;
	
	/**
	 * @var DOG_Command[]
	 */
	public static $COMMANDS = [];
	public static function register(DOG_Command $command) { self::$COMMANDS[count(self::$COMMANDS)] = $command; }
	
	/**
	 * @param string $trigger
	 * @return DOG_Command
	 */
	public static function byTrigger($trigger)
	{
		foreach (self::$COMMANDS as $command)
		{
			if ($trigger === $command->getTrigger())
			{
				return $command;
			}
		}
	}
	
	public function execute()
	{
	}
	
	public function onDogExecute(DOG_Message $message)
	{
		$args = [];
		foreach ($this->gdoParameters() as $gdt)
		{
			$args[] = $gdt->getParameterValue();
		}
		$this->dogExecute($message, ...$args);
	}
	
}
