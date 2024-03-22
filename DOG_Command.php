<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\User\GDO_User;

/**
 * A dog command adds Dog config to a method.
 * The permission is additionally checked for channel/private/connector scopes.
 *
 * Implement gdoParameters() and then success is called with:
 * 		$this->dogExecute($message, ...$args);
 *
 * @version 7.0.3
 * @author gizmore
 */
abstract class DOG_Command extends MethodForm
{

	/**
	 * @var GDT[]
	 */
	private ?array $ccBot = null;

	/**
	 * @var GDT[]
	 */
	private ?array $ccUser = null;

	/**
	 * @var GDT[]
	 */
	private ?array $ccRoom = null;

	/**
	 * @var GDT[]
	 */
	private ?array $ccServer = null;


	#############
	### Flags ###
	#############

    public function isCLI(): bool { return true; }

    public function isTrivial(): bool { return false; }

	public function getDefaultNickname(): string
	{
		return Module_Dog::instance()->cfgDefaultNickname();
	}

	public function getConfigVarBot(string $key): string|array|null
	{
		if ($var = DOG_ConfigBot::getById($this->gdoClassName(), $key))
		{
			return $var->gdoVar('confb_var');
		}
		return $this->getConfigGDTBot($key)->getVar();
	}

	public function getConfigGDTBot(string $key): ?GDT
	{
		$conf = $this->getConfigBotCached();
		return $conf[$key] ?: null;
	}


	##############
	### Helper ###
	##############

	/**
	 * @return GDT[]
	 */
	private function getConfigBotCached(): array
	{
		if ($this->ccBot === null)
		{
			$this->ccBot = [];
			foreach ($this->getConfigBot() as $gdt)
			{
				$this->ccBot[$gdt->getName()] = $gdt;
			}
		}
		return $this->ccBot;
	}

	/**
	 * @return GDT[]
	 */
	public function getConfigBot(): array { return GDT::EMPTY_ARRAY; }

	public function getConfigValueBot(string $key): float|object|int|bool|array|string|null
	{
		$gdt = $this->getConfigGDTBot($key);
		return $gdt->getValue();
	}

	public function setConfigVarBot(string $key, ?string $var): float|object|int|bool|array|string|null
	{
		$gdt = $this->getConfigGDTBot($key)->var($var);
		$value = $gdt->toVar($gdt->inputToVar($var));
		return $this->setConfigValueBot($key, $value);
	}


	##################
	### Config Bot ###
	##################

	public function setConfigValueBot(string $key, float|object|int|bool|array|string|null $value): bool
	{
		$gdt = $this->getConfigGDTBot($key);
		if (!$gdt->validate($value))
		{
			return false;
		}
		DOG_ConfigBot::blank([
			'confb_command' => $this->gdoClassName(),
			'confb_key' => $key,
			'confb_var' => $gdt->toVar($value),
		])->softReplace();
		return true;
	}

	public function getConfigVarUser(DOG_User $user, string $key): string|array|null
	{
		if ($var = DOG_ConfigUser::getById($this->gdoClassName(), $key, $user->getID()))
		{
			return $var->gdoVar('confu_var');
		}
		return $this->getConfigGDTUser($key)->getVar();
	}

	public function getID(): ?string { return $this->getCLITrigger(); }

	public function getCLITrigger(): string
	{
		$g = $this->getCLITriggerGroup();
		$t = strtolower($this->getMethodName());
		return "{$g}.{$t}";
	}

	public function getCLITriggerGroup(): string
	{
		$m = $this->getModule();
		return strtolower($m->getModuleName());
	}

	public function getConfigGDTUser(string $key): ?GDT
	{
		$conf = $this->getConfigUserCached();
		return $conf[$key] ?: null;
	}

	/**
	 * @return GDT[]
	 */
	private function getConfigUserCached(): array
	{
		if ($this->ccUser === null)
		{
			$this->ccUser = [];
			foreach ($this->getConfigUser() as $gdt)
			{
				$this->ccUser[$gdt->getName()] = $gdt;
			}
		}
		return $this->ccUser;
	}



	###################
	### Config User ###
	###################

	/**
	 * @return GDT[]
	 */
	public function getConfigUser(): array { return GDT::EMPTY_ARRAY; }

	protected function createForm(GDT_Form $form): void
	{
		$form->addFields(...$this->gdoParameters());
		$form->addField(GDT_AntiCSRF::make());
		$form->actions()->addField(GDT_Submit::make());
	}

	public function hasPermission(GDO_User $user, string &$error, array &$args): bool
	{
		$message = DOG_Message::$LAST_MESSAGE;
        if (!$message)
        {
            return true;
        }
		if (!$this->connectorMatches($message))
		{
			$error = 'err_dog_connector_match';
			$args = [$message->server->getConnectorName()];
			return false;
		}
		if ($message->room)
		{
			if (!$this->isRoomMethod())
			{
				$error = 'err_dog_cmd_not_room';
				return false;
			}
		}
		elseif (!$this->isPrivateMethod())
		{
			$error = 'err_dog_cmd_only_room';
			return false;
		}
		return true;
	}

	private function connectorMatches(DOG_Message $message): bool
	{
		return in_array($message->server->getConnectorName(), $this->getConnectors(), true);
	}

	/**
	 * Get all supported connectors for this command.
	 *
	 * @return string[]
	 */
	protected function getConnectors(): array
	{
		return array_keys(DOG_Connector::connectors());
	}

	protected function isRoomMethod(): bool { return true; }

