<?php
namespace GDO\Dog\Method;

use GDO\Core\GDT_String;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_DogCommand;

final class ConfigBot extends DOG_Command
{

	public $trigger = 'confb';

	public function getPermission(): ?string { return Dog::OWNER; }

	public function gdoParameters(): array
	{
		return [
			GDT_DogCommand::make('command')->notNull(),
			GDT_String::make('key'),
			GDT_String::make('var'),
		];
	}

	public function dogExecute(DOG_Message $message, DOG_Command $command, $key, $var)
	{
		if ($key === null)
		{
			return $this->showConfigKeys($message, $command);
		}

		elseif ($var === null)
		{
			return $this->showConfigVar($message, $command, $key);
		}

		else
		{
			return $this->setConfigVar($message, $command, $key, $var);
		}
	}

	private function showConfigKeys(DOG_Message $message, DOG_Command $command)
	{
		$keys = [];
		foreach ($command->getConfigBot() as $gdt)
		{
			$keys[] = $gdt->name;
		}
		return $message->rply('msg_dog_config_keys', [$command->trigger, implode(', ', $keys)]);
	}

	private function showConfigVar(DOG_Message $message, DOG_Command $command, $key)
	{
		$var = $command->getConfigVarBot($key);
		if (!($command->getConfigGDTBot($key)))
		{
			return $message->rply('err_dog_var_unknown', [$command->trigger, $key]);
		}
		return $message->rply('msg_dog_config_key', [$command->trigger, $var]);
	}

	private function setConfigVar(DOG_Message $message, DOG_Command $command, $key, $var)
	{
		$old = $command->getConfigVarBot($key);
		if (!($gdt = $command->getConfigGDTBot($key)))
		{
			return $message->rply('err_dog_var_unknown', [$command->trigger, $key]);
		}

		$value = $gdt->getValue();
		if (!$gdt->validate($value))
		{
			return $message->rply('err_dog_config_invalid', [$key, $command->trigger, $var]);
		}

		$command->setConfigValueBot($key, $value);

		return $message->rply('msg_dog_config_set', [$key, $command->trigger, $old, $var]);
	}

}
