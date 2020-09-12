<?php
namespace GDO\Dog;

use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;

final class DOG_Install
{
    public static function onInstall(Module_Dog $module)
    {
        $permissions = ['voice', 'halfop', 'operator', 'owner'];
        foreach ($permissions as $permission)
        {
            $perm = GDO_Permission::getOrCreateByName($permission);
            foreach (GDO_User::admins() as $admin)
            {
                GDO_UserPermission::grantPermission($admin, $perm);
            }
        }
    }
    
}