	protected function isPrivateMethod(): bool { return true; }


	###################
	### Config Room ###
	###################

	public function formValidated(GDT_Form $form): GDT
	{
		$args = [];
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$args[] = $gdt->getValue();
		}

		$message = DOG_Message::$LAST_MESSAGE;

		if ($this->isDebugging())
		{
			xdebug_break();
		}

        if ($result = $this->dogExecute($message, ...$args))
        {
            return $result;
        }

		return GDT_Response::make();
	}

	public function getConfigValueUser(DOG_User $user, string $key): float|object|int|bool|array|string|null
	{
		$gdt = $this->getConfigGDTUser($key);
		return $gdt->getValue();
	}

	public function setConfigVarUser(DOG_User $user, string $key, ?string $var): bool
	{
		$gdt = $this->getConfigGDTUser($key);
		$value = $gdt->toValue($gdt->inputToVar($var));
		return $this->setConfigValueUser($user, $key, $value);
	}

	public function setConfigValueUser(DOG_User $user, string $key, mixed $value): bool
	{
		$gdt = $this->getConfigGDTUser($key);
		if (!$gdt->validate($value))
		{
			return false;
		}
		DOG_ConfigUser::blank([
			'confu_command' => $this->gdoClassName(),
			'confu_key' => $key,
			'confu_user' => $user->getID(),
			'confu_var' => $gdt->toVar($value),
		])->softReplace();
		return true;
	}

	public function getConfigValueRoom(DOG_Room $room, string $key): float|object|array|bool|int|string|null
	{
		$gdt = $this->getConfigGDTRoom($key);
		$var = $this->getConfigVarRoom($room, $key);
		return $gdt->toValue($var);
	}

	public function getConfigGDTRoom(string $key): GDT
	{
		$conf = $this->getConfigRoomCached();
		return $conf[$key];
	}

	/**
	 * @return GDT[]
	 */
	private function getConfigRoomCached(): array
	{
		if ($this->ccRoom === null)
		{
			$this->ccRoom = [];
			foreach ($this->getConfigRoom() as $gdt)
			{
				$this->ccRoom[$gdt->getName()] = $gdt;
			}
		}
		return $this->ccRoom;
	}


	#####################
	### Config Server ###
	#####################

	/**
	 * @return GDT[]
	 */
	protected function getConfigRoom(): array { return GDT::EMPTY_ARRAY; }

	public function getConfigVarRoom(DOG_Room $room, string $key): string|array|null
	{
		if ($var = DOG_ConfigRoom::getById($this->gdoClassName(), $key, $room->getID()))
		{
			return $var->gdoVar('confr_var');
		}
		return $this->getConfigGDTRoom($key)->getVar();
	}

	public function setConfigVarRoom(DOG_Room $room, string $key, ?string $var): bool
	{
		$gdt = $this->getConfigGDTRoom($key);
		$value = $gdt->toValue($gdt->inputToVar($var));
		return $this->setConfigValueRoom($room, $key, $value);
	}

	public function setConfigValueRoom(DOG_Room $room, string $key, mixed $value): bool
	{
		$gdt = $this->getConfigGDTRoom($key);
		if (!$gdt->validate($value))
		{
			return false;
		}
		DOG_ConfigRoom::blank([
			'confr_command' => $this->gdoClassName(),
			'confr_key' => $key,
			'confr_room' => $room->getID(),
			'confr_var' => $gdt->toVar($value),
		])->softReplace();
		return true;
	}

	public function getConfigValueServer(DOG_Server $server, string $key): float|object|array|bool|int|string|null
	{
		$gdt = $this->getConfigGDTServer($key);
		$var = $this->getConfigVarServer($server, $key);
		return $gdt->toValue($var);
	}

	public function getConfigGDTServer(string $key): GDT
	{
		$conf = $this->getConfigServerCached();
		return $conf[$key];
	}

	/**
	 * @return GDT[]
	 */
	private function getConfigServerCached(): array
	{
		if ($this->ccServer === null)
		{
			$this->ccServer = [];
			foreach ($this->getConfigServer() as $gdt)
			{
				$this->ccServer[$gdt->getName()] = $gdt;
			}
		}
		return $this->ccServer;
	}


	#############
	### Perms ###
	#############

	/**
	 * @return GDT[]
	 */
	protected function getConfigServer(): array { return GDT::EMPTY_ARRAY; }


	##################
	### Repository ###
	##################

	public function getConfigVarServer(DOG_Server $server, string $key): string|array|null
	{
		if ($var = DOG_ConfigServer::getById($this->gdoClassName(), $key, $server->getID()))
		{
			return $var->gdoVar('confs_var');
		}
		return $this->getConfigGDTServer($key)->getVar();
	}

	public function setConfigVarServer(DOG_Server $server, string $key, ?string $var): bool
	{
		$gdt = $this->getConfigGDTServer($key);
		$value = $gdt->toValue($gdt->inputToVar($var));
		return $this->setConfigValueServer($server, $key, $value);
	}

	public function setConfigValueServer(DOG_Server $server, string $key, mixed $value): bool
	{
		$gdt = $this->getConfigGDTServer($key);
		if (!$gdt->validate($value))
		{
			return false;
		}
		DOG_ConfigServer::blank([
			'confs_command' => $this->gdoClassName(),
			'confs_key' => $key,
			'confs_server' => $server->getID(),
			'confs_var' => $gdt->toVar($value),
		])->softReplace();
		return true;
	}

}
