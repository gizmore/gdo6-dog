<?php
declare(strict_types=1);
namespace GDO\Dog\Method;

use GDO\Crypto\GDT_Password;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Connector;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Server;
use GDO\Dog\GDT_Connector;
use GDO\Net\GDT_Url;
use GDO\Net\URL;
use GDO\User\GDT_Username;

/**
 * Add a generic new server.
 * Useful via command line or connectors that are not like IRC.
 *
 * @version 7.0.3
 * @since 6.10.0
 * @author gizmore
 */
final class AddServer extends DOG_Command
{

	public function getPermission(): ?string { return Dog::OPERATOR; }

	public function gdoParameters(): array
	{
		return [
			GDT_Connector::make('connector')->notNull(),
			GDT_Url::make('url')->allSchemes()->allowInternal()->allowExternal()->positional()->reachable(false),
			GDT_Username::make('user'),
			GDT_Password::make('password'),
		];
	}

	public function dogExecute(DOG_Message $message, DOG_Connector $connector, URL $url = null, string $username = null, string $password = null)
	{
		if ($url)
		{
			$server = DOG_Server::getByURL($url->getTLD());
		}
		else
		{
			$server = DOG_Server::getBy('serv_connector', $connector->getName());
		}

		if ($server)
		{
			return $message->rply('err_dog_server_already_added', [$server->renderName()]);
		}

		# Add
		$data = [
			'serv_connector' => $connector->getName(),
		];

		if ($url)
		{
			$data['serv_url'] = $url->raw;
		}

		if ($username)
		{
			$data['serv_username'] = $username;
		}
		else
		{
			$data['serv_username'] = $this->getDefaultNickname();
		}

		if ($password)
		{
			$data['serv_password'] = $password;
		}

		$server = DOG_Server::blank($data);

		$connector->setupServer($server);

		$server->insert();

		Dog::instance()->servers[] = $server;

		$message->rply('msg_dog_server_added', [$server->getID(), $server->renderName()]);
	}

}
