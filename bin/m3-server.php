<?php

use M3\Cli;
use M3\Console;

if (!defined ( "M3_BASE_INCLUDED")) {
    require 'm3/bin/base.php';    
}

bin::help("Starts a simple web server on the project.", 
    '[host[:port]]',
    [
    'host[:port]' => [
        'optional' => true,
        'description' => 'Starts the server on the specified host and port. Default is localhost:8888',
    ],
]);

if (!bin::$project_exists) {
    Console::fail ( 'M3 project directory not found or not specified.' ); 
    exit (1);
}

$host = 'localhost';
$port = '8888'; 

$serverinfo = array_shift($argv);
if ($serverinfo) {
    $parts = explode(':', $serverinfo);
    
    if (isset($parts[0])) {
        $host = $parts[0];
    }
    if (isset($parts[1])) {
        $port = $parts[1];
    }
}

// Primero probamos si la carpeta pública está en el proyecto.
$root_path = bin::$project_path;
$index = 'index.php';
$full_path = M3\Path\join($root_path, $index);

if (!file_exists($full_path)) {
    // No existe. La raiz debe estar en otro lado
    if (!isset(M3::$settings['webroot_dir'])) {
        Console::fail("I can't find the web root directory. Check the project config");
    }

    $root_path = M3::$settings['webroot_dir'];
}

Console::write("Launching development server for {:project ".bin::$project_name."} project on {white http://{$host}:{$port}/}");

chdir($root_path);
$cmdline = "php -S {$host}:{$port} {$index}";
passthru ($cmdline);
