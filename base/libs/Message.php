<?php
namespace M3;

use M3;

class Message
{
    private static function addMessage($message, $type = 'message')
    {
        $m = M3::$session->messages;
        $m[] = [
            'type' => $type,
            'message' => $message,
        ];
        
        M3::$session->messages = $m;
    }

    public static function message($message)
    {
        self::addMessage($message, 'message');
    }

    public static function warning($message)
    {
        self::addMessage($message, 'warning');
    }

    public static function error($message)
    {
        self::addMessage($message, 'error');
    }
}