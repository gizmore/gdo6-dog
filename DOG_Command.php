<?php
namespace GDO\Dog;

use GDO\Form\MethodForm;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Util\Arrays;
use GDO\Core\GDT;
use GDO\Dog\Method\Disable;
use GDO\Core\GDT_Enum;
use GDO\UI\GDT_Confirm;
use GDO\CLI\CLI;
use GDO\Core\Expression\Parser;
use GDO\Core\Method;
use GDO\Core\GDO_Error;

abstract class DOG_Command extends MethodForm
{
	public static Parser $PARSER;
	
	public function isTrivial(): bool
	{
		return false;
	}
	
    ################
    ### Override ###
    ################
// 	public $group = null;
// 	public $trigger = null;
// 	public $priority = 50; # @TODO: make these into functions?
	
	public function isWebMethod() { return false; }
	public function isHiddenMethod() { return false; }
	public function isRoomMethod() { return true; }
	public function isPrivateMethod() { return true; }
	public function isAuthRequired() { return false; }
	public function isRegisterRequired() { return false; }
	
	##############
	### Helper ###
	##############
	public function getCLITriggerGroup(): string
	{
		$m = $this->getModule();
		return strtolower($m->getModuleName());
	}
	
	public function getCLITrigger()
	{
		$g = $this->getCLITriggerGroup();
		$t = strtolower($this->getMethodName());
		return "{$g}.{$t}";
	}
	
	public function getID() : ?string
	{
	    return $this->getCLITrigger();
	}
	
	##################
	### Config Bot ###
	##################
	
