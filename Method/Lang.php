<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Language\GDT_Language;
use GDO\Dog\DOG_Message;
use GDO\Language\GDO_Language;
use GDO\Util\Arrays;

final class Lang extends DOG_Command
{
//     public $group = 'Config';
    public $trigger = 'lang';
    
    public function gdoParameters()
    {
        return array(
            GDT_Language::make('language'),
        );
    }
    
    public function dogExecute(DOG_Message $message, GDO_Language $language=null)
    {
        $gdo_user = $message->getGDOUser();
        $current = $gdo_user->getLangISO();
        if (!$language)
        {
            $available = array_map(function(GDO_Language $lang){
                return $lang->getISO();
            }, GDO_Language::table()->allSupported());
            $message->rply('msg_dog_show_language', [$current, Arrays::implodeHuman($available)]);
        }
        else
        {
            $gdo_user->saveVar('user_language', $language->getISO());
            $message->rply('msg_dog_set_language', [$current, $language->getISO()]);
        }
    }
    
}
