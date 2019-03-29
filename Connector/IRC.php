<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Connector;
use GDO\Core\Logger;
use GDO\Dog\DOG_Message;

class IRC extends DOG_Connector
{
	private $timestamp;
	private $socket;
	private $context;
	
    public function connect()
    {
    	if (false === ($this->context = @stream_context_create()))
    	{
    		Logger::logError('IRC Connector cannot create stram context.');
    		return false;
    	}
    	
    	$errno = 0; $errstr = '';
    	if (false === ($socket = @stream_socket_client(
    		$this->server->getConnectURL(),
    		$errno,
    		$errstr,
    		$this->server->getConnectTimeout(),
    		STREAM_CLIENT_CONNECT,
    		$this->context)))
    	{
    		Logger::logError('IRC Connector cannot create stram context.');
    		Logger::logError('IRC Connector cannot create stram context.');
    		Logger::logError("Dog_IRC::connect() ERROR: stream_socket_client(): URL={$this->server->getURL()} CONNECT_TIMEOUT={$this->server->getConnectTimeout()}");
    		Logger::logError(sprintf('Dog_IRC::connect() $errno=%d; $errstr=%s', $errno, $errstr));
    	}
    	
    	if ($this->server->isTLS())
    	{
    		if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT))
    		{
    			Logger::logError('Dog_IRC::connect() ERROR: stream_socket_enable_crypto(true, STREAM_CRYPTO_METHOD_TLS_CLIENT)');
    			return false;
    		}
    	}
    	
    	if (!@stream_set_blocking($socket, 0))
    	{
    		Logger::logError('Dog_IRC::connect() ERROR: stream_set_blocking(): $blocked=0');
    		return false;
    	}
    	
    	$this->timestamp = time();
    	$this->socket = $socket;
    	$this->connected(true);
    	return true;
    }
    
	public function readMessage()
	{
		if (feof($this->socket))
		{
			$this->disconnect('I got feof!');
			return false;
		}
		$raw = fgets($this->socket, 2047);
		return DOG_Message::make()->raw($raw);
	}
	
	public function sendTo($to, $text)
	{
		
	}
}

DOG_Connector::register(new IRC());
