<?php
namespace GDO\Dog;

use GDO\Core\Application;
use GDO\Date\GDT_Duration;

/**
 * Add bruteforce protection to a dog command.
 *
 * @version 6.10.4
 * @since 6.10.4
 * @author gizmore
 */
trait WithBruteforceProtection
{

	private $attempts = [];

	public function getConfigBot()
	{
		return [
			GDT_Duration::make('timeout')->initial('10'),
		];
	}

	protected function isBruteforcing(DOG_Message $message)
	{
		$dog_user = $message->user;
		$time = Application::$MICROTIME;
		$last = isset($this->attempts[$dog_user->getID()]) ?
			$this->attempts[$dog_user->getID()] : 0;
		$wait = $time - $last;
		$minwait = $this->getTimeout();
		if ($wait < $minwait)
		{
			$wait = round($minwait - $wait, 1);
			$message->rply('err_please_wait', [$wait]);
			return true;
		}
		$this->attempts[$dog_user->getID()] = $time;
		return false;
	}

	public function getTimeout()
	{
		return $this->getConfigValueBot('timeout');
	}

}
