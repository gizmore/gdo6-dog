<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\GDT_DogCommand;
use GDO\DB\GDT_String;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;

final class ConfigRoom extends DOG_Command
{
    public $trigger = 'confc';
    
    public function getPermission() { return Dog::HALFOP; } 
    
    public function gdoParameters()
    {
        return array(
            GDT_DogCommand::make('command')->notNull(),
            GDT_String::make('key'),
            GDT_String::make('var'),
        );
    }
    
    public function dogExecute(DOG_Message $message, DOG_Command $command, $key, $var)
    {
        if ($key === null)
        {
            return $this->showConfigKeys($message, $command);
        }
        
        elseif ($var === null)
        {
            return $this->showConfigVar($message, $command, $key);
        }
        
        else
        {
            return $this->setConfigVar($message, $command, $key, $var);
        }
        
    }
    
    private function showConfigKeys(DOG_Message $message, DOG_Command $command)
    {
        $keys = [];
        foreach ($command->getConfigRoom() as $gdt)
        {
            $keys[] = $gdt->name;
        }
        return $message->rply('msg_dog_config_keys', [$command->trigger, implode(', ', $keys)]);
    }
    
    private function showConfigVar(DOG_Message $message, DOG_Command $command, $key)
    {
        $var = $command->getConfigVarRoom($message->room, $key);
        if (!($command->getConfigGDTUser($key)))
        {
            return $message->rply('err_dog_var_unknown', [$command->trigger, $key] );
        }
        return $message->rply('msg_dog_config_key', [$command->trigger, $var]);
    }
    
    private function setConfigVar(DOG_Message $message, DOG_Command $command, $key, $var)
    {
        $old = $command->getConfigVarRoom($message->room, $key);
        if (!($gdt = $command->getConfigGDTUser($key)))
        {
            return $message->rply('err_dog_var_unknown', [$command->trigger, $key]);
        }
        
        $value = $gdt->getValue();
        if (!$gdt->validate($value))
        {
            return $message->rply('err_dog_config_invalid', [$key, $command->trigger, $var]);
        }
        
        $command->setConfigValueRoom($message->room, $key, $value);

        return $message->rply('msg_dog_config_set', [$key, $command->trigger, $old, $var]);
    }
    
}
