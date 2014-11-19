<?php




if ( isset( $_POST['act'] ))
{
    $ERRMSG_EXISTS_USERNAME = "This username is already registered. ";
    $ERRMSG_EXISTS_EMAIL    = "This email is already registered.";

    $firstName  = $_POST["firstName"];
    $lastName   = $_POST["lastName"];
    $username   = $_POST["username"];
    $email      = $_POST["email"];
    $password   = $_POST["passone"];
    $inst       = $_POST["institute"];
    $major      = $_POST["major"];
    $gpa        = $_POST["gpa"];
    $schoolYear = $_POST["schoolYear"];
    $sex        = $_POST["sex"];
    $age        = $_POST["age"];
    $race       = $_POST["race"];
    $haveDisa   = $_POST["haveDisa"];
    $disability = $_POST["disability"];

    $errMsg = "";
    $error = false;

    Dbase::Connect(); 
    $users = Dbase::GetUsers();

    // check if the desired username or email already exists
    foreach ($users as $u)
    {
        if ($u["username"] == $username)
        {
            if ($error) $errMsg .= "<br />";
            $errMsg .= $ERRMSG_EXISTS_USERNAME;
            if ($error) break;
            $error = true;
        }
            
        if ($u["email"] == $email)
        {
            if ($error) $errMsg .= "<br />";
            $errMsg .= $ERRMSG_EXISTS_EMAIL;
            if ($error) break;
            $error = true;
        }
        
    }

    // special case for AJAX calls
    if ($error && $_POST['async'])
    {
        exit( json_encode( array("msg"=>$errMsg,"error"=>true) ) );
    }

    // store inputted data into the session when errors occured
    // so that the user won't have to re-input everything
    if ( $error && !$_POST['async'] )
    {   
        $_SESSION["formError"]["msg"]        = $errMsg;
        $_SESSION["formError"]["firstname"]  = $firstName;
        $_SESSION["formError"]["lastname"]   = $lastName;
        $_SESSION["formError"]["email"]      = $email;
        $_SESSION["formError"]["username"]   = $username;
        $_SESSION["formError"]["institute"]  = $inst;
        $_SESSION["formError"]["major"]      = $major;
        $_SESSION["formError"]["gpa"]        = $gpa;
        $_SESSION["formError"]["schoolYear"] = $schoolYear;
        $_SESSION["formError"]["sex"]        = $sex;
        $_SESSION["formError"]["age"]        = $age;
        $_SESSION["formError"]["race"]       = $race;
        $_SESSION["formError"]["haveDisa"]   = $haveDisa;
        $_SESSION["formError"]["disability"] = $disability;
        
        Dbase::Disconnect();

        header("Location: ".Page::getRealURL());
        exit;
    }

    else
    {
        // all checks passed, add the new user to the DB
        $role = "st";
            
        if (!$error)
        {
            Dbase::AddUser(
                $firstName, $lastName, $email, 
                $username, $password, 
                $inst, $major, $gpa, $schoolYear,
                $sex, $age, $race,
                $haveDisa, $disability
            );
            $_SESSION['newUserId'] = true;
        }
        
        Dbase::Disconnect();
        
        if ( !$_POST['async'] )
        {
            header("Location: ".Page::getRealURL("Login"));
            exit;
        }
        else
            exit( json_encode( array("error"=>false) ) );
    }
}

?>