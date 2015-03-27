<?php

global $reportError;
global $reportMessage;
global $reportCourseId;
global $userId;

$assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor'];
$instructor = isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'];

if(isset($_SESSION['currentUserId']) && $userId = $_SESSION['currentUserId']){

    if($assessor || $instructor){
        if ( isset($_POST['act']) ){
            extract($_POST);
            switch ($act){
                
            case "remove_course":

                Dbase::Connect();
                Dbase::RemoveCourse($courseId);
                Dbase::Disconnect();


                header("Location: ".Page::getRealURL("Reports"));
                exit;
                break;

            case "get_all_report":
                if($assessor){
                    Dbase::Connect();
                    $as_rep = Dbase::GetAssessorReport();
                    Dbase::Disconnect();
                    $out = $as_rep;
                }else{
                    $out = "Access denied!";
                }
                break;
                
            case "get_course_report":
                if($assessor || $instructor){
                    Dbase::Connect();
                    $crs_rep = Dbase::GetAssessorReport($courseId);
                    Dbase::Disconnect();

                    $out = $crs_rep;
                }else{
                    $out = "Access denied!";
                }
                break;
            }
            
            echo json_encode($out);
            exit;
        }
    }else{

        $reportError = true;
        $reportMessage = "Access denied!";
    }
}else{
    header("location: ".Page::getRealURL("Login"));
    exit;
}


function MakeRemoveCourseLink ($courseId){
    $html  = "<a href='#' ";
    $html .= "title='Remove Course' ";
    $html .= "onclick='AreYouSure(\"Remove this course?\", document.remcourse$courseId); return false;'>";
    $html .= '<i class="fa fa-trash-o red"></i>';
    $html .= "</a>";
    $html .= "<form class='hide' name='remcourse$courseId' ";
    $html .= "action='' method='POST'>";
    $html .= "<input type='hidden' name='act' value='remove_course' />";
    $html .= "<input type='hidden' name='courseId' value='$courseId' /></form>";
    return $html;
}