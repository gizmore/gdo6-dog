<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\GDT_DogCommand;
use GDO\Core\GDT_String;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;

final class ConfigServer extends DOG_Command
{
    public $trigger = 'confs';
    
    public function getPermission() : ?string { return Dog::OPERATOR; } 
    
    public function gdoParameters() : array
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
        foreach ($command->getConfigServer() as $gdt)
        {
            $keys[] = $gdt->name;
        }
        return $message->rply('msg_dog_config_keys', [$command->trigger, implode(', ', $keys)]);
    }
    
    private function showConfigVar(DOG_Message $message, DOG_Command $command, $key)
    {
        if (!($command->getConfigGDTServer($key)))
        {
            return $message->rply('err_dog_var_unknown', [$command->trigger, $key] );
        }
        $var = $command->getConfigVarServer($message->server, $key);
        return $message->rply('msg_dog_config_key', [$command->trigger, $var]);
    }
    
    private function setConfigVar(DOG_Message $message, DOG_Command $command, $key, $var)
    {
        if (!($gdt = $command->getConfigGDTServer($key)))
        {
            return $message->rply('err_dog_var_unknown', [$command->trigger, $key]);
        }
        
        $old = $command->getConfigVarServer($message->server, $key);
        $value = $gdt->toValue($gdt->inputToVar($var));
        if (!$gdt->validate($value))
        {
            return $message->rply('err_dog_config_invalid', [$key, $command->trigger, $var]);
        }
        $new = $gdt->toVar($value);
        
        $command->setConfigValueServer($message->server, $key, $value);

        return $message->rply('msg_dog_config_set', [
            $key, $command->trigger,
            $gdt->displayValue($old), $gdt->displayValue($new)]);
    }
    
}
