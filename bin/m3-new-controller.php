<?php
use M3\Cli;
use M3\Console;

// Creamos un controlador
Cli\createController (bin::$project_path, bin::$module->app, bin::$module->element);

// Creamos una vista, a menos que no querramos
if ( !isset ( bin::$args['noview'] ) ) {
    $APPLICATION = bin::$module->app;
    $ELEMENT_NAME = bin::$module->element;

    $content = <<<EOF
<!-- Delete this and the lines below, and write your own view. -->

<h1>Hi! I'm the <em>$ELEMENT_NAME</em> controller from <em>$APPLICATION</em> application!</h1>
<p>Now, edit the view file <strong>apps/$APPLICATION/views/$ELEMENT_NAME.php</strong> for 
changing this text, or the file <strong>apps/$APPLICATION/controllers/$ELEMENT_NAME.php</strong>
for changing the controller behavior.</p>

EOF;

    Cli\createView(bin::$project_path, bin::$module->app, bin::$module->element , $content);

}
