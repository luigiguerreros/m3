<?php
use M3\Cli;

// Debe existir la app

if ( ! bin::$module->app_exists ) {
    Console::fail ( "Application '{:app " . bin::$new__app . "}' doesn't exists. " );

}
// Creamos una vista. Simple.
Cli\createView (
    bin::$project_path, 
    bin::$module->app, 
    bin::$module->element
);
