<?php

global $reportError;
global $reportMessage;
global $userId;
global $courseId;

$assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor'];
$instructor = isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'];

if(isset($_SESSION['currentUserId']) && $userId = $_SESSION['currentUserId']){
    if($assessor || $instructor){
        if(isset($_POST) && isset($_POST['courseId'])){
            $_SESSION["courseId"] = $_POST["courseId"];
        }
        if(isset($_SESSION["courseId"])){
            $courseId = $_SESSION["courseId"];
        }else{
            header("location: ".Page::getRealURL("Report"));
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
?>