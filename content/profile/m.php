<?php

global $errorMsg;
global $instructor;
global $institutions;
global $firstname;
global $lastname;
global $studentid;
global $email;
global $student;
global $major;
global $gpa;
global $inst;
global $schoolYear;
global $sex;
global $age;
global $race;
global $haveDisa;
global $disability;
global $updatedFields;
global $allfields;
global $assessor;

$id = isset($_SESSION['currentUserId']) && $_SESSION['currentUserId'] ? $_SESSION['currentUserId'] : $_SESSION['newUserId'];

Dbase::Connect();
$user = Dbase::GetUserInfo($id);
$instructor = isset($_SESSION['isInstructor']) && $_SESSION['isInstructor']===true;
$assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor']===true;
$institutions = Dbase::GetInstitutions();
$allfields = Dbase::allFields($user);
Dbase::Disconnect();

$errorMsg   = "";

$firstname  = $user['firstname'];
$lastname   = $user['lastname'];
$studentid  = $user['studentid'];
$email      = $user['email'];
$major      = $user['major'];
$gpa        = $user['gpa'];
$inst       = $user['institute'];
$schoolYear = $user['school_year'];
$sex        = $user['gender'];
$age        = $user['age'];
$race       = $user['race'];
$haveDisa   = $user['have_disa'];
$disability = $user['disability'];

if ( isset($_SESSION["formError"]) )
{
    $errorMsg   = isset($_SESSION["formError"]["msg"])?$_SESSION["formError"]["msg"]:"";
    
    $firstname  = $_SESSION["formError"]["firstname"];
    $lastname   = $_SESSION["formError"]["lastname"];
    $studentid   = $_SESSION["formError"]["studentid"];
    $email      = $_SESSION["formError"]["email"];
    $major      = $_SESSION["formError"]["major"];
    $gpa        = $_SESSION["formError"]["gpa"];
    $inst       = $_SESSION["formError"]["institute"];
    $schoolYear = $_SESSION["formError"]["school_year"];
    $sex        = $_SESSION["formError"]["gender"];
    $age        = $_SESSION["formError"]["age"];
    $race       = $_SESSION["formError"]["race"];
    $haveDisa   = $_SESSION["formError"]["have_disa"];
    $disability = $_SESSION["formError"]["disability"];
}

if ( isset($_SESSION['updatedFields']) )
{
    $updatedFields = $_SESSION['updatedFields'];
    unset($_SESSION['updatedFields']);
}

if ( isset($updatedFields) )
foreach  ($updatedFields as $k=>$v)
{
    switch ($v)
    {
        case "firstname":   $v = "First Name"; break;
        case "lastname":    $v = "Last Name"; break;
        case "studentid":   $v = "Student ID"; break;
        case "email":       $v = "E-Mail"; break;
        case "major":       $v = "Major"; break;
        case "gpa":         $v = "GPA"; break;
        case "institute":   $v = "Institution"; break;
        case "school_year": $v = "School Year"; break;
        case "gender":      $v = "Gender"; break;
        case "age":         $v = "Age"; break;
        case "race":        $v = "Ethnicity"; break;
        case "have_disa":   $v = "Disability Status"; break;
        case "disability":  $v = "Disability Description"; break;
    }
    $updatedFields[$k] = $v;
}

$student = !($instructor || $assessor);

?>