<?php

include "lib.php";
//require_once "Unirest.php"; A light weight library for http requests!

define("DEFAULT_PAGE", "Login");

define("WEB_LOC", getenv("WEB_LOC"));
define("PHYS_LOC",getenv("PHYS_LOC"));
define("LIBRARY",PHYS_LOC."library/");
define("CONTENT",PHYS_LOC."content/");
define("PARTS_LOC",CONTENT."global/");

define("CSS","css");
define("JAVASCRIPT","js");
define("IMAGES","images");

//error_reporting(E_ALL ^ E_DEPRECATED);

session_start();

$_SESSION["settings"] = parse_ini_file(getenv("INI_LOC"),true);

if ($_SESSION['settings']['flags']['showPhpErrors'])
    ini_set("display_errors",1);
    
loadLibrary();

if ( isset($_SESSION['settings']['flags']['rewrite']) &&
        $_SESSION['settings']['flags']['rewrite'] )
    define("NO_REWRITE",false);
else
    define("NO_REWRITE",true);


/*PageLocker::set("03E01FD46AC");
Page::setPageNotFound("NotFound");*/

$template = new Template
(
    new Part("header.php"),
    new Part("footer.php"),
    new Part("mobhead.php"),
    new Part("mobfoot.php")
);

$db = new Part("dbase.php");
//$ga = new Part("gmailauth.php");
$pre = new Part("prerender.php");
        
new Page("About",        $template);
new Page("Classroom",    $template);
new Page("Courses",      $template);
new Page("Login",        $template);
new Page("Profile",      $template);
//new Page("Registration", $template); Gmail Authentication, we do not need registration anymore

new Page("Error",false,PARTS_LOC,"error.html");
new Page("NotFound",false,PARTS_LOC,"nopage.html");

$uri = getURI();
define("URI",$uri);

try
    {
        $db->run();
        $pre->run();
        //$ga->run();
        Page::prepareAndRender(URI);
    }
    catch (Exception $e)
    {
        echo "<h3>".$e->getMessage()."</h3>";
        echo "<pre>".$e->getTraceAsString()."</pre>";
    }

?>