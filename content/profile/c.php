<?php

$ERRMSG_EXISTS_USERNAME = "This username is already registered.";
$ERRMSG_EXISTS_EMAIL    = "This email is already registered.";
$ERRMSG_EMPTY_USERNAME  = "Username is a required field and was left empty.";
$ERRMSG_EMPTY_FIRSTNAME = "Firstname is a required field and was left empty.";
$ERRMSG_EMPTY_LASTNAME  = "Lastname is a required field and was left empty.";
$ERRMSG_EMPTY_EMAIL     = "Email is a required field and was left empty.";


if (isset($_POST['act'])){
    
    extract($_POST);
    switch ($act)
    {
    case "update_profile":

        $errMsg = "";
        $error = false;
        $_SESSION['formError'] = null;
        $id = isset($_SESSION['currentUserId']) && $_SESSION['currentUserId'] ? $_SESSION['currentUserId'] : $_SESSION['newUserId'];
        
        Dbase::Connect();
        $users = Dbase::GetUsers();
        $user  = Dbase::GetUserInfo($id);

        $usernameBad  = false;
        $emailBad     = false;
        $firstnameBad = false;
        $lastnameBad  = false;
        
        // check for required fields that are empty
        if ($username == "")
        {
            if ($errMsg != "") $errMsg .= "<br />";
            $errMsg .= $ERRMSG_EMPTY_USERNAME;
            $usernameBad = true;
        }

        if ($firstname == "")
        {
            if ($errMsg != "") $errMsg .= "<br />";
            $errMsg .= $ERRMSG_EMPTY_FIRSTNAME;
            $firstnameBad = true;
        }

        if ($lastname == "")
        {
            if ($errMsg != "") $errMsg .= "<br />";
            $errMsg .= $ERRMSG_EMPTY_LASTNAME;
            $lastnameBad = true;
        }

        if ($email == "")
        {
            if ($errMsg != "") $errMsg .= "<br />";
            $errMsg .= $ERRMSG_EMPTY_EMAIL;
            $emailBad = true;
        }

        // check for an existing username and email
        foreach ($users as $u)
        {
            if ($u['id'] == $id)
                continue;
            
            if ($u["username"] == $username)
            {
                if ($errMsg != "") $errMsg .= "<br />";
                $usernameBad = true;
                $errMsg .= $ERRMSG_EXISTS_USERNAME;
                
                if ($error) break;
                $error = true;
            }

            if (($u["email"] == $email) && ($user['email'] != $email) && !$emailBad)
            {
                if ($errMsg != "") $errMsg .= "<br />";
                $emailBad = true;
                $errMsg .= $ERRMSG_EXISTS_EMAIL;
                
                if ($error) break;
                $error = true;
            }
        }

        $_SESSION['updatedFields'] = array();
        foreach ($user as $key=>$val)
        {
            if ( is_numeric($key) ) continue;
            
            $_SESSION['formError'][$key] = isset($_POST[$key])?$_POST[$key]:"";
            
            if ( ($key == "username") && $usernameBad  ) continue;
            if ( ($key ==    "email") && $emailBad     ) continue;
            if ( ($key =="firstname") && $firstnameBad ) continue;
            if ( ($key == "lastname") && $lastnameBad  ) continue;
            if (  $key ==        "id" )                  continue;
            if (  $key == "role_code" )                  continue;
            
            // value has changed
            if ($val != $_POST[$key])
            {
                $c = array("key"=>$key, "val"=>$_POST[$key]);
                
                if ( ($key!="firstname") && ($key!="username") )
                    $c['val'] = Dbase::Encrypt($c['val']);
                
                $changes[] = $c;
                $_SESSION['updatedFields'][] = $key;
            }
        }
        
        if ( isset($changes) )
            Dbase::Updates("users",$changes,"id=$id") or exit(mysql_error());


        $_SESSION['formError']['msg'] = $errMsg;
        Dbase::Disconnect();
        header("Location: ".Page::getRealURL("Profile"));
        exit;
    
    }
}




?>