<?php
$titulo = "<strong>$class</strong> on <tt>" .
            $E->getFile() . ':' . $E->getLine() . '</tt>';
?>
<!DOCTYPE html>
<html>
<head>  
    <style>
        body {font-family:sans-serif; font-size:13px;}
        div.excepcion {font-size: 18px; background: #EEF; padding: 10px;}
        div.titulo {font-size: 24px; margin-bottom: 10px;}
        div.mensaje {font-size: 14px; font-family: monospace;}
        li.traza {margin-bottom: 5px; margin-left:20px;font-family:monospace; padding: 5px; cursor: pointer; overflow: auto}
        div.traza:hover {background: #F8F8F8;}
        div.traza_fichero {font-weight:bold;}
        div.traza_lineas {display: none; border: 1px solid #EEE; padding: 5px; margin: 10px; overflow: hidden;}
        span.traza_numero {background: #BBB;}
        span.traza_resaltada {background: #EEE; font-weight: bold; color: #F00;}
        table.exportados {font-family: monospace; border-collapse: collapse}
        table.exportados .variable { font-weight: bold;}
        table.exportados td {vertical-align: top; border: 1px solid #EEE; padding: 5px;}
        table.exportados tr:hover {background-color: #EEF;}
    </style>
    <title><?=strip_tags ($titulo)?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
</head>    
<body>
    <script type="text/javascript">
    function cambiar ( id ) {
        e = document.getElementById( id )
        if ( e.style.display != 'block') 
            e.style.display = 'block'
        else
            e.style.display = 'none'
    }
    </script>

    <div class="excepcion">
    <div class="titulo"><?=$titulo?></div>
    <div class="mensaje"><?=$E->getmessage();?></div>
    </div>
    
    <h3>Trace:</h3>
<?php
        $trace = $E->getTrace();
        
        // En la traza, metemos el fichero original
        array_unshift ( $trace, [
            'file' => $E->getFile(),
            'line' => $E->getLine(),
        ]);
        
        echo "<ol>";
        foreach ( $trace as $id => $t ) {

            // leemos algunas líneas del fichero.
            echo '<li class="traza" onclick="cambiar(\'tl_' . $id . '\')">';
            if ( isset ( $t['file'] ) ) { 
                $lines = file ( $t['file'] );
                $de = $t['line'] - 4;
                $al = $t['line'] + 2;
                
                $padding = strlen ( $al );

                if ( $de < 0 )
                    $de = 0;
                    
                if ( $al >= count ( $lines ) )
                    $al = count ( $lines ) - 1;
            
                echo '<div class="traza_fichero">' . $t['file'] . ':' . $t['line'] . '</div>';
            }
            
            // Si no hay una función, no imprimimos
            if ( isset ($t['function'] ) ) {
            
                // Revisamos los argumentos, por si hay uno que no sea string
                $args = [];
                if ( isset ( $t['args'] ) ) foreach ( $t['args'] as $arg ) {
                    if ( is_string ( $arg ))
                        $args[] = $arg;
                    else {
                        $type = gettype ( $arg );
                        switch ( $type ) {
                            case 'object':
                                $args[] = 'object:' . get_class ( $arg );
                                break;
                            case 'array':
                                $args[] = 'array:' . count ( $arg );
                                break;
                            default:
                            $args[] = $type;
                        }
                    }
                }

                $args = join (', ', $args);
            
                echo '<div class="traza_funcion">' . $t['function'] . ' ( ' . $args . ' )</div>';
            }
            echo "\n";

            if ( isset ( $t['file']) ) {
                echo '<div class="traza_lineas" id="tl_' . $id . '">';
                for ( $l = $de; $l <= $al; $l++ ) {
                    $linea = $l + 1;            

                    echo '<div class="traza_linea">';
                    
                    echo '<span class="traza_numero">&nbsp;' . 
                        str_pad ( $linea, $padding, ' ', STR_PAD_LEFT) . '&nbsp;';
                    echo '</span> ';

                    // Clase para iluminar la línea en cuestión
                    $class = "";
                    if ( $linea == $t['line'] ) {
                        $class = "class='traza_resaltada'";
                    }
                    
                    echo "<span $class>";
                    echo  
                        strtr ( htmlentities ( $lines [ $l ] ), [' ' => '&nbsp;'] );
                        
                    echo "</span>";
                    echo "</div>";
                }
                echo "</div>";
            }
            echo "</li>\n"; //traza;
        }
        echo "</ol>";
        
        // Si existe datos exportados de la excepción, los mostramos
        if ( isset ( $E->__EXPORTED_DATA ) && $E->__EXPORTED_DATA ):
?>
        <h3>Extra data:</h3>
        <table class="exportados">
        <?php foreach ( $E->__EXPORTED_DATA as $variable => $value ): ?>
        <tr>
        <td class="variable"><?=$variable?>
        </td>
        <td class="valor">

<?php
    $filter = function ($string) {
        return strtr ( htmlentities ( $string ), [' ' => '&nbsp;']);
    };

    if ( is_array ( $value ) ) {
        foreach ( $value as $id => $val ) {
            echo "$id => " . $filter($val) . "<br />";
        }
    }
    else {
        echo $filter ( $value ) . "<br />";
    }
?>
        </td>
        </tr>
        <?php endforeach; ?>
        </table>

<?php
        endif;
?>

    </body>
</html>