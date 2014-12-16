<?php

global $reportError;
global $reportMessage;
global $reportCourseId;
global $userId;

if(isset($_SESSION['currentUserId']) && $_SESSION['currentUserId']){
    $userId = $_SESSION['currentUserId'];
    if(isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'] == "in"){
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
?>