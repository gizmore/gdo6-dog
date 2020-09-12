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
	
	private function showHelpFor(DOG_Message $message, DOG_Command $command)
	{
	    
	}
	
	private function showOverallHelp(DOG_Message $message)
	{
	    
	}
}
