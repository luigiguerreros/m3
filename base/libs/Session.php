<?php
namespace M3;

use M3;

/**
 * Handle session variables for this project
 */
class Session {
    
    /**
     * Session token, used for distinguish this app from another running on
     * this same host.
     */
    public $token = '';

    function __construct()
    {

        // Usamos el ID de la sesión para generar un código de sesión
        $this->token = hash_hmac ( 'sha256', 
            session_id(),
            M3::$settings['APPID']);

        // Enviamos el token como cookie al usuario
        /*if ( M3::$EXECUTION_TYPE != 'CLI') {
            setcookie("m3-session-token", $this->token, 0, M3::$base_url);
        }*/
        
    }

    /**
     * "Getter"
     */
    function __get($var) 
    {
        return ifset($_SESSION[$this->token][$var]);
    }

    /**
     * "Setter"
     */
    function __set($var, $val)
    {
        $_SESSION[$this->token][$var] = $val;
    }

    /**
     * "Unsetter"
     */
    function __unset ($var)
    {
        unset ( $_SESSION[$this->token][$var] );
    }
}
