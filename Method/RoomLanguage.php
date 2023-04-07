<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Language\GDO_Language;
use GDO\Language\GDT_Language;

final class RoomLanguage extends DOG_Command
{

	public $trigger = 'clang';

	public function isWebMethod() { return false; }

	public function isHiddenMethod() { return false; }

	public function isRoomMethod() { return true; }

	public function isPrivateMethod() { return false; }

	public function gdoParameters(): array
	{
		return [
			GDT_Language::make('lang')->positional(),
		];
	}

	public function dogExecute(DOG_Message $message, GDO_Language $lang = null)
	{
		$old = $message->room->getLanguage();
		if ($lang)
		{
			if ($lang === $old)
			{
				return $message->rply('err_nothing_happened');
			}
			else
			{
				$message->room->saveVar('room_lang', $lang->getID());
				return $message->rply('msg_dog_room_lang_now', [
					$old->renderName(), $lang->renderName()]);
			}
		}
		else
		{
			return $message->rply('msg_dog_room_lang', [
				$old->renderName()]);
		}
	}

}
