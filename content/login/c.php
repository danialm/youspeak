<?php
if ( isset($_POST['act']) )
{
    extract($_POST);

    switch ($act)
    {
    case "auth":
        $_SESSION["formError"] = null;
        $ERRMSG_MISMATCH = "These credentials don't match with our records.";
        
        //$usr_email = GmailAuth::Authenticate($access_token);
        
        Dbase::Connect();
        $auth = Dbase::Authenticate($username, $password);//Dbase::Authenticate($username, $password);
        $user = Dbase::GetUserInfo($auth);
        Dbase::Disconnect();

        if ($auth)
        {
            $_SESSION["currentUserId"] = $auth;
            $_SESSION["isInstructor"] = $user['role_code']=='in';

            header("location: ".Page::getRealURL("Courses"));
            exit;
        }
        else
        {
            $_SESSION["formError"]["msg"] = $ERRMSG_MISMATCH;
            
            header("location: ".Page::getRealURL("Login"));
            exit;
        }
        break;
        
    case "logout":
        if ( isset($_SESSION["currentUserId"]) )
            unset($_SESSION["currentUserId"]);

        if ( isset($_SESSION["sessionId"]) )
            unset($_SESSION["sessionId"]);
        break;
    }
    
}

?>