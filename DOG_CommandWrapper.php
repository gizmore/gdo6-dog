<?php
namespace GDO\Dog;

use GDO\Form\MethodForm;
use GDO\Core\Method;

/**
 * Wrap a gdo method in a dog command. 
 * @author gizmore
 */
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
        $this->trigger = $method->gdoShortName();
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
    
    public function dogExecute(DOG_Message $message, ...$args)
    {
        if ($this->method instanceof MethodForm)
        {
            $this->dogExecuteForm($message, ...$args);
        }
        else
        {
            $this->dogExecuteMethod($message, ...$args);
        }
    }
    
    private function dogExecuteMethod(DOG_Message $message, ...$args)
    {
        return $message->reply($this->method->exec()->render());
    }
    
    private function dogExecuteForm(DOG_Message $message, ...$args)
    {
        return $message->reply($this->method->exec()->render());
    }
    
}
