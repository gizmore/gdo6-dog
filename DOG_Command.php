<?php
namespace GDO\Dog;

use GDO\Form\MethodForm;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Util\Strings;
use GDO\Util\Arrays;
use GDO\Core\Logger;
use GDO\Core\GDT;

abstract class DOG_Command extends MethodForm
{
	public $priority = 50;
	public $trigger = null;
	public $group = 'Various';
	
	public function isWebMethod() { return false; }
	public function isHiddenMethod() { return false; }
	public function isRoomMethod() { return true; }
	public function isPrivateMethod() { return true; }
	
	##################
	### Config Bot ###
	##################
	
	/**
	 * @return GDT[]
	 */
	public function getConfigBot() { return []; }
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTBot($key)
	{
	    foreach ($this->getConfigBot() as $gdt)
	    {
	        if ($gdt->name === $key)
	        {
	            return $gdt;
	        }
	    }
	}
	
	public function getConfigVarBot($key)
	{
	    if ($var = DOG_ConfigBot::table()->getById($key))
	    {
	        return $var->getVar('confb_var');
	    }
	    return $this->getConfigGDTBot($key)->initial;
	}
	
	public function getConfigValueBot($key)
	{
	    $gdt = $this->getConfigGDTBot($key);
	    return $gdt->toValue($this->getConfigVarBot($key));
	}
	
	public function setConfigVarBot($key, $var)
	{
	    $gdt = $this->getConfigGDTBot($key);
	    $value = $gdt->toValue($var);
	    return $this->setConfigValueBot($key, $value);
	}
	
	public function setConfigValueBot($key, $value)
	{
	    $gdt = $this->getConfigGDTBot($key);
	    $var = $gdt->toVar($value);
	    DOG_ConfigBot::blank(array(
	        'confb_key' => $key,
	        'confb_var' => $var,
	    ))->replace();
	    return true;
	}
	
	###################
	### Config User ###
	###################
	/**
	 * @return GDT[]
	 */
	public function getConfigUser() { return []; }
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTUser($key)
	{
	    foreach ($this->getConfigUser() as $gdt)
	    {
	        if ($gdt->name === $key)
	        {
	            return $gdt;
	        }
	    }
	}
	
	public function getConfigVarUser(DOG_User $user, $key)
	{
	    if ($var = DOG_ConfigUser::table()->getById($key, $user->getID()))
	    {
	        return $var->getVar('confu_var');
	    }
	    return $this->getConfigGDTUser($key)->initial;
	}
	
	public function getConfigValueUser(DOG_User $user, $key)
	{
	    $gdt = $this->getConfigGDTUser($key);
	    return $gdt->toValue($this->getConfigVarUser($user, $key));
	}
	
	public function setConfigVarUser(DOG_User $user, $key, $var)
	{
	    $gdt = $this->getConfigGDTUser($key);
	    $value = $gdt->toValue($var);
	    return $this->setConfigValueUser($user, $key, $value);
	}
	
	public function setConfigValueUser(DOG_User $user, $key, $value)
	{
	    $gdt = $this->getConfigGDTUser($key);
	    $var = $gdt->toVar($value);
	    DOG_ConfigUser::blank(array(
	        'confu_key' => $key,
	        'confu_user' => $user->getID(),
	        'confu_var' => $var,
	    ))->replace();
	    return true;
	}
	
	###################
	### Config Room ###
	###################
    /**
	 * @return GDT[]
	 */
	public function getConfigRoom() { return []; }
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTRoom($key)
	{
	    foreach ($this->getConfigRoom() as $gdt)
	    {
	        if ($gdt->name === $key)
	        {
	            return $gdt;
	        }
	    }
	}
	
	public function getConfigVarRoom(DOG_Room $room, $key)
	{
	    if ($var = DOG_ConfigRoom::table()->getById($key, $room->getID()))
	    {
	        return $var->getVar('confr_var');
	    }
	    return $this->getConfigGDTRoom($key)->initial;
	}
	
	public function getConfigValueRoom(DOG_Room $room, $key)
	{
	    $gdt = $this->getConfigGDTRoom($key);
	    return $gdt->toValue($this->getConfigVarRoom($room, $key));
	}
	
	public function setConfigVarRoom(DOG_Room $room, $key, $var)
	{
	    $gdt = $this->getConfigGDTRoom($key);
	    $value = $gdt->toValue($var);
	    return $this->setConfigValueRoom($key, $value);
	}
	
	public function setConfigValueRoom(DOG_Room $room, $key, $value)
	{
	    $gdt = $this->getConfigGDTUser($key);
	    $var = $gdt->toVar($value);
	    DOG_ConfigRoom::blank(array(
	        'confr_key' => $key,
	        'confr_room' => $room->getID(),
	        'confr_var' => $var,
	    ))->replace();
	    return true;
	}
	
	#####################
	### Config Server ###
	#####################
	
	/**
	 * @return GDT[]
	 */
	public function getConfigServer() { return []; }

	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTServer($key)
	{
	    foreach ($this->getConfigServer() as $gdt)
	    {
	        if ($gdt->name === $key)
	        {
	            return $gdt;
	        }
	    }
	}
	
	public function getConfigVarServer(DOG_Server $server, $key)
	{
	    if ($var = DOG_ConfigServer::table()->getById($key, $server->getID()))
	    {
	        return $var->getVar('confs_var');
	    }
	    return $this->getConfigGDTServer($key)->initial;
	}
	
	public function getConfigValueServer(DOG_Server $server, $key)
	{
	    $gdt = $this->getConfigGDTServer($key);
	    return $gdt->toValue($this->getConfigVarServer($server, $key));
	}
	
	public function setConfigVarServer(DOG_Server $server, $key, $var)
	{
	    $gdt = $this->getConfigGDTServer($key);
	    $value = $gdt->toValue($var);
	    return $this->setConfigValueServer($server, $key, $value);
	}
	
	public function setConfigValueServer(DOG_Server $server, $key, $value)
	{
	    $gdt = $this->getConfigGDTServer($key);
	    $var = $gdt->toVar($value);
	    DOG_ConfigBot::blank(array(
	        'confs_key' => $key,
	        'confs_user' => $server->getID(),
	        'confs_var' => $var,
	    ))->replace();
	    return true;
	}
	
	##################
	### Repository ###
	##################
	
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
		    if ($command->trigger === $trigger)
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
	        return $message->rply('err_wrong_connector', [Arrays::implodeHuman($this->getConnectors())]);
	    }
	    
	    if ( (!$this->isRoomMethod()) && ($message->room) )
	    {
	        return $message->rply('err_not_in_room');
	    }
	    
	    if ( (!$this->isPrivateMethod()) && (!$message->room) )
	    {
	        return $message->rply('err_not_in_private');
	    }
	    
	    if (!$this->hasUserPermission($message->getUser()))
	    {
	        return $message->rply('err_no_permission');
	    }
	    
		$args = [];
		$_REQUEST = [];
		$text = mb_substr($message->text, mb_strlen($this->trigger));
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
		        $message->reply(t('usage', [$this->trigger, $this->getUsageText()]));
		        return;
		    }
		    $args[] = $value;
		}

		if (defined('GWF_CONSOLE_VERBOSE'))
		{
		    Logger::logCron("executing " . $this->gdoClassName());
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
