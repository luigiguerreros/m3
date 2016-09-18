<?php
use M3\Cli;
use M3\Console;

// Si ya existe la app, fallamos
if ( bin::$module->app_exists ) {
    Console::fail ("Application '{:app " . bin::$module->app . "}' already exist.");
}

// Hay algunos nombres prohibidos
if (in_array(bin::$module->app, ['static', 'm3', 'for'])) {
    Console::fail("'" . bin::$module->app . "' is a invalid name for an app.");
}

// Hay un controlador que hay que crear, en vez de default?
$controller = array_shift ($argv);
if ( !$controller ) {
    $controller = 'default';
}

// Creamos la nueva estructura
if ( isset ( bin::$args['bare'] ))  {
    Console::write ( "Creating bare new app {:app " . bin::$module->app . "}...");
}
else {
    Console::write ( "Creating new app {:app " . bin::$module->app . "}...");
    Cli\makeTree(bin::$project_path, [
        "apps/" . bin::$module->app => [
            'controllers', 
            'models', 
            'forms',
            'services',
            'db',
            'assets' => [
                'css',
                'js',
                'imgs'
            ],
            'views' => [
                'layouts'
            ],
        ]
    ]);

}

// oh, php...
$APPLICATION = bin::$module->app;

// Creamos una vista, a menos que no querramos
$content = '';
if (!isset(bin::$args['noview'])) {

    $content = <<<EOF
<!-- Delete this and the lines below, and write your own view. -->

<h1>Hi! I'm the <em>$controller</em> controller from <em>$APPLICATION</em> application!</h1>
<p>Now, edit the view file <strong>apps/$APPLICATION/views/default.php</strong> for 
changing this text, or the file <strong>apps/$APPLICATION/controllers/default.php</strong>
for changing the controller behavior.</p>

EOF;
}

Cli\createController (bin::$project_path, bin::$module->app, $controller);
Cli\createView (bin::$project_path, $APPLICATION, $controller, $content);
