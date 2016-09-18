<?php
namespace M3;

/**
 * Extended exception. Allows to save an array, for debug purposes.
 */
class Exception extends \Exception 
{
    public $__EXPORTED_DATA = [];

    public function __construct($message, $exported = []) {
        $this->__EXPORTED_DATA = $exported;
        parent::__construct  ($message, 0, null);
    }    
}

// Creamos Exceptiones
class RouteException extends Exception {}
class ControllerNotFound extends Exception {}
class AppRegisterException extends Exception {}
