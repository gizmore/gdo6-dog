<?php
namespace GDO\Dog;

use GDO\Form\MethodForm;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Util\Strings;
use GDO\Util\Arrays;

abstract class DOG_Command extends MethodForm
{
	public $priority = 50;
	
	/**
	 * @var DOG_Command[]
	 */
	public static $COMMANDS = [];
	public static function register(DOG_Command $command) { self::$COMMANDS[] = $command; }
	
	public static function sortCommands()
	{
	    uasort(self::$COMMANDS, function(DOG_Command $a, DOG_Command $b) {
	        return $b->priority - $a->priority;
	    });
	}
	
	/**
	 * @param string $trigger
	 * @return self
	 */
	public static function byTrigger($trigger)
	{
		foreach (self::$COMMANDS as $command)
		{
			if ($trigger === $command->getTrigger())
			{
				return $command;
			}
		}
	}
	
	public function getConnectors()
	{
	    return array_map(
	        function(DOG_Connector $connector) {
	            return $connector->getName();
	        }, DOG_Connector::connectors());
	}
	
	public function getTrigger() {}
	
	public function getGroup() { return 'Various'; }
	
	public function isWebMethod() { return false; }
	
	public function createForm(GDT_Form $form)
	{
        $form->addFields($this->gdoParameters());
        $form->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
	    $args = [];
	    foreach ($form->getFields() as $gdt)
	    {
	        $args[] = $gdt->getValue();
	    }
	    $message = new DOG_HTTPMessage();
	    $this->dogExecute($message, ...$args);
	    return $this->message("%s", $message->getReply());
	}
	
	public function connectorMatches(DOG_Message $message)
	{
	    return in_array($message->server->getConnectorName(), $this->getConnectors());
	}
	
	public function onDogExecute(DOG_Message $message)
	{
	    if (!$this->connectorMatches($message))
	    {
	        $message->rply('err_wrong_connector', [Arrays::implodeHuman($this->getConnectors())]);
	        return false;
	    }
	    
	    if (!$this->hasUserPermission($message->getUser()))
	    {
	        $message->rply('err_permission');
	        return false;
	    }
	    
		$args = [];
		$_REQUEST = [];
		$text = Strings::substrFrom($message->text, ' ', '');
		$text = trim($text);
		foreach ($this->gdoParameters() as $gdt)
		{
		    if (!($gdt instanceof GDT_DogString))
		    {
		        $token = Strings::substrTo($text, ' ', $text);
		        $text = ltrim(Strings::substrFrom($text, ' ', ''));
		    }
		    else
		    {
		        $token = $text;
		        $text = '';
		    }
		    
		    $_REQUEST[$gdt->name] = $token;
			$value = $gdt->getParameterValue();
		    
		    if (!$gdt->validate($value))
		    {
		        $message->reply(sprintf('%s: %s', $gdt->name, $gdt->error));
		        $message->reply(t('usage', [$this->getTrigger(), $this->getUsageText()]));
		        return;
		    }
		    $args[] = $value;
		}
		$this->dogExecute($message, ...$args);
		return true;
	}
	
	public function getUsageText()
	{
	    $usage = [];
	    foreach ($this->gdoParameters() as $gdt)
	    {
	        $dots = ($gdt instanceof GDT_DogString) ? '...' : '';
	        
	        if ($gdt->notNull)
	        {
	            $usage[] = "<{$dots}{$gdt->name}{$dots}>";
	        }
	        else
	        {
	            $usage[] = "[<{$dots}{$gdt->name}{$dots}>]";
	        }
	    }
	    return implode(" ", $usage);
	}
	
}
