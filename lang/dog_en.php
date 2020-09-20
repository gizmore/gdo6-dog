<?php
return array(
'%s' => '%s',
'none' => 'none',
'usage' => 'Usage: #CMD#%s %s',
'err_connector' => 'Unknown connector %s. Known connectors: %s.',
'err_wrong_connector' => 'This command only works in %s.',
'err_not_in_private' => 'This command does not work in private.',
'err_not_in_room' => 'This command does not work in rooms.',
'dog_pong' => 'PONG!',
'err_dog_bruteforce' => 'Please wait %.01fs before you try again.',
'err_dog_error' => '%s: %s in %s line %s.',
'err_dog_exception' => '%s: %s',
######
'dog_help_add_server' => 'Add a new server to your #BOT# installation.',
'err_dog_server_already_added' => 'A server with this url or connector has already been added: %s',
'msg_dog_server_added' => 'Server %s has been added: %s.',
    
    
'dog_help_stats' => 'Show how much servers, rooms and users are online.',
'msg_dog_stats' => 'Currently i am online on %s servers, %s rooms and %s users.',
    
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

'dog_help_lang' => 'Show or change the user interface language.',
'msg_dog_show_language' => 'Your language is currently set to %s. Available languages: %s.',
'msg_dog_set_language' => 'Your language has been changed from %s to %s.',
    
'dog_help_trigger' => 'Change the char that triggers the bot in a channel.',
'msg_dog_trigger_changed' => 'The command trigger in %s has changed from %s to %s',
    
'dog_help_disable' => 'Disables a command in a room - or server wide when invoked in private.',
'msg_dog_already_disabled' => '%s is already disabled.',
'err_cannot_disable' => 'You cannot disable this command.',
'err_disabled' => 'This command is disabled here.',

'dog_help_enable' => 'Re-enables a command in a room - or server wide when invoked in private.',
'msg_dog_not_disabled' => '%s is not disabled in this scope.',
    
);
