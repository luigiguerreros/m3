<?php

/**
 * Holding class for various App-level information
 */
class M3
{
    /** Base URL for this project */
    static public $base_url;

    /** Session variables */
    static public $session;

    /** M3\Request object */
    static public $request;

    /** Execution type. Default is 'web'. */
    static public $execution_type = 'web';

    /** Active application name */
    static public $application;

    /** Active controller name */
    static public $controller;

    /** M3\View object, containing the main view */
    static public $view;

    /** Arguments passed to the controller */
    static public $args;
    
    /** Settings array, from config/settings.php */
    static public $settings;

    /** Holder for the active user, if this project uses that.*/
    static public $active_user;

    /** Debug state */
    static public $debug = false;

    /** POST security token, for preventing CSRF */
    static public $security_token;
    
    /**
     * Initialize the application
     */
    static public function init() {
        // Cargamos la configuración de la aplicación
        self::$settings = require M3\PROJECT_PATH . '/config/settings.php';

        // Cargamos la configuración seguín este ambiente de trabajo
        $target = M3\PROJECT_PATH . '/config/settings.' . M3\ENVIRONMENT . '.php';

        if (file_exists($target)) {
            $more_settings = require $target;
            self::$settings = array_replace_recursive (self::$settings,
                $more_settings);
        }

        // Copiamos el estado de debug de la configuracion
        self::$debug = self::$settings['debug_mode'];

        // Venimos por el cli?
        if (PHP_SAPI == 'cli') {
            self::$execution_type = 'cli';
        }

        // Seteamos la zona horaria
        if (isset(M3::$settings['time_zone'])) {
            date_default_timezone_set(M3::$settings['time_zone']);
        }

        if (isset(M3::$settings['locale'])) {
            setlocale(LC_ALL, M3::$settings['locale']);
        }

        // No estoy seguro si esto es bueno que esté hardcoded...
        setlocale(LC_NUMERIC, 'C');

        // Todo el encoding será utf-8
        mb_internal_encoding('utf-8');

        // Creamos el placeholder para los argumentos
        self::$args = new M3\Args;

        // Y creamos el request
        if (PHP_SAPI != 'cli') {
            self::$request = M3\Http\Request::fromSuperGlobals();
        }

        // Sesión
        self::$session = new M3\Session;

        // Obtenemos la ruta web de este proyecto. Usamos
        // la ruta web de index.php para sacar la raiz.
        $base_url = dirname($_SERVER['SCRIPT_NAME']);

        // Debe acabar en un /
        $base_url .= (substr($base_url, -1) != '/'?'/':'');

        self::$base_url = $base_url;
    }
}
