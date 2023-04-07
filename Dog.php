<?php
declare(strict_types=1);
namespace GDO\Dog;

use Error;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Event\Table;
use GDO\Core\GDO;
use GDO\Core\GDO_Hook;
use GDO\Core\GDO_Module;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Cronjob\MethodCronjob;
use GDO\Dog\Connector\Bash;
use GDO\Install\Installer;
use GDO\UI\GDT_Error;
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

	final public const MICROSLEEP = 20000;
	private static self $INSTANCE;

	public $running = true;

	/**
	 * @var DOG_Server[]
	 */
	public $servers;
	private bool $loadedPlugins = false;

	public function __construct()
	{
		self::$INSTANCE = $this;
	}

	public function loadPlugins()
	{
		if ($this->loadedPlugins)
		{
			return true;
		}

		Filewalker::traverse('GDO', null, null, function ($entry, $path)
		{
			if (preg_match('/^Dog[A-Z0-9]*$/i', $entry))
			{
//                 if (!Application::instance()->isUnitTests())
//                 {
				if (!module_enabled($entry))
				{
					return;
				}
//                 }

				Filewalker::traverse(["$path/Connector", "$path/Method"], null, function ($entry, $path)
				{
					$class_name = str_replace('/', "\\", $path);
					$class_name = substr($class_name, 0, -4);
					if (is_a($class_name, DOG_Command::class))
					{

					}
					if (class_exists($class_name))
					{
						if (is_a($class_name, '\\GDO\\Dog\\DOG_Connector', true))
						{
							DOG_Connector::register(new $class_name());
							$this->loadedPlugins = true;
						}
					}
					else
					{
						$this->loadedPlugins = false;
						Logger::logCron("Error loading $class_name");
					}
				});
			}
		}, 0);

//		if ($this->loadedPlugins)
//		{
//			$this->autoCreateCommands();
//		}

		return $this->loadedPlugins;
	}

	/**
	 * Create dog commands automatically from methods.
	 * Certain method types are skipped, as well those not shown in sitemap.
	 *
	 * @return bool
	 */
	private function autoCreateCommands()
	{
		$modules = ModuleLoader::instance()->getEnabledModules();
		foreach ($modules as $module)
		{
			$this->autoCreateCommandsForModule($module);
		}
		return true;
	}

	public static function instance(): static
	{
		if (!isset(self::$INSTANCE))
		{
			self::$INSTANCE = new self();
			self::$INSTANCE->init();
			self::$INSTANCE->loadPlugins();
		}
		return self::$INSTANCE;
	}

	private function autoCreateCommandsForModule(GDO_Module $module): void
	{
		Installer::loopMethods($module, function ($entry, $fullpath, GDO_Module $module)
		{
			$method = Installer::loopMethod($module, $fullpath);
			if (
				($method instanceof MethodCronjob) || # skip cronjobs
				($method instanceof DOG_Command) ||  # skip real dog commands
				(!$method->isCLI()) || # skip non cli
				($method->isAjax())
			) # skip ajax
			{
				return;
			}
			DOG_Command::register(new $method());
		});
	}

	public function removeServer(DOG_Server $server)
	{
		if (false !== ($index = array_search($server, $this->servers)))
		{
			unset($this->servers[$index]);
			return true;
		}
		return false;
	}

	private bool $inited = false;

	public function init()
	{
		if (!$this->inited)
		{
			Bash::instance()->init();
			$this->servers = DOG_Server::table()->all();
			DOG_User:
		}
	}

	public function mainloop()
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

	public function mainloopStep()
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

	private function mainloopServer(DOG_Server $server)
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
			catch (Error $e)
			{
				Debug::error($e);
			}
			catch (Throwable $e)
			{
				Debug::exception_handler($e);
			}
		}
	}

	public function event($name, ...$args)
	{
		$this->eventB(array_map(function (DOG_Server $s)
		{
			return $s->getConnector();
		}, $this->servers), $name, ...$args);
		$this->eventB(Method::$CLI_ALIASES, $name, ...$args);
	}

	private function eventB(array $objects, string $name, ...$args): void
	{
		foreach ($objects as $object)
		{
			if (method_exists($object, $name))
			{
//				try
//				{
					call_user_func([$object::make(), $name], ...$args);
//				}
//				catch (Throwable $ex)
//				{
////					echo GDT_Error::fromException($ex)->render();
////					@ob_flush();
//					Logger::logException($ex);
//				}
			}
		}
	}

	private function ipcTimer()
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

	private function webHookDB($message)
	{
		if (GDO_CONSOLE_VERBOSE)
		{
			echo "{$message}\n";
		}
		$message = json_decode($message, true);
		$event = $message['event'];
		$args = $message['args'];
		$param = [$event];
		if ($args)
		{
			$param = array_merge($param, $args);
		}
		return $this->webHook($param);
	}

	private function webHook(array $hookData)
	{
		$event = array_shift($hookData);
		$method_name = "hook$event";
		if (method_exists($this, $method_name))
		{
			call_user_func([$this, $method_name], ...$hookData);
		}
	}

	private function hasPendingConnections()
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

	public function hookCacheInvalidate($table, $id)
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
