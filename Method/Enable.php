<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\GDT_DogCommand;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;

final class Enable extends DOG_Command
{
    public $trigger = 'enable';
    
    public function getPermission() : ?string { return Dog::OPERATOR; }
    
    public function gdoParameters() : array
    {
        return array(
            GDT_DogCommand::make('command')->notNull(),
        );
    }
    
    public function dogExecute(DOG_Message $message, DOG_Command $command)
    {
        $disable = Disable::instance();
        
        if ($message->room)
        {
            if (!$disable->isDisabledRoom($message, $command))
            {
                return $message->rply('msg_dog_not_disabled', [$command->getCLITrigger()]);
            }
            $key = 'disable_'.$command->getCLITrigger();
            $disable->setConfigValueRoom($message->room, $key, false);
            return $message->rply('msg_dog_enabled', [$command->getCLITrigger()]);
        }
        else
        {
            if (!$disable->isDisabledServer($message, $command))
            {
                return $message->rply('msg_dog_not_disabled', [$command->getCLITrigger()]);
            }
            $key = 'disable_'.$command->getCLITrigger();
            $disable->setConfigValueServer($message->server, $key, false);
            return $message->rply('msg_dog_enabled', [$command->getCLITrigger()]);
        }
    }
    
}
