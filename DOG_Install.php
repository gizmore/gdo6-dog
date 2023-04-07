<?php
declare(strict_types=1);
namespace GDO\Dog;

use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;

final class DOG_Install
{

	public static function onInstall(Module_Dog $module): void
	{
		$permissions = [
			'operator',
			'halfop',
			'voice',
		];

		foreach ($permissions as $permission)
		{
			$perm = GDO_Permission::create($permission);
			foreach (GDO_User::admins() as $admin)
			{
				GDO_UserPermission::grantPermission($admin, $perm);
			}
		}
	}

}
