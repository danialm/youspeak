<?php

global $reportError;
global $reportMessage;
global $userId;
global $courses;

$COMMENTS_REPORT_TITLE = 'comments';
$QUIZ_REPORT_TITLE = 'questionnaires';

if(!$reportError){
    $inReport = $report = null;
    $assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor'];
    $admin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];
    Dbase::Connect();
        
    if($assessor){
        $allCourses = Dbase::GetCourses();
        $courses = array();
        foreach($allCourses as $crs){
            $temp_course = Array();
            $temp_course['title'] = $crs['title'] . " (" . Dbase::GetTermRef($crs['term_code']) . " " . $crs['year'] . ")";
            $temp_course['id'] = $crs['id'];
            if($admin && Dbase::GetEnrollmentFromCourse($crs['id']) === null)
                $temp_course['noInstructor'] = true;
            array_push($courses, $temp_course);
        }
    }else{
        $userCourses = Dbase::GetEnrollmentFromUser($userId);
        $courses = array();
        foreach($userCourses as $crs){
            if($crs['role_code'] == 'in'){
                $temp_course = Dbase::GetCourseInfo($crs['course_id']);
                $temp_course['title'] = $temp_course['title'] . " (" . Dbase::GetTermRef($temp_course['term_code']) . " " . $temp_course['year'] . ")";
                array_push($courses, $temp_course);
            }
        }
        foreach($courses as $crs){
                $inReport['courses'][$crs['id']]['name'] = $crs['title'];
                $inReport['courses'][$crs['id']]['report'] = Dbase::GetAssessorReport($crs['id']);
        }
    }

    Dbase::Disconnect();
}
