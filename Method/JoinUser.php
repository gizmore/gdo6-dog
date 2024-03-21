<?php
namespace GDO\Dog\Method;

use GDO\Core\GDT_Token;
use GDO\Core\Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\DOG_User;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

final class JoinUser extends DOG_Command
{

    public static function generateToken(DOG_User $dogUser, GDO_User $user): string
    {
        $did = $dogUser->getID();
        $uid = $user->getID();
        return GDT_Token::generateToken("{$did}{$uid}");
    }


    protected function createForm(GDT_Form $form): void
    {
        $form->addFields(
            GDT_User::make('with')->notNull(),
            GDT_Token::make('token')->notNull(),
            GDT_AntiCSRF::make(),
        );
        $form->actions()->addField(GDT_Submit::make());
    }

    public function dogExecute(DOG_Message $message, GDO_User $with, string $token)
    {
        if (self::generateToken($message->user, $with) !== $token)
        {
            return $this->error('err_dog_join_token');
        }



        return $this->message('msg_dog_join_users');

    }


//    public function gdoParameters(): array
//    {
//        return [
//        ];
//    }

}

