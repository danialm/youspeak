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
                $_SESSION['isInstructor'] = $user['role_code'] == 'in';

                header("location: " . Page::getRealURL("Courses"));
                exit;
            }

            break;

        case "logout":
            
            $logout = true;

            if (isset($_SESSION['currentUserId']))
                unset($_SESSION['currentUserId']);

            if (isset($_SESSION['sessionId']))
                unset($_SESSION['sessionId']);

            if (isset($_SESSION['newUserId']))
                unset($_SESSION['newUserId']);
            
            if (isset($_SESSION['reportCourseId']))
                unset($_SESSION['reportCourseId']);

            break;
    }
}

?>