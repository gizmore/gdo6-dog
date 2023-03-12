<?php
namespace GDO\Dog\lang;
return [
	'none' => 'none',
	'usage' => 'Usage: #CMD#%s %s',
	
	#####
	'cfg_default_nickname' => 'Default nickname',
	
	#####
	'dog_user' => 'User',
	'dog_room' => 'Room',
	'dog_server' => 'Server',
	
	#####
	'err_connector' => 'Unknown connector %s. Known connectors: %s.',
	'err_wrong_connector' => 'This command only works in %s.',
	'err_not_in_private' => 'This command does not work in private.',
	'err_not_in_room' => 'This command does not work in rooms.',
	'err_dog_bruteforce' => 'Please wait %.01fs before you try again.',
	'err_dog_error' => '%s: %s in %s line %s.',
	'err_dog_exception' => '%s: %s',
	'err_not_same_server' => 'You have to be on the same server as %s.',
	'err_not_same_room' => 'You have to be in the same room as %s.',
	'err_not_online' => '%s is not online.',
	'err_username_ambigous' => 'This username is ambigous and matches %s.',
	'err_exact_username' => 'You have to submit the exact username %s.',
	'err_user_thyself' => 'You may not choose yourself.',
	'err_page' => 'There are only %s pages.',
	'err_no_data' => 'There is no data yet.',
	'err_authenticate_first' => 'You need to authenticate to execute this command.',
	'err_please_wait' => 'Please wait %ss before you try again.',
	'err_register_first' => 'Please register with #BOT# first to use this function.',
	
	######
	'perm_voice' => 'Voice',
	'perm_halfop' => 'Halfop',
	'perm_operator' => 'Operator',
	'perm_owner' => 'Owner',
	
	######
	'dog_help_ping' => 'Test if #BOT# responds.',
	'dog_pong' => 'PONG!',
	
	'dog_help_add_server' => 'Add a new server to your #BOT# installation.',
	'err_dog_server_already_added' => 'A server with this url or connector has already been added: %s',
	'msg_dog_server_added' => 'Server %s has been added: %s.',
	
	'dog_help_stats' => 'Show how much servers, rooms and users are online.',
	'msg_dog_stats' => 'Currently i am online on %s servers and %s rooms, seeing %s users including myself and some services.',
	
	'dog_help_mem' => 'Show performance statistics.',
	'dog_mem' => 'Loaded %s GDO files. Using %s memory / max %s. DB Queries read %s / write %s.',
	
	'dog_help_confb' => 'Configure global #BOT# settings.',
	'dog_help_confu^' => 'Configure your user settings with #BOT#.',
	'dog_help_confr' => 'Configure room settings for #BOT#.',
	'dog_help_confs' => 'Configure serverwide #BOT# settings.',
	'msg_dog_config_keys' => 'Available keys for %s: %s.',
	'msg_dog_config_key' => 'Current setting for %s: %s.',
	'msg_dog_config_set' => 'The %s value for %s has changed from %s to %s.',
	
	'dog_help_help' => 'Shows available commands or details for a command.',
	'msg_dog_overall_help' => 'Here you can execute the following commands; %s',
	'msg_dog_help' => '%s - %s',
	
	'mt_dog_lang' => 'Show or change the user interface language.',
	'msg_dog_show_language' => 'Your language is currently set to %s. Available languages: %s.',
	'msg_dog_set_language' => 'Your language has been changed from %s to %s.',
	
	'dog_help_trigger' => 'Change the char that triggers the bot in a channel.',
	'msg_dog_trigger_changed' => 'The command trigger in %s has changed from %s to %s',
	
	'dog_help_disable' => 'Disables a command in a room - or server wide when invoked in private.',
	'msg_dog_already_disabled' => '%s is already disabled.',
	'msg_dog_disabled' => 'The %s command has been disabled.',
	'err_cannot_disable' => 'You cannot disable this command.',
	'err_disabled' => 'This command is disabled here.',
	
	'dog_help_enable' => 'Re-enables a command in a room - or server wide when invoked in private.',
	'msg_dog_not_disabled' => '%s is not disabled in this scope.',
	'msg_dog_enabled' => 'The %s command has been enabled.',
	
	'mt_dog_ping' => 'Test if the bot responds.',
	'mt_dog_help' => 'Show commands or the help for a command',
	
	'mt_dogirc_join' => 'Join an IRC channel.',
	'msg_dog_room_lang' => 'The room\'s language is currently set to %s.',
	'msg_dog_room_lang_now' => 'The room\'s language changed from %s to %s.',
	
	'mt_dog_bot' => 'Show and toggle bot flag for a user.',
];
