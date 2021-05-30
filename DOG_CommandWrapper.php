<?php
namespace GDO\Dog;

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
        return $this->method->allParameters();
    }
    
    public function dogExecute(DOG_Message $message, ...$args)
    {
        return $message->reply($this->method->exec()->renderCLI());
    }
    
}
