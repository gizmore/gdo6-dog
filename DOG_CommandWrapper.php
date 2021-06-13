<?php
namespace GDO\Dog;

use GDO\Core\Method;
use GDO\Core\Website;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Form;

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
    
    public function init()
    {
        return $this->method->init();
    }
    
    public function getForm()
    {
        if ($this->method instanceof MethodForm)
        {
            return $this->method->getForm();
        }
    }
    
    public function beforeExecute() { return $this->method->beforeExecute(); }
    public function isUserRequired() { return $this->method->isUserRequired(); }
    public function isGuestAllowed() { return $this->method->isGuestAllowed(); }
    
    public function formName()
    {
        if ($this->method instanceof MethodForm)
        {
            return $this->method->formName();
        }
        return GDT_Form::DEFAULT_NAME;
    }
    
    public function &gdoParameterCache()
    {
        return $this->method->gdoParameterCache();
    }
    
    public function gdoParameters()
    {
        return $this->method->gdoParameters();
    }
    
    public function allParameters()
    {
        return $this->method->allParameters();
    }
    
    public function dogExecute(DOG_Message $message, ...$args)
    {
        try
        {
            $response = $this->method->exec()->renderCLI();
            if (Website::$TOP_RESPONSE)
            {
                $response .= ' ' . Website::$TOP_RESPONSE->renderCLI();
            }
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
 
    public function getTitle()
    {
        return $this->method->getTitle();
    }
    
    public function getDescription()
    {
        return $this->method->getDescription();
    }
    
    public function getButtons()
    {
        return $this->method->getButtons();
    }
    
}
