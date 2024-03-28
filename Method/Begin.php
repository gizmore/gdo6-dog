<?php
namespace GDO\Dog\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Dog\GDT_RestOfText;

final class Begin extends DOG_Command
{

    public static array $RECORDINGS = [];


    public function getCLITrigger(): string
    {
        return 'begin';
    }


    public function gdoParameters(): array
    {
        return [
            GDT_RestOfText::make('text'),
        ];
    }

    private static function getRecordingID(DOG_Message $message): string
    {
        $rid = $message->room?$message->room->getID():'0';
        $uid = $message->user->getID();
        return "{$uid}:{$rid}";
    }

    private static function getRecording(DOG_Message $message): ?string
    {
        $id = self::getRecordingID($message);
        if (!isset(self::$RECORDINGS[$id]))
        {
            self::$RECORDINGS[$id] = '';
        }
        return self::$RECORDINGS[$id];
    }

    private static function appendRecording(DOG_Message $message, string $append): void
    {
        self::getRecording($message);
        $id = self::getRecordingID($message);
        self::$RECORDINGS[$id] .= "{$append} ";
    }

    private static function isRecording(DOG_Message $message): bool
    {
        $id = self::getRecordingID($message);
        return isset(self::$RECORDINGS[$id]);
    }

    private static function stopRecording(DOG_Message $message): void
    {
        $id = self::getRecordingID($message);
        unset(self::$RECORDINGS[$id]);
    }

    public function dogExecute(DOG_Message $message, $text): GDT
    {
        $this->append($message, $text);
        return GDT_Response::make();
    }

    private function append(DOG_Message $message, string $text): void
    {
        $exec = false;
        $end = strpos($text, '$end');
        if ($end !== false)
        {
            $text = substr($text, 0, -4);
            $exec = true;
        }
        self::appendRecording($message, $text);
        if ($exec)
        {
            $msg2 = new DOG_Message(false);
            $msg2->text(trim(self::getRecording($message)));
            $msg2->room($message->room);
            $msg2->user($message->user);
            $msg2->server($message->server);
            $message->server->enqueue($msg2);
            self::stopRecording($message);
        }
    }

    public function dog_message(DOG_Message $message): void
    {
        if (self::isRecording($message))
        {
            $text = $message->text;
            $this->append($message, $text);
        }
    }

}
