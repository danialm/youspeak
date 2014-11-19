<?php

function loadLibrary ()
{
    if ( !defined("LIBRARY") )
        throw new Exception("lib: Trying to load the library. The LIBRARY constant needs to point to a library path.");
    
    require_once LIBRARY.'google-api-php-client-master/autoload.php';
    
    $dir = opendir(LIBRARY);

    if ($dir)
        while ( ($file = readdir($dir)) !== false )
            if ( substr($file,-3) == "php" )
                include LIBRARY."$file";

    closedir($dir);
}

function getURI ()
{
    $uri;

    if (!NO_REWRITE)
    {
        $uri = $_SERVER["REQUEST_URI"];

        if ($uri == "/") $uri = DEFAULT_PAGE;
        else $uri = substr($uri,1);
    }
    else
    {
        if ( isset($_GET['p']) ) $uri = $_GET['p'];
        else $uri = DEFAULT_PAGE;
    }

    return $uri;
}

?>