<?php
namespace GDO\Dog\Method;

use GDO\Core\GDT_String;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_DogCommand;

final class ConfigUser extends DOG_Command
{

	public $trigger = 'confu';

	public function isUserRequired(): bool { return true; }

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
		foreach ($command->getConfigUser() as $gdt)
		{
			$keys[] = $gdt->name;
		}
		return $message->rply('msg_dog_config_keys', [$command->trigger, implode(', ', $keys)]);
	}

	private function showConfigVar(DOG_Message $message, DOG_Command $command, $key)
	{
		$var = $command->getConfigVarUser($message->user, $key);
		if (!($command->getConfigGDTUser($key)))
		{
			return $message->rply('err_dog_var_unknown', [$command->trigger, $key]);
		}
		return $message->rply('msg_dog_config_key', [$command->trigger, $var]);
	}

	private function setConfigVar(DOG_Message $message, DOG_Command $command, $key, $var)
	{
		$old = $command->getConfigVarUser($message->user, $key);
		if (!($gdt = $command->getConfigGDTUser($key)))
		{
			return $message->rply('err_dog_var_unknown', [$command->trigger, $key]);
		}

		$value = $gdt->getValue();
		if (!$gdt->validate($value))
		{
			return $message->rply('err_dog_config_invalid', [$key, $command->trigger, $var]);
		}

		$command->setConfigValueUser($message->user, $key, $value);

		return $message->rply('msg_dog_config_set', [$key, $command->trigger, $old, $var]);
	}

}
