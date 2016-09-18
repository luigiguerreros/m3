<?php

/**
 * Carga una o varias librerías. 
 *
 * @uses new M3\Path\FileSearch() para recabar la ruta de las librerías.
 */
function load_lib () {

    foreach ( func_get_args() as $lib ) {
        // Le quitamos los puntos y espacios alrededor
        $lib = trim ( $lib, " ." );

        $fp = new M3\Path\FileSearch($lib, 'libs');
        if ( $fp->found() ) {
            require_once $fp->get();
        }
        else {
            throw new m3\NotFoundException ( "Library '$lib' not found.", [
                'Search paths' => $fp->searched_paths,
            ]);
        }
    }
}

/**
 * Devuelve la ruta de un fichero relativo a la raiz del proyecto
 */
function project_file ( $file ) {
    return $file;
}

/**
 * Función que se usa en spl_autload_register() para cargar los modelos
 * de las aplicaciones.
 *
 * Todos las demás funciones de autoload deben ser PREPENDidas!
 */
/*function class_autoloader( $class ) {
    // Dividimos la clase en partes
    $parts = explode ('\\', $class );

    $search_paths = [];

    // Las clases que empiezan con M3 las sacamos del m3\base\lib
    if ( strtolower($parts[0]) == "m3") {
        
        $search_paths[] = [M3\BASE_PATH, 'base/libs'];
        unset ( $parts[0] );
    }
    else {
        // Buscamos en la app, luego en base
        $search_paths = [
            [M3\PROJECT_PATH, 'apps'],
            [M3\PROJECT_PATH, 'base'],
            [M3\PROJECT_PATH, 'vendor'],

            [M3\BASE_PATH, 'apps'],
            [M3\BASE_PATH, 'vendor'],

        ];
        
    }
    foreach ( $search_paths as $base_path ) {
        $path = M3\Path\join ($base_path, $parts) . '.php' ;

        // Existe?
        if (file_exists($path)) {

            require_once $path;
            return true;
        }
    }
}
*/