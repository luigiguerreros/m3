<?php
use M3\Cli;

if (!defined ( "M3_BASE_INCLUDED")) {
    require 'm3/bin/base.php';    
}

bin::help('Creates a new form definition file.', 
    '[for] app_name form_name [control_name:control_type [...]]', [
        'for' => [
                    'optional' => true,
                    'description' => 'Syntactic sugar word.'
                ],
        'app_name' => "Name of the app where the new form will be created.",
        'form_name' => "New form name.",
        'control_name' => [
            'optional' => true,
            'description' => 'Adds a new control to the form definition.',
        ],
        'control_type' => [
            'optional' => true,
            'description' => "Type of control. Only specify the class name, not the\nFQCN.",
        ],
    ]);

// Debe existir el proyecto
if (!bin::$project_exists) {
    Console::fail ( "M3 project not found." );
}

// Si hay más parámetros, entonces lo usamos como nombres de campos
$fields = [];

foreach ( $argv as $d ) {
    $p = explode ( ':', $d );

    $field = $p[0];

    if ( isset ( $p[1]) ) {
        $type = ucfirst(strtolower ($p[1]));
    }
    else {
        $type = 'Text';
    }

    $fields [ $field ] = $type ;
}

// Creamos el formulario
$app = bin::$module->app;
$classname = bin::$module->element;
$form = <<<EOF
<?php
namespace {$app}\\forms;

use M3;
use M3\\Form;
use M3\\Form\\Control;
use M3\\Form\\Validator;

class $classname extends Form
{
EOF;

foreach ( $fields as $field => $type ) {
    $form .= <<<EOF
    var \$$field = [Control\\{$type}::class,
        // Extra options for $field
    
    ];

EOF;
}
$form .= '}';

$file = M3\Path\join('apps', bin::$module->app, 'forms', bin::$module->element .'.php');
cli\fileSave ($file, $form);