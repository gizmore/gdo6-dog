<?php
namespace GDO\Dog\Method;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_DogCommand;

class Help extends DOG_Command
{
    public $trigger = 'help';
    
    public function isWebMethod() { return true; }
    
    public function gdoParameters()
    {
        return array(
            GDT_DogCommand::make('command')
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
	        if ($command->trigger && $command->canExecute($message))
	        {
    	        if (!isset($grouped[$command->group]))
    	        {
    	            $grouped[$command->group] = [];
    	        }
    	        
    	        $grouped[$command->group][] = $command->trigger;
	        }
	        
	    }
	    
	    $b = "\x02";
	    $groupOut = [];
	    foreach ($grouped as $group => $triggers)
	    {
	        $groupOut[] = sprintf("{$b}$group{$b}: %s.", implode(', ', $triggers));
	    }
	    
	    $message->user->sendNotice($message->t('msg_dog_overall_help', [implode(' ', $groupOut)]));
	}

	private function showHelpFor(DOG_Message $message, DOG_Command $command)
	{
	    $message->rply('msg_dog_help', [$command->getUsageText($message), $command->getHelpText($message)]);
	}
	
}
