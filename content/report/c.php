<?php

global $reportError;
global $reportMessage;
global $reportCourseId;
global $userId;

$student = !(isset($_SESSION['isInstructor']) && $_SESSION['isInstructor']) && !(isset($_SESSION['isAssessor']) && $_SESSION['isAssessor']);
if(isset($_SESSION['currentUserId']) && $userId = $_SESSION['currentUserId']){

    if(!$student){
        if ( isset($_POST['act']) ){
            extract($_POST);
            switch ($act)
            {
            case "clear":
                if (isset($_SESSION['reportCourseId']))
                    unset($_SESSION['reportCourseId']);
                
                break;

            case "report":
                $reportCourseId = isset($_POST['reportCourseId']) ? $_POST['reportCourseId'] : '';
                $_SESSION['reportCourseId'] = $reportCourseId;
                break;

            case "remove_course":

                $cId = $_POST["courseId"];

                Dbase::Connect();
                Dbase::RemoveCourse($cId);
                Dbase::Disconnect();


                header("Location: ".Page::getRealURL("Report"));
                break;
            }
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
?>