<?php
use M3\Cli;
use M3\Console;
use M3\Database\Fields;

if (!defined('M3_BASE_INCLUDED')) {
    require 'm3/bin/base.php';
}
bin::help("Creates a empty database table definition.", 
    "new tabledef [for] app_name", 
    [
        'app_name' => 'Target app'
    ]
);

// Debe existir el proyecto
if (!bin::$project_exists ) {
    Console::fail ( "M3 project not found." );
}

// Debe existir el proyecto
if (!bin::$module->app ) {
    Console::fail ( "You must specify the app for the new dbdef." );
}

$baseapp = M3\Path\join(bin::$project_path, 'db');

// AISH PHP... Sacamos todos los tipos de campos en minúscula...
$lcase = [];
$constants = (new \ReflectionClass('M3\\Database\\Fields'))->getConstants();
foreach ($constants as $name => $c) {
    $lcase[strtolower($name)] = $name;
}

// Sacamos los campos de los argumentos, si hay
$fielddef = '   // Write here the field definitions for ' . bin::$module->element;

$fielddefs = []; 
foreach ($argv as $arg) {
    if (strpos($arg, ':') === false) {
        Console::fail('Format of parameters are "name:type".');
    }

    list($name, $type) = explode(':', $arg, 2);

    $type = $lcase[strtolower($type)];
    
    if (!defined("M3\\Database\\Fields::$type")) {
        Console::fail("'$type' is an invalid data base field.");
    }
    $def  = "    public \$$name = [Fields::$type,\n";
    $def .= "        // Options for field '$name'\n";
    $def .= "\n";
    $def .= "    ];";

    $fielddefs[] = $def;
}

if ($fielddefs) {
    $fielddef = join("\n\n", $fielddefs);
}


$namespace = bin::$module->app;
$classname = bin::$module->element;

$file = <<<EOF
<?php
namespace $namespace\\db;

use M3\Database\Tabledef;
use M3\Database\Fields;

class $classname extends Tabledef
{
    // Uncomment this static property to force the table name
    /*
    static \$table_name = 'a_nice_table_name';
    /**/
$fielddef
}
EOF;

Cli\fileSave (bin::$module->element . '.php', $file, [
    'base_path' => [bin::$project_path, 'apps', bin::$module->app,  'db'],
]);

// También creamos su modelo
Cli\createModel(bin::$project_path, $namespace, $classname);