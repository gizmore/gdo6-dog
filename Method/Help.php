<?php
namespace GDO\Dog\Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_DogCommand;
use GDO\Util\Strings;

class Help extends DOG_Command
{
    public $group = '';
    public $trigger = 'help';
    
    public function isWebMethod() { return true; }
    
    public function gdoParameters() : array
    {
        return array(
            GDT_DogCommand::make('command')->positional(),
        );
    }
    
	public function dogExecute(DOG_Message $message, DOG_Command $command=null)
	{
		if ($command)
		{
		    return $this->showHelpFor($message, $command);
		}
		else
		{
		    return $this->showOverallHelp($message);
		}
	}
	
	private function showOverallHelp(DOG_Message $message)
	{
	    $grouped = [];
	    foreach (DOG_Command::$COMMANDS as $command)
	    {
	        $trigger = $command->getCLITrigger();
	        $group = Strings::substrTo($trigger, '.', 'Dog');
	        $trigger = Strings::substrFrom($trigger, '.', $trigger);
	        if ($trigger && $command->canExecute($message))
	        {
    	        if (!isset($grouped[$group]))
    	        {
    	            $grouped[$group] = [];
    	        }
    	        
    	        $grouped[$group][] = $trigger;
	        }
	    }
	    
	    ksort($grouped);
	    foreach ($grouped as $k => $group)
	    {
	        asort($group);
	        $grouped[$k] = $group;
	    }
	    
	    $b = "\x02";
	    $groupOut = [];
	    foreach ($grouped as $group => $triggers)
	    {
	        $groupOut[] = sprintf("{$b}$group{$b}: %s.",
	           implode(', ', $triggers));
	    }
	    
	    $message->user->sendNotice(
	        $message->t('msg_dog_overall_help', [
	            implode(' ', $groupOut)]));
	}

	private function showHelpFor(DOG_Message $message, DOG_Command $command)
	{
	    $message->rply('msg_dog_help', [
	        $command->getUsageText($message),
	        $command->getHelpText($message)]);
	}
	
}
