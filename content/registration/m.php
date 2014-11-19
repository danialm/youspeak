<?php

global $errorMsg;

global $institutions;

global $firstname;
global $lastname;
global $email;
global $username;
global $inst;
global $major;
global $gpa;
global $schoolYear;
global $sex;
global $age;
global $race;
global $haveDisa;
global $disability;

$firstname  = "";
$lastname   = "";
$email      = "";
$username   = "";
$inst       = "0";
$major      = "";
$gpa        = "";
$schoolYear = "0";
$sex        = "";
$age        = "";
$race       = "";
$haveDisa   = "";
$disability = "";


$errorMsg = "";

if ( isset($_SESSION["formError"]) )
{
    $errorMsg = 
        isset($_SESSION['formError']['msg'])
        ? $_SESSION['formError']['msg']
        : "";
    
    $firstname  = $_SESSION['formError']['firstname'];
    $lastname   = $_SESSION['formError']['lastname'];
    $email      = $_SESSION['formError']['email'];
    $username   = $_SESSION['formError']['username'];
    $inst       = $_SESSION['formError']["institute"];
    $major      = $_SESSION['formError']["major"];
    $gpa        = $_SESSION['formError']["gpa"];
    $schoolYear = $_SESSION['formError']["schoolYear"];
    $sex        = $_SESSION['formError']["sex"];
    $age        = $_SESSION['formError']["age"];
    $race       = $_SESSION['formError']["race"];
    $haveDisa   = $_SESSION['formError']["haveDisa"];
    $disability = $_SESSION['formError']["disability"];
}

Dbase::Connect();
$institutions = Dbase::GetInstitutions();
$user = Dbase::GetUserInfo($_SESSION['newUserId']);
Dbase::Disconnect();

?>