<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_Object;
use GDO\Util\Arrays;
use GDO\Util\Strings;

/**
 * GDT_DogUser parameter is like an object but features a few options:
 *
 * online - only online users are allowed
 * same_room - user has to be in the same room
 * same_server - user has to be on the same server
 * exact - username might not be abbreviated (deprecated)
 * thyself - user might choose thyself
 *
 * @TODO deleted - also select deleted users
 * @TODO registrered - only select registered users
 * @TODO authenticated - only select registered and authenticated users
 *
 * @version 7.0.3
 * @since 6.10.0
 * @author gizmore
 */
final class GDT_DogUser extends GDT_Object
{

	public bool $online = false;

	###############
	### Options ###
	###############
	public bool $sameRoom = false;
	public bool $sameServer = false;
	public bool $exact = false;
	public bool $thyself = true;
	private bool $ambigious = false;

	protected function __construct()
	{
		parent::__construct();
		$this->table = DOG_User::table();
	}

	public function online(bool $online = true): static
	{
		$this->online = $online;
		return $this;
	}

	public function sameRoom(bool $sameRoom = true): static
	{
		$this->sameRoom = $sameRoom;
		return $this;
	}

	public function sameServer(bool $sameServer = true): static
	{
		$this->sameServer = $sameServer;
		return $this;
	}

	public function exact(bool $exact = true): static
	{
		$this->exact = $exact;
		return $this;
	}

	######################
	### Ambigious hack ###
	######################

	public function thyself(bool $thyself = true): static
	{
		$this->thyself = $thyself;
		return $this;
	}

	##################
	### GDT_Object ###
	##################

//	/**
//	 * Always use the findByName method.
//	 */
//	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
//	{
//		$_REQUEST['nocompletion_' . $this->name] = 1;
//		return parent::toValue($var);
//	}

	/**
	 * Validate object first, then options.
	 */
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (!parent::validate($value))
		{
			return false;
		}

		if (!$value)
		{
			return true; # null
		}

		/**
		 * @var DOG_User $user
		 */
		$user = $value;

		if ($this->online)
		{
			if (!($user->isOnline()))
			{
				return $this->error('err_not_online', [$user->displayFullName()]);
			}
		}

		if ($this->sameServer)
		{
			if ($user->getServerID() != DOG_Message::$LAST_MESSAGE->server->getID())
			{
				return $this->error('err_not_same_server', [$user->displayFullName()]);
			}
		}

		if ($this->sameRoom)
		{
			if (!($room = DOG_Message::$LAST_MESSAGE->room))
			{
				return $this->error('err_not_same_room', [$user->displayFullName()]);
			}
			if (!$room->hasUser($user))
			{
				return $this->error('err_not_same_room', [$user->displayFullName()]);
			}
		}

		if ($this->exact)
		{
			$input = $this->getVar();
			$input = Strings::substrTo($input, '{', $input);
			if ($input !== $user->getName())
			{
				return $this->error('err_exact_username', [$user->displayFullName()]);
			}
		}

		if ($this->ambigious !== false)
		{
			return $this->error('err_username_ambigous', [Arrays::implodeHuman($this->ambigious)]);
		}

		if (!$this->thyself)
		{
			if ($user === DOG_Message::$LAST_MESSAGE->user)
			{
				return $this->error('err_user_thyself');
			}
		}

		return true;
	}

	public function findByName($name)
	{
		$this->ambigious = false;

		$server = DOG_Message::$LAST_MESSAGE->server;

		if (!$this->sameServer)
		{
			$matches = null;
			if (preg_match('/\\{(\\d+)\\}$/iuD', $name, $matches))
			{
				$server = DOG_Server::findById($matches[1]);
			}
		}

		$_name = Strings::substrTo($name, '{', $name);
		$ename = GDO::escapeSearchS($_name);

		$query = $this->table->select()->
		where("doguser_name LIKE '%$ename%' ")->
		where('doguser_service = 0')->
		limit(10);

		if ($this->sameServer)
		{
			$query->where("doguser_server={$server->getID()}");
		}

		/**
		 * @var DOG_User[] $users
		 */
		$users = $query->exec()->fetchAllObjects();

		# Evaluate result set by size
		switch (count($users))
		{
			case 0:
				return null;

			case 1:
				return $users[0];

			default:
				foreach ($users as $user)
				{
					if (!$this->sameServer)
					{
						if ($user->getFullName() === $name)
						{
							return $user; # exact match
						}
					}
					else
					{
						if ($user->getName() === $name)
						{
							return $user;
						}
					}
				}

				# Try beginning of name
				$possible = [];
				foreach ($users as $user)
				{
					if (stripos($user->displayFullName(), $name) === 0)
					{
						$possible[] = $user;
					}
				}
				if (count($possible) === 1)
				{
					return $possible[0];
				}

				# Multiple matches!
				# Return a dummy user but mark as ambigous for validation.
				$this->ambigious = array_map(function ($user)
				{
					return $user->displayFullName();
				}, $users);
				return $users[0];
		}
	}

}
