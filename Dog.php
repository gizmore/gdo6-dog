<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Event\Table;
use GDO\Core\GDO;
use GDO\Core\GDO_Hook;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Dog\Connector\Bash;
use GDO\User\GDO_Permission;
use GDO\Util\Filewalker;
use Throwable;

/**
 * Dog chatbot instance.
 *
 * @version 7.0.3
 * @since 6.8.0
 * @author gizmore
 */
final class Dog
{

	final public const ADMIN = GDO_Permission::ADMIN;
	final public const STAFF = GDO_Permission::STAFF;

	final public const OPERATOR = 'operator';
	final public const HALFOP = 'halfop';
	final public const VOICE = 'voice';

	final public const MICROSLEEP = 10000;
	private static self $INSTANCE;

	public bool $running = true;

	/**
	 * @var DOG_Server[]
	 */
	public array $servers;
	private bool $loadedPlugins = false;

	public function loadPlugins(): bool
	{
		if ($this->loadedPlugins)
		{
			return true;
		}

		Filewalker::traverse('GDO', null, null, function ($entry, $path)
		{
			if (preg_match('/^Dog[_A-Z0-9]*$/iD', $entry))
			{
				if (!module_enabled($entry))
				{
					return;
				}
				Filewalker::traverse(["$path/Connector", "$path/Method"], null, function ($entry, $path)
				{
					$class_name = str_replace('/', "\\", $path);
					$class_name = substr($class_name, 0, -4);
					if (class_exists($class_name))
					{
						if (is_a($class_name, DOG_Connector::class , true))
						{
							DOG_Connector::register(new $class_name());
							$this->loadedPlugins = true;
						}
					}
					else
					{
						Logger::logCron("Error loading $class_name");
						$this->loadedPlugins = false;
					}
				});
			}
		}, 0);

		return $this->loadedPlugins;
	}

	public static function instance(): self
	{
		if (!isset(self::$INSTANCE))
		{
			self::$INSTANCE = new self();
			self::$INSTANCE->init();
			self::$INSTANCE->loadPlugins();
		}
		return self::$INSTANCE;
	}

	public function removeServer(DOG_Server $server): bool
	{
		if (false !== ($index = array_search($server, $this->servers, true)))
		{
			unset($this->servers[$index]);
			return true;
		}
		return false;
	}

	private bool $inited = false;

	public function init(): void
	{
		if (!$this->inited)
		{
			Bash::instance()->init();
			$this->servers = DOG_Server::table()->all();
		}
	}

	public function mainloop(): void
	{
		$lastIPC = Application::$TIME;
		while ($this->running)
		{
			$this->mainloopStep();
			if ((Application::$TIME - $lastIPC) >= 10)
			{
				$lastIPC = Application::$TIME;
				$this->ipcTimer();
			}
			usleep(self::MICROSLEEP);
		}

		while ($this->hasPendingConnections())
		{
			sleep(1);
		}
	}

	public function mainloopStep(): void
	{
		Application::updateTime();
		Table::dispatch('tick');
		foreach ($this->servers as $server)
		{
			if ($server->isActive())
			{
				$this->mainloopServer($server);
			}
		}
	}

	private function mainloopServer(DOG_Server $server): void
	{
		$connector = $server->getConnector();

		if (!$connector->connected)
		{
			# Connect
			if ($server->shouldConnect())
			{
				if ($connector->connect())
				{
					Dog::instance()->event('dog_server_connected', $server);
					$server->resetConnectionAttempt();
				}
				else
				{
					# Try again
					$server->nextAttempt();
				}
			}
			# Give up
			elseif ($server->shouldGiveUp())
			{
				$server->setVar('serv_active', '0');
				Dog::instance()->event('dog_server_failed', $server);
			}
		}

		else # process a few messages
		{
			$processed = 0;
			try
			{
				while ($processed < 5)
				{
					if (!$connector->readMessage())
					{
						break;
					}
					$processed++;
				}
			}
			catch (Throwable $ex)
			{
				echo Debug::backtraceException($ex);
			}
		}
	}

	public function event(string $name, mixed ...$args): bool
	{
		$this->eventB(array_map(function (DOG_Server $s)
		{
			return $s->getConnector();
		}, $this->servers), $name, ...$args);
		return $this->eventB(Method::$CLI_ALIASES, $name, ...$args);
	}

	private function eventB(array $objects, string $name, mixed ...$args): bool
	{
		foreach ($objects as $object)
		{
			if (method_exists($object, $name))
			{
				call_user_func([$object::make(), $name], ...$args);
			}
		}
		return true;
	}

	private function ipcTimer(): void
	{
		if ($messages = GDO_Hook::table()->select()->exec()->fetchAllRows())
		{
			foreach ($messages as $message)
			{
				$this->webHookDB($message[1]);
				GDO_Hook::table()->deleteWhere('hook_id=' . $message[0], false);
			}
		}
	}

	###########
	### IPC ###
	###########

	private function webHookDB(string $message): void
	{
		$message = json_decode($message, true);
		$event = $message['event'];
		$args = $message['args'];
		$param = [$event];
		if ($args)
		{
			$param = array_merge($param, $args);
		}
		$this->webHook($param);
	}

	private function webHook(array $hookData): void
	{
		$event = array_shift($hookData);
		$method_name = "hook{$event}";
		if (method_exists($this, $method_name))
		{
			call_user_func([$this, $method_name], ...$hookData);
		}
	}

	private function hasPendingConnections(): bool
	{
		foreach ($this->servers as $server)
		{
			if ($server->isConnected())
			{
				return true;
			}
		}
		return false;
	}

	public function hookCacheInvalidate($table, $id): void
	{
		$table = GDO::tableFor($table);
		$table->reload($id);
	}

//     private function tempReset(GDO $gdo)
//     {
//         if ($gdo instanceof GDO_User)
//         {
// //             $sessid = $gdo->tempGet('sess_id');
//             $gdo->tempReset();
// //             $gdo->tempSet('sess_id', $sessid);
//         }
//         else
//         {
//             $gdo->tempReset();
//         }
//     }

}
