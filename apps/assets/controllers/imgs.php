<?php
namespace assets;

use M3;
use M3\Path;
use M3\Http;

// Si no hay argumentos, 500!
if ( !isset ( M3::$args[0] ) || trim( M3::$args[0] ) == "" ) {
    throw new M3_HTTP_Exception ( 500, "You must specify at leaste one image asset filename.");
}

// Buscamos la imágen
$path = Path\join (M3::$args->get());

// Hay una app por defecto?
$colon = strpos ($path, ':' );
if ( $colon !== false ) {
    // Cambiamos el nombre de la app. No creo que haya problemas...
    M3::$APPLICATION = substr ( $path, 0, $colon );
    $path = ( substr ( $path, $colon + 1) );

}

$fp = new M3\Path\FileSearch($path, 'assets/imgs' );
$fp->ext = false;

// Si no existe, 404!
if ( $fp->notFound() ) {
    #m3\http_code(404, 'Not found');
    M3\Http\Response::notFound ( "Image asset file '$path' not found.", [
        'Search Path' => $fp->searched_paths,
    ]);
}

// TODO: Crear una verificación usando el etag y last modified
$response = Http\Response::fromFile($fp->get());

$response->send();

# Finalizamos la ejecución
exit;