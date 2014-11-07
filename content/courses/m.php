<?php

global $userId;
global $userInfo;
global $sessions;
global $enrollment;
global $allCourses;
global $roleRef;
global $joinCourseList;
global $warnNewPassword;

global $expand;

//exit ( $_SESSION['currentUserId'] );

if ( isset($_SESSION["currentUserId"]) )
    $userId = $_SESSION["currentUserId"];
    
else
{
    header("location: ".Page::getRealURL("Login"));
    exit ("Restricted Area");
}

Dbase::Connect();

$userInfo = Dbase::GetUserInfo($userId);
$sessions = Dbase::GetSessions();
$enrollment = Dbase::GetEnrollmentFromUser($userId);
$allCourses = Dbase::GetCourses();
$roleRef = Dbase::GetRoleRef();
$joinCourseList = Dbase::GetJoinCourseList();
$warnNewPassword = (Dbase::IsFirstLogin($userId)) && ($userInfo['role_code']=="in");

Dbase::Disconnect();

if ( isset($_REQUEST['expand']) )
    $expand = $_REQUEST['expand'];
else
    $expand = 0;
    
?>