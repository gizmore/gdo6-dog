<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_String;

final class DOG_ConfigUser extends GDO
{

	public function gdoColumns(): array
	{
		return [
			GDT_String::make('confu_command')->primary()->ascii()->notNull()->max(128),
			GDT_String::make('confu_key')->ascii()->primary()->notNull()->max(64),
			GDT_DogUser::make('confu_user')->primary()->notNull()->cascade(),
			GDT_String::make('confu_var'),
		];
	}

}
