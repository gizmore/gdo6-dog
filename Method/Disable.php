<?php
declare(strict_types=1);
namespace GDO\Dog\Method;

use GDO\Core\GDT_Checkbox;
use GDO\Core\Method;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_DogCommand;

/**
 * Disable a method for servers and channels.
 *
 * @version 7.0.3
 */
final class Disable extends DOG_Command
{

//	public $trigger = 'disable';
//
//	/**
//	 * @return self
//	 */
//	public static function instance()
//	{
//		return DOG_Command::byTrigger('disable');
//	}

	public function getPermission(): ?string { return Dog::OPERATOR; }

	public function gdoParameters(): array
	{
		return [
			GDT_DogCommand::make('command')->notNull(),
		];
	}

	protected function getConfigRoom(): array
	{
		$conf = [];
		foreach (Method::$CLI_ALIASES as $trigger => $klass)
		{
			$name = 'disable_' . $trigger;
			$conf[] = GDT_Checkbox::make($name)->notNull()->initial('0');
		}
		return $conf;
	}

	protected function getConfigServer(): array
	{
		$conf = [];
		foreach (Method::$CLI_ALIASES as $trigger => $klass)
		{
			$name = 'disable_' . $trigger;
			$conf[] = GDT_Checkbox::make($name)->notNull()->initial('0');
		}
		return $conf;
	}

	public function isDisabled(DOG_Message $message, DOG_Command $command): bool
	{
		if ($this->isDisabledServer($message, $command))
		{
			return true;
		}
		if (isset($message->room))
		{
			return $this->isDisabledRoom($message, $command);
		}
		return false;
	}

	public function isDisabledRoom(DOG_Message $message, DOG_Command $command): bool
	{
		$key = 'disable_' . $command->getCLITrigger();
		return $this->getConfigValueRoom($message->room, $key);
	}

	public function isDisabledServer(DOG_Message $message, DOG_Command $command): bool
	{
		$key = 'disable_' . $command->getCLITrigger();
		return $this->getConfigValueServer($message->server, $key);
	}

	public function dogExecute(DOG_Message $message, DOG_Command $command): bool
	{
		if ($command === $this)
		{
			return $message->rply('err_cannot_disable');
		}

		if ($message->room)
		{
			if ($this->isDisabledRoom($message, $command))
			{
				return $message->rply('msg_dog_already_disabled', [$command->getCLITrigger()]);
			}
			$key = 'disable_' . $command->getCLITrigger();
			$this->setConfigValueRoom($message->room, $key, true);
		}
		else
		{
			if ($this->isDisabledServer($message, $command))
			{
				return $message->rply('msg_dog_already_disabled', [$command->getCLITrigger()]);
			}
			$key = 'disable_' . $command->getCLITrigger();
			$this->setConfigValueServer($message->server, $key, true);
		}
		return $message->rply('msg_dog_disabled', [$command->getCLITrigger()]);
	}

}
