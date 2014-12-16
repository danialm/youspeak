<?php

$ERRMSG_EXISTS_EMAIL    = "This email is already registered.";
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
        
        $emailBad     = false;
        $firstnameBad = false;
        $lastnameBad  = false;

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

        // check for an existing email
        foreach ($users as $u)
        {
            if ($u['id'] == $id)
                continue;

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
            
            if ( ($key ==    "email") && $emailBad     ) continue;
            if ( ($key =="firstname") && $firstnameBad ) continue;
            if ( ($key == "lastname") && $lastnameBad  ) continue;
            if (  $key ==        "id" )                  continue;
            if (  $key == "role_code" )                  continue;
            
            // value has changed
            if ($val != $_POST[$key])
            {
                $c = array("key"=>$key, "val"=>$_POST[$key]);
                
                if ( ($key!="firstname")  )//&& ($key!="username")
                    $c['val'] = Dbase::Encrypt($c['val']);
                
                $changes[] = $c;
                $_SESSION['updatedFields'][] = $key;
            }
        }
        
        $required = false;
        if ( isset($changes) ){
            Dbase::Updates("users",$changes,"id=$id") or exit(mysql_error());
            $required = Dbase::requiredFields(Dbase::GetUserInfo($id));
        }

        $_SESSION['formError']['msg'] = $errMsg;
        Dbase::Disconnect();
        
//        var_dump($required);
        if(isset($_SESSION['newUserId']) && $required ){//new user who fill out the required field.
            unset($_SESSION['newUserId']);
            header("Location: ".Page::getRealURL("Courses"));
            exit;    
        }
        
        header("Location: ".Page::getRealURL("Profile"));
        exit;
    
    }
}




?>