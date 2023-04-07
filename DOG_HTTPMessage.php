<?php
namespace GDO\Dog;

final class DOG_HTTPMessage extends DOG_Message
{

	private $reply;

	public function __construct()
	{
//		parent::__construct();
//     	self::$LAST_MESSAGE = $this;
	}

	public function getReply()
	{
		return $this->reply;
	}

	public function reply($text): bool
	{
		$this->reply = $text;
		return true;
	}

}
