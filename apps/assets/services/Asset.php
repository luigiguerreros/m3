<?php
namespace assets\services;

use M3;

/**
 * Static class for various assets utilities.
 */
class Asset 
{
    /**
     * Build a single file name for all the assets, plus the controller name.
     */
    static public function buildUri(array $assets)
    {
        $uri = '';

        if (M3::$application) {
            $uri = M3::$application . '::';
        }

        $uri .= join(',', $assets);
        
        return $uri;
    }

    /**
     * Return all the files associated to an asset, from M3::$Args.
     *
     * @return array [application, asset_names]
     */
    public static function getNamesFromArgs()
    {
        if (!isset (M3::$args[0]) || trim(M3::$args[0]) == "") {
            Http\Response::serverError("You must specify at least one Javascript asset filename.");
        }

        $application = M3::$application;
        $names = explode (',', M3::$args[0]);

        $colon = strpos($names[0], '::');
        if ($colon !== false) {
            $application = substr($names[0], 0, $colon);
            $names[0] = substr($names[0], $colon + 2);
        }

        return [$application, $names];
    }
}
