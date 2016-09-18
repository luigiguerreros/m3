<?php
    use m3\cli;
    
    load_lib ( 'ar/singularplural' );

    $base_name = false;
    $fields = [];

    // Nombres básicos
    $CRUD = [
        'new' => 'nuevo', 
        'edit' => 'editar', 
        'delete' => 'borrar'
    ];

    // Sacamos los campos
    while ( $arg = array_shift ( $argv ) ) {
        // SI no tiene :, entonces es el nombre base
        $colon = strpos ( $arg, ':' );

        if ( $colon === false ) {
            // Solo si no hay basenam,e
            if ( !$base_name )
                $base_name = $arg;
            else {
                Console::fail ('Syntax: m3 new crud APPLICATION [base_name] param:type [param:type [...]]');
            }
        }
        else {
            $field = strtolower ( substr ( $arg, 0, $colon ) );
            $type = strtolower ( substr ( $arg, $colon + 1 ) );

            // Solo soportamos unos cuantos tipos
            if ( !in_array ( $type, ['string', 'integer', 'decimal', 'bool'] ) ) {
                Console::fail ( "'$type' not supported. Valid: string, integer, decimal, bool" );
            }
            $fields [ $field ] = $type;
        }
    }

    // Si no hay una base de datos, fallamos
    if ( ! isset( M3::$settings['databases'] ) ) {
        fail ( 'You need to setup the database in {green "databases"} section at {white config/settings.php}.' );
        exit;
    }

    // Let's CRUD!
    $base = $base_name?$base_name:$application;

    // El modelo siempre está en singular
    $model = m3\ar\singular ( $base );

    $model_upcase = ucfirst ( strtolower ( $model ));
    

    /* DB */
    $eq = [
        'string' => 'char:40',
        'integer' => 'integer',
        'decimal' => 'decimal:8,2',
        'bool' => 'bool'
    ];
    $db = "<?php return [\n";
    foreach ( $fields as $field => $type ) {
        $db .= "    '$field' => '{$eq[$type]}',\n";
    }
    $db .= "];";

    $file = "apps/$application/db/$base.php";
    file_save ( $db, $file );

    /* MODEL */
    $model_data = <<<EOF
<?php
namespace $application;
use m3, m3\\ar;

class $model extends ar\model {
}

EOF;
    $file = "apps/$application/models/$model.php";
    file_save ( $model_data, $file );

    /* FORM */
    $eq = [
        'string' => "'text'",
        'integer' => "'text',
        'regex' => m3\\forms\\REGEX_NUMBERS,
    ",
        'decimal' => "'text', 
        'regex' => m3\\formscREGEX_NUMBERS,
    ",
        'bool' => "'checkbox'",
    ];

    $form_data = "<?php return [\n";
    foreach ( $fields as $field => $type ) {
        $form_data .= "    '$field' => [{$eq[$type]}],\n";
    }
    $form_data .= "];";

    $file = "apps/$application/forms/$base.php";
    file_save ( $form_data, $file );
    
    /* 
     * CRUD: Create 
     */


    $app_title = ucfirst ( strtolower ( $base ) );
    
    $new_name = ucfirst ( strtolower ( $CRUD['new'] ));
    $edit_name = ucfirst ( strtolower ( $CRUD['edit'] ));
    $delete_name = ucfirst ( strtolower ( $CRUD['delete'] ));


    // Index es especial
    if ( $base_name ) {
        $index_controller = $base_name;
        $index_url = ':' . $base_name;
    }
    else {
        $index_controller = 'default';
        $index_url = ':';
    }


    foreach ( ['new', 'edit', 'delete'] as $method ) {
        $var_controller = "{$method}_controller";
        $var_url = "{$method}_url";

        if ( $base_name ) {
            $$var_controller = $base_name . '_' . $CRUD[$method];
            $$var_url = ':' . $base_name . '_' . $CRUD[$method];
        }
        else {
            $$var_controller = $CRUD[$method];
            $$var_url = ':' . $CRUD[$method];
        }
    }

    /* 
     * CRUD: Index
     */

    $create = "\$_$base = $model::all();\n";
    create_controller ( $application, $index_controller, $create );


    $view = <<<EOF
<h1>$app_title</h1>

<p>
<?=html\\link('$new_url', "New $model")?>
</p>

<table>
<?php foreach ( \$_$base as \$$model ): ?>
    <tr>

EOF;
    foreach ( $fields as $field => $type ) {
        $view .="    <td><?=\$$model->$field?></td>\n";
    }
    $view .= <<<EOF
    <td>
        <?=html\\link(['$edit_url', \$$model->pk()], '$edit_name' )?>
        <a href="javascript:post_confirm('This will delete this $model.', '<?=m3\\expand_url('$delete_url')?>', {id: <?=\$$model->pk()?>})">$delete_name</a>
    </td>
    </tr>
<?php endforeach ?>    
</table>

EOF;
    create_view ( $application, $index_controller, $view );

    /*
     * CRUD: New/edit
     */

    $edit_code = <<<EOF
    load_lib ( 'forms/form' );
    \$_frm = new forms\\form ();

    \$id = intval( M3::\$ARGS[0] );
    if ( \$id ) {
        \$_$model = $model::get ( \$id );
        if ( \$_$model->isempty() ) {
            m3\\error ( 'Nonexistent $model.' );
            m3\\redirect ( '$index_url' );
        }
    }
    else {
        \$_$model =  new $model();
    }

    if ( m3\\method ( 'post') ) {
        \$_frm->set ( \$_POST );
        if ( \$_frm->is_valid() ) {
            \$_$model->update ( \$_frm->get() );

            if ( \$_$model->isnew() ) {
                m3\\message ( 'New $model created.' );
            }
            else {
                m3\\message ( 'Registry updated.' );
            }
            m3\\redirect ( '$index_url' );
        }
        else {
            m3\\error ( 'There are errors in the form.');
        }
    }
    else {
        \$_frm->set ( \$_$model );
    }
EOF;
    create_controller ( $application, $edit_controller,  $edit_code );

    $edit_view = <<<EOF

<?php if ( \$_$model->isnew() ) :?>
    <h1>{$new_name} $model</h1>
<?php else: ?>
    <h1>{$edit_name} $model</h1>
<?php endif ?>

<form method="post">
<?=\$_frm->draw() ?>
<button type="submit">Submit form</button>
</form>
EOF;
    create_view  ( $application, $edit_controller, $edit_view );

    /*
     * CRUD: Delete
     */

    $delete_code = <<<EOF

if ( m3\method('post') ) {
    \$id = \$_POST['id'];
    $model::get ( \$id )->delete();
    m3\\warning ( "$model_upcase deleted." );
}

m3\\redirect ( '$index_url' );
EOF;
    create_controller ( $application, $delete_controller,  $delete_code );
