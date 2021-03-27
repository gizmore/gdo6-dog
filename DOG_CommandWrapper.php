<?php
namespace GDO\Dog;

use GDO\Core\Method;
use GDO\Form\MethodForm;
use GDO\Core\MethodAjax;

final class DOG_CommandWrapper extends DOG_Command
{
    private $method;
    private $command;
    
    public function __construct(Method $method, $trigger=null)
    {
        $module = $method->getModule();
        $this->method = $method;
        $this->group = $module->getName();
        $this->priority = $module->module_priority;
        $this->trigger = strtolower($this->group . '.' . $method->gdoShortName());
    }
    
    public function gdoParameters()
    {
        if ($this->method instanceof MethodForm)
        {
            return array_merge(
                $this->method->getForm()->getFields(),
                $this->method->gdoParameters()); 
        }
    }
    
//     public function dogExecute(DOG_Message $message, ...$args)
//     {
//         if ($this->method instanceof MethodForm)
//         {
//             $this->dogExecuteForm($message, ...$args);
//         }
//         else
//         {
//             $this->dogExecuteMethod($message, ...$args);
//         }
//     }
    
//     private function dogExecuteMethod(DOG_Message $message, ...$args)
//     {
        
//     }
    
//     private function dogExecuteForm(DOG_Message $message, ...$args)
//     {
        
//     }
    
}

