<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_String;

final class DOG_ConfigRoom extends GDO
{

	public function gdoColumns(): array
	{
		return [
			GDT_String::make('confr_command')->primary()->ascii()->notNull()->max(128),
			GDT_String::make('confr_key')->ascii()->primary()->notNull()->max(64),
			GDT_Room::make('confr_room')->primary()->notNull(),
			GDT_String::make('confr_var'),
		];
	}

}
