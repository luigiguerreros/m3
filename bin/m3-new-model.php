<?php
use M3\Cli;

if (!defined ( "M3_BASE_INCLUDED")) {
    require 'm3/bin/base.php';    
}
bin::help("Creates a model definitio.", 
    "new model [for] app_name model_name", 
    [
        'app_name' => 'Target app',
        'model_name' => 'Model name. It should match a tabledef.'
    ]
);


// Debe existir el proyecto
if (!bin::$project_exists) {
    Console::fail ( "M3 project not found." );
}

$namespace = bin::$module->app;
$classname = bin::$module->element;

Cli\createModel(bin::$project_path, $namespace, $classname);