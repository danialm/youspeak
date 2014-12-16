<?php

global $userInfo;
global $sessionInfo;
global $courseInfo;
global $comments;
global $userrates;
global $roleInCourse;
global $presents;
global $quizzesAnswered;
global $sessionId;
global $instructor;

if ( isset($_SESSION["currentUserId"]) )
    $userId = $_SESSION["currentUserId"];
else
{
    header("location: ".Page::getRealURL("Login"));
    exit ("Restricted Area");
}

if ( !isset($_POST["sessionId"]) && !isset($_SESSION["sessionId"]) )
{
    header("location: ".Page::getRealURL("Login"));
    exit ("Access Error");
}

if ( isset($_POST["sessionId"]) )
{
    $_SESSION["sessionId"] = $_POST["sessionId"];
    header("location: ".Page::getRealURL("Classroom"));
    exit;
}
    
$sessionId = $_SESSION["sessionId"];

Dbase::Connect();
$userInfo = Dbase::GetUserInfo($userId);
$sessionInfo = Dbase::GetSessionInfo($sessionId);
$courseInfo = Dbase::GetCourseFromSession($sessionId);
$comments = Dbase::GetCommentsFromSession($sessionId,$userId);
$userrates = Dbase::GetCommentRatingsForUser($userId);
$roleInCourse = Dbase::GetUserRoleInCourse($userId,$courseInfo["id"]);
$presents = Dbase::GetPresentsFromSession($sessionId);
$quizzesAnswered = Dbase::getQuizzesUserAnswered($userId);
Dbase::Disconnect();

global $classroomExtraNavi;
$classroomExtraNavi  = ": <b>$courseInfo[title]</b> on <b>";
$classroomExtraNavi .= date("M jS, Y", strtotime($sessionInfo['date'])) . "</b>";

$canAddPresents = null;
$canAddComments = null;
$instructor = ($roleInCourse == "in");

if ( $instructor )
    $canAddPresents = true;
else
    $canAddComments = true;

?>