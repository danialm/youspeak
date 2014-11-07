<?php

session_start();
if ( $_SESSION["settings"]["debug"]["showPhpErrors"] )
{
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

if ( isset($_SESSION["currentUserId"]) )
    unset($_SESSION["currentUserId"]);

if ( isset($_SESSION["sessionId"]) )
    unset($_SESSION["sessionId"]);

if ($_POST['mobile']) return;

header("location: ../index.php");
exit;

?>