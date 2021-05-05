<?php
namespace GDO\Dog\Connector;

use GDO\Dog\DOG_Room;
use GDO\Dog\DOG_Server;
use GDO\Dog\DOG_Message;
use GDO\Dog\Dog;
use GDO\Dog\DOG_Connector;
use GDO\Dog\DOG_User;
use GDO\User\GDO_User;

/**
 * This connector can be called by shell: "GDO/Dog/bin/dog <command> [<..parameters..>]"
 * It is the default and a required connector.
 * You can dog add_server IRC irc://irc.freenode.net:6667 with it.
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 6.10.0
 */
class Bash extends DOG_Connector
{
    public static $INSTANCE;
    
    /**
     * @return DOG_Server
     */
    public static function instance() { return self::$INSTANCE; }
    
    public function init()
    {
        if (!self::$INSTANCE)
        {
            if (!(self::$INSTANCE = $this->getBashServer()))
            {
                self::$INSTANCE = DOG_Server::table()->blank([
                    'serv_connector' => $this->gdoShortName(),
                ])->insert();
                Dog::instance()->servers[] = self::$INSTANCE;
            }
        }
        return self::$INSTANCE;
    }
    
    /**
     * @return DOG_Server
     */
    private function getBashServer()
    {
        return DOG_Server::table()->select('*')->
            where("serv_connector='{$this->gdoShortName()}'")->
            first()->exec()->fetchObject();
    }
    
    public function getBashUser()
    {
        $user = DOG_User::getOrCreateUser(self::$INSTANCE, get_current_user());
        GDO_User::setCurrent($user->getGDOUser());
        return $user;
    }
    
    public function connect()
    {
        $this->connected = true;
        return true;
    }
    
    public function disconnect($reason)
    {
        echo "Disconnecting: {$reason}\n";
        $this->connected = false;
    }
    
    public function readMessage()
    {
        return false;
    }
    
    public function dog_cmdline(...$argv)
    {
        $text = implode(' ', $argv);
        $this->dog_cmdline2($text);
    }
    
    public function dog_cmdline2($text)
    {
        $msg = DOG_Message::make()->
            server(self::$INSTANCE)->
            user($this->getBashUser())->
            text($text);
        Dog::instance()->event('dog_message', $msg);
    }

    public function sendToUser(DOG_User $user, $text)
    {
        parent::sendToUser($user, $text);
        echo "$text\n";
    }
    
    public function sendToRoom(DOG_Room $room, $text)
    {
        parent::sendToRoom($room, $text);
        echo "$text\n";
    }
   
    public function sendNoticeToUser(DOG_User $user, $text)
    {
        parent::sendNoticeToUser($user, $text);
        echo "$text\n";
    }
    
}
