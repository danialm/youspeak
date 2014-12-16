<?php

global $error;
global $message;


if(isset($_SESSION['currentUserId']) && $_SESSION['currentUserId']){

    if(isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'] == "in"){
        var_dump("Success!");
        if ( isset($_POST['act']) ){
            extract($_POST);

            switch ($act)
            {
            case "auth":

                break;

            case "logout":

                break;
            }


        }
    }else{
        $error = true;
        $message = "Access denied!";
        var_dump($message);
    }
}else{
    header("location: ".Page::getRealURL("Login"));
    exit;
}
?>