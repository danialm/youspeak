<?php
global $logout;

$logout = false;

if (isset($_POST['act'])) {

    extract($_POST);

    switch ($act) {

        case "auth":

            $temp = $_SESSION['settings']['googleAuth'];
            $client = new Google_Client();
            $appName = $temp['appname'];
            $clientId = $temp['clientid'];
            $clientSacred = $temp['clientsacred'];

            $client->setApplicationName($appName);
            $client->setClientId($clientId);
            $client->setClientSecret($clientSacred);
            $client->setRedirectUri('postmessage');
            $client->authenticate($code); //POST variable
            $token = json_decode($client->getAccessToken());

            $attributes = $client->verifyIdToken($token->id_token, $clientId)->getAttributes();

            $email = $attributes['payload']['email'];
            $_SESSION['login_email'] = $email;
            Dbase::Connect();
            $auth = Dbase::Authenticate($email);
            //$auth = 3;
            $user = Dbase::GetUserInfo($auth);
            Dbase::Disconnect();
                
            if (!Dbase::requiredFields($user)) {
                
                $_SESSION['newUserId'] = $auth;
                
                header("location: " . Page::getRealURL("Profile"));
                exit;
                
            } else {

                $_SESSION['currentUserId'] = $auth;
                
                switch ($user['role_code']){
                case 'in' :
                    $_SESSION['isInstructor'] = true;
                    header("location: " . Page::getRealURL("Courses"));
                    exit;
                    
                case 'st' :
                    header("location: " . Page::getRealURL("Courses"));
                    exit;
                    
                case 'as' :
                    $_SESSION['isAssessor'] = true;
                    header("location: " . Page::getRealURL("Report"));
                    exit;
                    
                case 'ai' ://assessor and instructor
                    $_SESSION['isAssessor'] = true;
                    $_SESSION['isInstructor'] = true;
                    header("location: " . Page::getRealURL("Courses"));
                    exit;
                    
                case 'ad' ://admin and assessor and instructor
                    $_SESSION['isAssessor'] = true;
                    $_SESSION['isInstructor'] = true;
                    $_SESSION['isAdmin'] = true;
                    header("location: " . Page::getRealURL("Courses"));
                    exit;
                }
            }

            break;

        case "logout":
            
            $logout = true;

            session_unset();
                
            break;
    }
}

?>