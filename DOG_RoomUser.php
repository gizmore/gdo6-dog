<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;

final class DOG_RoomUser extends GDO
{

    public function gdoCached(): bool
    {
        return false;
    }

    public function gdoColumns(): array
    {
        return [
            GDT_DogUser::make('ru_user')->notNull()->primary(),
            GDT_Room::make('ru_room')->notNull()->primary(),
        ];
    }

    public static function joined(DOG_User $user, DOG_Room $room): void
    {
        if (!self::getById($user->getID(), $room->getID()))
        {
            self::blank([
                'ru_user' => $user->getID(),
                'ru_room' => $room->getID(),
            ])->insert();
        }
    }

}
