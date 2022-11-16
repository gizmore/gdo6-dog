<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\GDT_DogCommand;
use GDO\Dog\DOG_Message;
use GDO\Core\GDT_Checkbox;
use GDO\Dog\Dog;

final class Disable extends DOG_Command
{
    public $trigger = 'disable';
    
    public function getPermission() : ?string { return Dog::OPERATOR; }
    
    /**
     * @return self
     */
    public static function instance()
    {
        return DOG_Command::byTrigger('disable');
    }
    
    public function gdoParameters() : array
    {
        return array(
            GDT_DogCommand::make('command')->notNull(),
        );
    }
    
    public function getConfigRoom()
    {
        $conf = [];
        foreach (DOG_Command::$COMMANDS_T as $command)
        {
            $name = 'disable_'.$command->getCLITrigger();
            $conf[] = GDT_Checkbox::make($name)->notNull()->initial('0');
        }
        return $conf;
    }
    
    public function getConfigServer()
    {
        $conf = [];
        foreach (DOG_Command::$COMMANDS_T as $command)
        {
            $name = 'disable_'.$command->getCLITrigger();
            $conf[] = GDT_Checkbox::make($name)->notNull()->initial('0');
        }
        return $conf;
    }
    
    public function isDisabled(DOG_Message $message, DOG_Command $command)
    {
    	if (isset($message->room))
        {
            if ($this->isDisabledRoom($message, $command))
            {
                return true;
            }
        }
        return $this->isDisabledServer($message, $command);
    }
        
    public function isDisabledRoom(DOG_Message $message, DOG_Command $command)
    {
        $key = 'disable_'.$command->getCLITrigger();
        return $this->getConfigValueRoom($message->room, $key);
    }
    
    public function isDisabledServer(DOG_Message $message, DOG_Command $command)
    {
        $key = 'disable_'.$command->getCLITrigger();
        return $this->getConfigValueServer($message->server, $key);
    }
    
    public function dogExecute(DOG_Message $message, DOG_Command $command)
    {
        if ($command === $this)
        {
            return $message->rply('err_cannot_disable');
        }
        
        if ($message->room)
        {
            if ($this->isDisabledRoom($message, $command))
            {
                return $message->rply('msg_dog_already_disabled', [$command->getCLITrigger()]);
            }
            $key = 'disable_'.$command->getCLITrigger();
            $this->setConfigValueRoom($message->room, $key, true);
            return $message->rply('msg_dog_disabled', [$command->getCLITrigger()]);
        }
        else
        {
            if ($this->isDisabledServer($message, $command))
            {
                return $message->rply('msg_dog_already_disabled', [$command->getCLITrigger()]);
            }
            $key = 'disable_'.$command->getCLITrigger();
            $this->setConfigValueServer($message->server, $key, true);
            return $message->rply('msg_dog_disabled', [$command->getCLITrigger()]);
        }
    }
    
}
