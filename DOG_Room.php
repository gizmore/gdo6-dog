<?php
namespace GDO\Dog;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Secret;
use GDO\Core\GDT_Char;
use GDO\Language\GDT_Language;
use GDO\Language\GDO_Language;

/**
 * A chatroom.
 * @author gizmore
 */
class DOG_Room extends GDO
{
    /**
     * @var DOG_User[]
     */
    public $users = [];
    
    ###########
    ### GDO ###
    ###########
    public function gdoColumns() : array
    {
        return array(
            GDT_AutoInc::make('room_id'),
            GDT_Server::make('room_server')->notNull(),
            GDT_String::make('room_name')->notNull()->max(64),
            GDT_Secret::make('room_password')->max(64),
            GDT_Char::make('room_trigger')->length(1)->initial('$')->notNull(),
            GDT_String::make('room_description')->max(512),
            GDT_Language::make('room_lang')->notNull()->initial(GDO_LANGUAGE),
        );
    }
    
    ##############
    ### Getter ###
    ##############
    /**
     * @return DOG_Server
     */
    public function getServer() { return $this->gdoValue('room_server'); }
    public function getServerID() { return $this->gdoVar('room_server'); }
    
    /**
     * @return string
     */
    public function getName() { return  $this->gdoVar('room_name'); }
    public function getPassword() { return $this->gdoVar('room_password'); }
    public function getTrigger() { return  $this->gdoVar('room_trigger'); }
    
    /**
     * @return GDO_Language
     */
    public function getLanguage() { return $this->gdoValue('room_lang'); }
    public function getLanguageISO() { return $this->gdoVar('room_lang'); }
    
    ############
    ### Send ###
    ############
    public function send($text)
    {
        $this->getServer()->getConnector()->sendToRoom($this, $text);
    }
    
    ##############
    ### Static ###
    ##############
    /**
     * @param DOG_Server $server
     * @param string $roomName
     * @return self
     */
    public static function getByName(DOG_Server $server, $roomName)
    {
        if ($room = $server->getRoomByName($roomName))
        {
            return $room;
        }
        $name = GDO::quoteS($roomName);
        return self::table()->select()->where("room_server={$server->getID()} AND room_name={$name}")->first()->exec()->fetchObject();
    }
    
    public static function getOrCreate(DOG_Server $server, $roomName, $description=null)
    {
        if ($room = self::getByName($server, $roomName))
        {
            return $room->saveVar('room_description', $description);
        }
        return self::create($server, $roomName, $description);
    }
    
    public static function create(DOG_Server $server, $roomName, $description=null)
    {
        return self::blank(array(
            'room_server' => $server->getID(),
            'room_name' => $roomName,
            'room_description' => $description,
        ))->insert();
    }
    
    ##############
    ### Events ###
    ##############
    public function disconnect($text)
    {
        $this->users = [];
    }

    #############
    ### Users ###
    #############
    public function hasUser(DOG_User $user=null)
    {
        return $user ? isset($this->users[$user->getID()]) : false;
    }
    
    public function addUser(DOG_User $user)
    {
        $userId = $user->getID();
        if (!isset($this->users[$userId]))
        {
            $this->users[$userId] = $user;
        }
    }
    
    public function removeUser(DOG_User $user)
    {
        unset($this->users[$user->getID()]);
    }
    
}
