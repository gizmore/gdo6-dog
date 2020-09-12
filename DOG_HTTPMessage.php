<?php
namespace GDO\Dog;

final class DOG_HTTPMessage extends DOG_Message
{
    private $reply;
    
    public function getReply()
    {
        return $this->reply;
    }
    
    public function reply($text)
    {
        $this->reply = $text;
    }
    
}
