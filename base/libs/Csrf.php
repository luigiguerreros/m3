<?php
namespace M3;

use M3;

class Csrf implements CsrfInterface
{
    /** Generated CSRF token */
    static $token = null;

    public function generateToken()
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
            'abcdefghijklmnopqrstuvwxyz' .
            '0123456789';
        $lc = strlen($letters) - 1; 
        $token = '';
        for ($i = 0; $i < 48; $i++) { 
            $token .= $letters{rand(0, $lc)};
        }

        static::$token = $token;
        M3::$security_token = $token;
        M3::$session->security_token = $token;
        return $token;
    }

    public function getToken()
    {
        if (is_null(static::$token)) {
            return $this->generateToken();
        } else {
            return static::$token;
        }
    }

    public function getSavedToken()
    {
        // Debería de existir...
        return M3::$session->security_token;
    }
}
