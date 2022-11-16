<?php
namespace GDO\Dog;

use GDO\Core\Method;
use GDO\Form\GDT_Form;
use GDO\CLI\CLI;
use GDO\Core\GDO_Module;

/**
 * Wrap a gdo method in a dog command.
 * Used to wrap certain incompatible methods?
 * 
 * @author gizmore
 * @deprecated
 */
final class DOG_CommandWrapper extends DOG_Command
{
    private Method $method;
    
//     public function __construct(Method $method, $trigger=null)
//     {
//         $module = $method->getModule();
//         $this->method = $method;
// //         $this->group = $module->getModuleName();
// //         $this->priority = $module->priority;
// //         $this->trigger = $method->gdoShortName();
//     }
    
    public function getModule(): GDO_Module
    {
    	return $this->method->getModule();
    }
    
    public function onMethodInit(): void
    {
        $this->method->onMethodInit();
    }

    public function getForm(bool $resetForm=false): GDT_Form
    {
//     	if ($this->method instanceof MethodForm)
//     	{
			return $this->method->getForm($resetForm);
//     	}
    }
    
    public function beforeExecute() : void { $this->method->beforeExecute(); }
    public function isUserRequired() : bool { return $this->method->isUserRequired(); }
    public function isGuestAllowed() : bool { return $this->method->isGuestAllowed(); }
    
    public function getFormName(): string
    {
		return $this->method->getFormName();
    }
    
    public function &gdoParameterCache(): array
    {
        return $this->method->gdoParameterCache();
    }
    
    public function gdoParameters() : array
    {
        return $this->method->gdoParameters();
    }
    
    public function dogExecute(DOG_Message $message, ...$args)
    {
        try
        {
            $response = $this->method->exec()->render();
            $response = CLI::getTopResponse() . $response;
            return $message->reply(trim($response));
        }
        catch (\Throwable $ex)
        {
            return $message->reply($ex->getMessage());
        }
    }
    
    public function getCLITrigger()
    {
        $m = $this->method;
        $t = sprintf('%s.%s', $m->getModuleName(),  $m->getMethodName());
        return strtolower($t);
    }
 
    public function getMethodTitle(): string
    {
        return $this->method->getMethodTitle();
    }
    
    public function getMethodDescription(): string
    {
        return $this->method->getMethodDescription();
    }
    
//     public function getButtons()
//     {
//         return $this->method->getButtons();
//     }
    
}
