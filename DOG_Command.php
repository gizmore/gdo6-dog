<?php
namespace GDO\Dog;
use GDO\Core\Method;

abstract class DOG_Command extends Method
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
	
	public function getTrigger()
	{
		
	}
	
	public function execute()
	{
	}
	
	public function onDogExecute(DOG_Message $message)
	{
		$args = [];
		$_REQUEST = [];
		$i = 1;
		$matches = null;
		preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $message->text, $matches);
		foreach ($this->gdoParameters() as $gdt)
		{
			$_REQUEST[$gdt->name] = $matches[0][$i++];
			$value = $gdt->getParameterValue();
			if (!$gdt->validate($value))
			{
				$message->reply(t('err_param'));
				return;
			}
			$args[] = $value;
		}
		$this->dogExecute($message, ...$args);
	}
	
}
