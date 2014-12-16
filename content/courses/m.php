<?php

global $userId;
global $userInfo;
global $courses;
global $joinCourseList;
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
$joinCourseList = Dbase::GetJoinCourseList($userId);
$terms = Dbase::GetTermRef();
if ( count($enrollment) > 0 ){
    $courses = array();
    foreach ($enrollment as $e){
        $courseID = $e["course_id"];
        $courseInfo = Dbase::GetCourseInfo($courseID);
        foreach($terms as $term){
            if($term['code'] == $courseInfo['term_code']){
                $courseInfo['term_name'] = $term['term_name'];
                $courseInfo['term_order'] = $term['order'];
            }
        }
        $courseInfo['role_in_course'] = $e["role_code"];
        $courseInfo['sessions'] = Dbase::GetSessions($courseID);//SessionsForCourse($courseID);
        array_push($courses, $courseInfo);
    }
    
    array_sort_bycolumn($courses,"term_order");
    array_sort_bycolumn($courses,"year", "desc");
}
Dbase::Disconnect();

if ( isset($_REQUEST['expand']) )
    $expand = $_REQUEST['expand'];
else
    $expand = 0;
    
?>