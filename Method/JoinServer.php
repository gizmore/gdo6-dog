<?php
namespace GDO\Dog\Method;
use GDO\Net\GDT_Url;
use GDO\Net\GDT_Port;
use GDO\Dog\DOG_Command;
use GDO\DB\GDT_Checkbox;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_Server;

final class JoinServer extends DOG_Command
{
	public function gdoParameters()
	{
		return array(
			GDT_Url::make('url')->notNull(),
			GDT_Port::make('port')->initial('6667'),
			GDT_Checkbox::make('tls')->initial('0'),
		);
	}

	public function dogExecute(DOG_Message $message, $url, $port, $tls)
	{
		if (!($server = DOG_Server::getByURL($url)))
		{
			$server = DOG_Server::blank(array(
				
			));
		}
	}

}

DOG_Command::register(new JoinServer());
