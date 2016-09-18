<?php
namespace M3;

use M3;

class ExceptionHandler
{
    /**
     * Selects the proper method according the execution type
     */
    public static function handler($exception)
    {
        // El nombre del método es el tipo de ejecución
        $type = strtolower(M3::$execution_type);
        $callable = ['self', "handle$type"];

        call_user_func($callable, $exception);
    }

    public static function register()
    {
        // No registramos en CLI, por que eso se registra antes, 
        // y en otro momento
        if (M3::$execution_type != 'cli') {
            set_exception_handler ([__CLASS__, 'handler']);        
        }
    }

    /**
     * Shows a view with the exception informacion
     */
    public static function handleWeb($exception)
    {
        // Borramos cualquier salida, solo mostramos la excepción
        ob_clean();
        $view = View::render('m3_exception_handler', [
            'class' => get_class($exception),
            'E' => $exception,
        ]);

        $view->send();
    }

    /**
     * Returns an Ajax::EXCEPTION
     */
    public static function handleAjax($exception)
    {
        Ajax::message(Ajax::EXCEPTION, "boom");
    }
}
