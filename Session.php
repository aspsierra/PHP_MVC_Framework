<?php

namespace aspsierra\phpBasicFw\core;

/**
 * handle sessions
 */
class Session
{
    //defined session variables 
    protected const FLASH_KEY = 'flash_messages';

    public function __construct(){
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$message){
            $message['remove'] = true; 
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
        //var_dump($_SESSION[self::FLASH_KEY]);
    }

    /**
     * define a temporal mmessage
     * @param   string  $key      identifier for the message
     * @param   string  $message  message itself
     */
    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'value' => $message,
            'remove' => false
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key, $value){
        $_SESSION[$key] = $value;
    }

    public function get($key){
        return $_SESSION[$key] ?? false;
    }

    public function remove($key){
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$message){
            if($message['remove'] == true){           
                unset($flashMessages[$key]);
            }
        }
        
        $_SESSION[self::FLASH_KEY] = $flashMessages;

    }
}