	public function getDefaultNickname() { return Module_Dog::instance()->cfgDefaultNickname(); }
	
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
	        return $var->gdoVar('confb_var');
	    }
	    return $this->getConfigGDTBot($key)->var;
	}
	
	public function getConfigValueBot($key)
	{
	    $gdt = $this->getConfigGDTBot($key);
	    return $gdt->getValue();
	}
	
	public function setConfigVarBot($key, $var)
	{
	    $gdt = $this->getConfigGDTBot($key)->var($var);
	    $value = $gdt->toVar($gdt->inputToVar($var));
	    return $this->setConfigValueBot($key, $value);
	}
	
	public function setConfigValueBot($key, $value)
	{
	    $gdt = $this->getConfigGDTBot($key);
	    if (!$gdt->validate($value))
	    {
	        return false;
	    }
	    DOG_ConfigBot::blank([
	        'confb_command' => $this->gdoClassName(),
	        'confb_key' => $key,
	        'confb_var' => $gdt->toVar($value),
	    ])->replace();
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
	        return $var->gdoVar('confu_var');
	    }
	    return $this->getConfigGDTUser($key)->var;
	}
	
	public function getConfigValueUser(DOG_User $user, $key)
	{
	    $gdt = $this->getConfigGDTUser($key);
	    return $gdt->getValue();
	}
	
	public function setConfigVarUser(DOG_User $user, $key, $var)
	{
	    $gdt = $this->getConfigGDTUser($key);
	    $value = $gdt->toValue($gdt->inputToVar($var));
	    return $this->setConfigValueUser($user, $key, $value);
	}
	
	public function setConfigValueUser(DOG_User $user, $key, $value)
	{
	    $gdt = $this->getConfigGDTUser($key);
	    if (!$gdt->validate($value))
	    {
	        return false;
	    }
	    DOG_ConfigUser::blank([
	        'confu_command' => $this->gdoClassName(),
	        'confu_key' => $key,
	        'confu_user' => $user->getID(),
	        'confu_var' => $gdt->toVar($value),
	    ])->replace();
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
	        return $var->gdoVar('confr_var');
	    }
	    return $this->getConfigGDTRoom($key)->var;
	}
	
	public function getConfigValueRoom(DOG_Room $room, $key)
	{
	    $gdt = $this->getConfigGDTRoom($key);
	    $var = $this->getConfigVarRoom($room, $key);
	    return $gdt->toValue($var);
	}
	
	public function setConfigVarRoom(DOG_Room $room, $key, $var)
	{
	    $gdt = $this->getConfigGDTRoom($key);
	    $value = $gdt->toValue($gdt->inputToVar($var));
	    return $this->setConfigValueRoom($room, $key, $value);
	}
	
	public function setConfigValueRoom(DOG_Room $room, $key, $value)
	{
	    $gdt = $this->getConfigGDTRoom($key);
	    if (!$gdt->validate($value))
	    {
	        return false;
	    }
	    DOG_ConfigRoom::blank([
	        'confr_command' => $this->gdoClassName(),
	        'confr_key' => $key,
	        'confr_room' => $room->getID(),
	        'confr_var' => $gdt->toVar($value),
	    ])->replace();
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
	        return $var->gdoVar('confs_var');
	    }
	    return $this->getConfigGDTServer($key)->var;
	}
	
	public function getConfigValueServer(DOG_Server $server, $key)
	{
	    $gdt = $this->getConfigGDTServer($key);
	    $var = $this->getConfigVarServer($server, $key);
	    return $gdt->toValue($var);
	}
	
	public function setConfigVarServer(DOG_Server $server, $key, $var)
	{
	    $gdt = $this->getConfigGDTServer($key);
	    $value = $gdt->toValue($gdt->inputToVar($var));
	    return $this->setConfigValueServer($server, $key, $value);
	}
	
	public function setConfigValueServer(DOG_Server $server, $key, $value)
	{
	    $gdt = $this->getConfigGDTServer($key);
	    if (!$gdt->validate($value))
	    {
	        return false;
	    }
	    DOG_ConfigServer::blank([
	        'confs_command' => $this->gdoClassName(),
	        'confs_key' => $key,
	        'confs_server' => $server->getID(),
	        'confs_var' => $gdt->toVar($value),
	    ])->replace();
	    return true;
	}
	
	##################
	### Repository ###
	##################
	
	/**
	 * @var Method[]
	 */
	public static $COMMANDS = [];
	
	/**
	 * @var Method[]
	 */
	public static $COMMANDS_T = []; # By trigger
	
	public static function register(Method $command)
	{
	    self::$COMMANDS[] = $command;
	    if ($t = $command->getCLITrigger())
	    {
    	    self::$COMMANDS_T[$t] = $command;
			Method::addCLIAlias($t, get_class($command));
	    }
	}
	
	public static function sortCommands()
	{
		uasort(self::$COMMANDS, function(Method $a, Method $b) {
			return $a->getModule()->priority - $b->getModule()->priority;
		});
		uasort(self::$COMMANDS_T, function(Method $a, Method $b) {
			return $a->getModule()->priority - $b->getModule()->priority;
        });
    }
	
	/**
	 * Get a command by trigger.
	 */
	public static function byTrigger(string $trigger, bool $throw=true): self
	{
		if (!isset(self::$COMMANDS_T[$trigger]))
		{
			if ($throw)
			{
				throw new GDO_Error('err_unknown_command');
			}
			return null;
		}
	    return self::$COMMANDS_T[$trigger];
	}
	
	/**
	 * Get supported connectors for this command.
	 * @return string[]
	 */
	public function getConnectors()
	{
	    return array_keys(DOG_Connector::connectors());
	}
	
	public function createForm(GDT_Form $form) : void
	{
        $form->addFields(...$this->gdoParameters());
        $form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
	    $args = [];
	    foreach ($this->gdoParameterCache() as $gdt)
	    {
	        $args[] = $gdt->getValue();
	    }
	    $message = new DOG_HTTPMessage();
	    $m = DOG_Message::$LAST_MESSAGE;
	    $message->user($m->user);
	    $message->server($m->server);
	    if (isset($m->room))
	    {
	    	$message->room($m->room);
	    }
	    $this->dogExecute($message, ...$args);
	    return $this->message("%s", [$message->getReply()]);
	}
	
	public function connectorMatches(DOG_Message $message)
	{
	    return in_array($message->server->getConnectorName(), $this->getConnectors(), true);
	}
	
	public function canExecute(DOG_Message $message)
	{
	    if ($this->isRegisterRequired() && (!$message->user->isRegistered())) { return false; }
	    if ($this->isAuthRequired() && (!$message->user->isAuthenticated())) { return false; }
	    if (!$this->connectorMatches($message)) { return false; }
	    if ( (!$this->isRoomMethod()) && ($message->room) ) { return false; }
	    if ( (!$this->isPrivateMethod()) && (!$message->room) ) { return false; }
	    if (!$this->hasUserPermission($message->getGDOUser())) { return false; }
	    if ( ($this->getPermission()) && ($message->user->isRegistered() && (!$message->user->isAuthenticated())) ) { return false; }
	    return true;
	}
	
	public function onDogExecute(DOG_Message $message)
	{
		GDT_Form::$CURRENT = $this->getForm();
	    
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

	    if ($this->isRegisterRequired() && (!$message->user->isRegistered()))
	    {
	        return $message->rply('err_register_first');
	    }
	    
	    if ($this->isAuthRequired() && (!$message->user->isAuthenticated()))
	    {
	        return $message->rply('err_authenticate_first');
	    }
	    
	    if ( ($this->getPermission()) && ($message->user->isRegistered() && (!$message->user->isAuthenticated())) )
	    {
	        return $message->rply('err_authenticate_first');
	    }
	    
	    if (!$this->hasPermission($message->getGDOUser()))
	    {
	        return $message->rply('err_permission_required');
	    }
	    
	    /**
	     * @var Disable $disable
	     */
	    $disable = Disable::instance();
	    if ($disable->isDisabled($message, $this))
	    {
	        return $message->rply('err_disabled');
	    }
	    
		$_REQUEST = [];
		
		# Clear
		CLI::getTopResponse();
		
		if (isset($message->room))
		{
// 			$trigger = $message->text[0];
// 			if ($trigger !== $message->room->getTrigger())
// 			{
// 				return true;
// 			}
    		$text = substr($message->text, 1);
		}
		else
		{
		    $text = $message->text;
		}
		
		
// 		$trigger = ltrim(Strings::substrTo($text, ' ', $text), '.');
// 		$text = trim(Strings::substrFrom($text, ' ', ''));
		
		# Generate button from trigger. Default submit
// 		$trigger = strtolower($trigger);
// 		$button = str_replace($trigger, '', $this->getCLITrigger());
// 		$button = $button ? $button : $this->getDefaultButtonLabel();
// 		$button = $this->getButtonByLabel($button);
// 		$_REQUEST[$this->getFormName()] = [$button => $button];
		
		if (!$text)
		{
		    if ($this->hasPositionalCLIParameters())
		    {
		    	$help = CLI::renderCLIHelp($this);
    		    return $message->reply($help);
		    }
		}

		try
		{
			$exp = self::$PARSER->parse($text);
			$result = $exp->execute();
			return $message->reply($result->render());
// 			$parameters = parseArgline($text, $this, true);
		}
		catch (\Throwable $ex)
		{
		    $message->rply('err_cli_exception', [
		    	$ex->getMessage(), $ex->getFile(), $ex->getLine()]);
		    return false;
		}
		
// 		if ($parameters === false)
// 		{
// 		    $errors = [];
// 		    foreach ($this->gdoParameterCache() as $gdt)
// 		    {
// 		        if ($gdt->hasError())
// 		        {
// 		            $errors[] = $gdt->getName() . ': ' . $gdt->renderError();
// 		        }
// 		    }
// 		    $message->rply('err_cli', [implode(' ', $errors)]);
// 		    return false;
// 		}
		
// 		$parameters = array_values($parameters);

// 		$this->dogExecute($message, ...$parameters);

		return true;
	}
	
	public function getParametersSorted()
	{
	    $parameters = $this->gdoParameterCache();
	    
	    # Sort them by type of param, positional or optionally.
	    uasort($parameters, function(GDT $a, GDT $b) {
	        $positionalA = $a->isPositional() ? 1 : 0;
	        $positionalB = $b->isPositional() ? 1 : 0;
	        return $positionalA - $positionalB;
	    });
	    
	    return $parameters;
	}
	
	public function getHelpText(DOG_Message $message)
	{
	    return $this->getDescription();
	}
	
	public function getUsageText(DOG_Message $message)
	{
	    $usage = [];
	    
	    foreach ($this->getParametersSorted() as $gdt)
	    {
	        if ((!$gdt->writeable) || (!$gdt->cli))
	        {
	            continue;
	        }
	        
	        $nameparam = '';
	        
	        $dots = ($gdt instanceof GDT_DogString) ? '...' : '';
	        
	        $positional = $gdt->notNull && ($gdt->initial === null);
	        
	        if (!$positional)
	        {
	            $nameparam = "--{$gdt->displayCLILabel()}=";
	        }
	        
	        $brk_open = $positional ? '<' : '[<';
	        $brk_close = $positional ? '>' : '>]';
	        
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
	            $name = $gdt->gdoHumanName();
	        }
	        $usage[] = $brk_open . $dots . $nameparam . $name . $dots . $brk_close;
	    }
	    return $message->t('usage', [
	        $this->getCLITrigger() . $this->getButtonChoice(), implode(' ', $usage)]);
	}
	
	private function getButtonChoice()
	{
	    $choices = [];
	    $submit = false;
	    $buttons = $this->getButtons();
	    foreach ($buttons as $button)
	    {
	        if ($button->name !== 'submit')
	        {
	            $choices[] = $button->displayCLILabel();
	        }
	        else
	        {
	            $submit = true;
	        }
	    }
	    
	    if (count($choices))
	    {
	        if ($submit)
	        {
	            array_unshift($choices, t('submit'));
	        }
	        
	        return sprintf('.[%s]', implode('|', $choices));
	    }
	}
	
	private function getDefaultButtonLabel()
	{
	    if ($form = $this->getForm())
	    {
    	    $buttons = $form->actions()->getAllFields();
    	    if ($button = array_shift($buttons))
    	    {
    	        return $button->renderLabel();
    	    }
	    }
	    return t('submit');
	}
	
	private function getButtonByLabel($label)
	{
	    if ($form = $this->getForm())
	    {
    	    foreach ($form->actions()->getAllFields() as $button)
    	    {
    	    	$myLabel = $button->renderLabel();
    	        if (strcasecmp($myLabel, $label) === 0)
    	        {
    	            return $button->getName();
    	        }
    	    }
	    }
	}
	
	private function hasPositionalCLIParameters(): bool
	{
		return Arrays::sum($this->gdoParameterCache(), function(GDT $gdt) {
			return $gdt->isPositional() * 1;
		}) > 0;
	}
	
}

DOG_Command::$PARSER = new Parser();
