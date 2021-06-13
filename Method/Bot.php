<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\DB\GDT_Checkbox;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_DogUser;
use GDO\Dog\DOG_User;
use GDO\User\GDO_User;

final class Bot extends DOG_Command
{
    public $trigger = 'bot';
    
    public function gdoParameters()
    {
        return [
            GDT_DogUser::make('user')->sameServer()->notNull(),
            GDT_Checkbox::make('botflag')->undetermined(),
        ];
    }
    
    public function dogExecute(DOG_Message $message, DOG_User $user, $botflag)
    {
        if ($user->isService())
        {
            return $message->rply('err_dog_user_is_service');
        }
        
        $u = $user->getGDOUser();
        if ($botflag === null)
        {
            $k = $u->isBot() ? 'msg_dog_is_bot' : 'msg_dog_no_bot';
            return $message->rply($k, [$user->displayName()]);
        }
        
        if ($u->isBot() === $botflag)
        {
            return $message->rply('err_nothing_happened');
        }
        
        if ($botflag)
        {
            $u->saveVar('user_type', GDO_User::BOT);
            return $message->rply('msg_user_now_bot');
        }
        else
        {
            $u->saveVar('user_type', GDO_User::MEMBER);
            return $message->rply('msg_user_no_bot_anymore');
        }
    }
    
}
