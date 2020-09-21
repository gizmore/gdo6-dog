<?php
namespace GDO\Dog;

use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;

final class DOG_Install
{
    public static function onInstall(Module_Dog $module)
    {
        $permissions = array(
            'voice' => 100,
            'halfop' => 300,
            'staff' => 500,
            'operator' => 600,
            'owner' => 900,
            'admin' => 1000,
        );
        
        foreach ($permissions as $permission => $level)
        {
            $perm = GDO_Permission::create($permission, $level);
            foreach (GDO_User::admins() as $admin)
            {
                GDO_UserPermission::grantPermission($admin, $perm);
            }
        }
    }
    
}
