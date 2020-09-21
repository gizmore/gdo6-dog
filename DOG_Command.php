<?php
namespace GDO\Dog;

use GDO\Form\MethodForm;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Util\Strings;
use GDO\Util\Arrays;
use GDO\Core\Logger;
use GDO\Core\GDT;
use GDO\Dog\Method\Disable;
use GDO\DB\GDT_Enum;
use GDO\UI\GDT_Confirm;

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
	
	public function getDefaultNickname() { return Module_Dog::instance()->getDefaultNickname(); }
	
	/**
	 * @return GDT[]
	 */
	public function getConfigBot() { return []; }
	
	private $ccBot = null;
	private function getConfigBotCached()
	{
	    if ($this->ccBot === null)
	    {
	        $this->ccBot = [];
	        foreach ($this->getConfigBot() as $gdt)
	        {
	            $this->ccBot[$gdt->name] = $gdt;
	        }
	    }
	    return $this->ccBot;
	}
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTBot($key)
	{
	    $conf = $this->getConfigBotCached();
	    return @$conf[$key];
	}
	
	public function getConfigVarBot($key)
	{
	    if ($var = DOG_ConfigBot::table()->getById($this->gdoClassName(), $key))
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
	        'confb_command' => $this->gdoClassName(),
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

	private $ccUser = null;
	private function getConfigUserCached()
	{
	    if ($this->ccUser === null)
	    {
	        $this->ccUser = [];
	        foreach ($this->getConfigUser() as $gdt)
	        {
	            $this->ccUser[$gdt->name] = $gdt;
	        }
	    }
	    return $this->ccUser;
	}
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTUser($key)
	{
	    $conf = $this->getConfigUserCached();
	    return @$conf[$key];
	}
	
	public function getConfigVarUser(DOG_User $user, $key)
	{
	    if ($var = DOG_ConfigUser::table()->getById($this->gdoClassName(), $key, $user->getID()))
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
	        'confu_command' => $this->gdoClassName(),
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

	private $ccRoom = null;
	private function getConfigRoomCached()
	{
	    if ($this->ccRoom === null)
	    {
	        $this->ccRoom = [];
	        foreach ($this->getConfigRoom() as $gdt)
	        {
	            $this->ccRoom[$gdt->name] = $gdt;
	        }
	    }
	    return $this->ccRoom;
	}
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTRoom($key)
	{
	    $conf = $this->getConfigRoomCached();
	    return @$conf[$key];
	}
	
	public function getConfigVarRoom(DOG_Room $room, $key)
	{
	    if ($var = DOG_ConfigRoom::table()->getById($this->gdoClassName(), $key, $room->getID()))
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
	    $gdt = $this->getConfigGDTRoom($key);
	    $var = $gdt->toVar($value);
	    DOG_ConfigRoom::blank(array(
	        'confr_command' => $this->gdoClassName(),
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

	private $ccServer = null;
	private function getConfigServerCached()
	{
	    if ($this->ccServer === null)
	    {
	        $this->ccServer = [];
	        foreach ($this->getConfigServer() as $gdt)
	        {
	            $this->ccServer[$gdt->name] = $gdt;
	        }
	    }
	    return $this->ccServer;
	}
	
	/**
	 * @param string $key
	 * @return GDT
	 */
	public function getConfigGDTServer($key)
	{
	    $conf = $this->getConfigServerCached();
	    return @$conf[$key];
	}
	
	public function getConfigVarServer(DOG_Server $server, $key)
	{
	    if ($var = DOG_ConfigServer::table()->getById($this->gdoClassName(), $key, $server->getID()))
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
	    DOG_ConfigServer::blank(array(
	        'confs_command' => $this->gdoClassName(),
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
	
	/**
	 * @var DOG_Command[]
	 */
	public static $COMMANDS_T = []; # By trigger
	
	public static function register(DOG_Command $command)
	{
	    self::$COMMANDS[] = $command;
	    if ($command->trigger)
	    {
    	    self::$COMMANDS_T[$command->trigger] = $command;
	    }
	}
	
	public static function sortCommands()
	{
	    uasort(self::$COMMANDS, function(DOG_Command $a, DOG_Command $b) {
	        return $a->priority - $b->priority;
	    });
        uasort(self::$COMMANDS_T, function(DOG_Command $a, DOG_Command $b) {
            return $a->priority - $b->priority;
        });
    }
	
	/**
	 * Get a command by trigger.
	 * @param string $trigger
	 * @return self
	 */
	public static function byTrigger($trigger)
	{
	    return @self::$COMMANDS_T[$trigger];
	}
	
	/**
	 * Get supported connectors for this command.
	 * @return string[]
	 */
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
	
	public function canExecute(DOG_Message $message)
	{
	    if (!$this->connectorMatches($message)) { return false; }
	    if ( (!$this->isRoomMethod()) && ($message->room) ) { return false; }
	    if ( (!$this->isPrivateMethod()) && (!$message->room) ) { return false; }
	    if (!$this->hasUserPermission($message->getGDOUser())) { return false; }
	    if ( ($this->getPermission()) && ($message->user->isRegistered() && (!$message->user->isAuthenticated())) ) { return false; }
	    return true;
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
	    
	    if ( ($this->getPermission()) && ($message->user->isRegistered() && (!$message->user->isAuthenticated())) )
	    {
	        return $message->rply('err_authenticate_first');
	    }
	    
	    if (!$this->hasUserPermission($message->getGDOUser()))
	    {
	        return $message->rply('err_no_permission');
	    }
	    
	    /**
	     * @var Disable $disable
	     */
	    $disable = DOG_Command::byTrigger('disable');
	    if ($disable->isDisabled($message, $this))
	    {
	        return $message->rply('err_disabled');
	    }
	    
		$args = [];
		$_REQUEST = [];
		if ($message->room)
		{
    		$text = mb_substr($message->text, 1);
		}
		else
		{
		    $text = $message->text;
		}
		
		$text = trim(Strings::substrFrom($text, ' ', ''));

		try
		{
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
		        
		        $_REQUEST[$gdt->name] = $token ? $token : $gdt->initial;
		        $value = $gdt->getParameterValue();
		        
		        if (!$gdt->validate($value))
		        {
		            $usage = $this->getUsageText($message);
		            $message->reply(sprintf('%s: %s %s', $gdt->name, $gdt->error, $usage));
		            return false;
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
		catch (\Error $e)
		{
		    $message->rply('err_dog_error', [get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()]);
		}
		catch (\Exception $e)
		{
		    $message->rply('err_dog_exception', [get_class($e), $e->getMessage()]);
		}
		return false;
	}
	
	public function getUsageText(DOG_Message $message)
	{
	    $usage = [];
	    foreach ($this->gdoParameters() as $gdt)
	    {
	        $dots = ($gdt instanceof GDT_DogString) ? '...' : '';
	        
	        $brk_open = $gdt->notNull ? '<' : '[<';
	        $brk_close = $gdt->notNull ? '>' : '>]';
	        
	        if ($gdt instanceof GDT_Enum)
	        {
	            $name = implode('|', $gdt->enumValues);
	        }
	        elseif ($gdt instanceof GDT_Confirm)
	        {
	            $name = t($gdt->confirmation);
	        }
	        else
	        {
	            $name = $gdt->name;
	        }
	        $usage[] = $brk_open . $dots . $name . $dots . $brk_close;
	    }
	    return $message->t('usage', [$this->trigger, implode(' ', $usage)]);
	}
	
	public function getHelpText(DOG_Message $message)
	{
	    return $message->t("dog_help_{$this->trigger}");
	}
	
}
