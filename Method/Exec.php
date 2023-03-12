<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Core\Expression\Parser;
use GDO\UI\GDT_Page;

/**
 * Listens to the `dog_message` event and calls a command.
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.2
 */
final class Exec extends DOG_Command
{
    public function isHiddenMethod() { return true; }
    public function isRoomMethod() { return false; }
    public function isPrivateMethod() { return false; }
    
    public function dog_message(DOG_Message $message)
	{
	    $text = $message->text;
	    
	    # Remove trigger char if inside room.
	    if (isset($message->room))
	    {
	        if (!str_starts_with($text, $message->room->getTrigger()))
	        {
	            return;
	        }
	        $text = substr($text, 1);
	    }
	    
// 		$trigger = strtolower(Strings::substrTo($text, ' ', $text));
		
		$parser = new Parser();
		$exp = $parser->parse($text);
		$exp->method->runAs($message->user->getGDOUser());
		$result = $exp->execute();
		
		$response = GDT_Page::instance()->topResponse()->render();
		$response .= "\n";
		$response .= $result->render();
		return $message->reply(trim($response));
	}

}
