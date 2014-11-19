<?php
require_once LIBRARY.'google-api-php-client-master/autoload.php';

class GmailAuth{
    
    private static $client;
    
    public static function Authenticate ($code){

        $temp = $_SESSION['settings']['googleAuth'];
        self::$client = new Google_Client();
        $appName = $temp['appname'];
        $clientId = $temp['clientid'];
        $clientSacred = $temp['clientsacred'];

        self::$client->setApplicationName($appName);
        self::$client->setClientId($clientId);
        self::$client->setClientSecret($clientSacred);
        self::$client->setRedirectUri('postmessage');
        self::$client->authenticate($code);
        $token = json_decode(self::$client->getAccessToken());
        $attributes = self::$client->verifyIdToken($token->id_token, $clientId)->getAttributes();

        return $attributes['payload']['email'];
    }
    public static function Logout (){
        self::$client->revokeToken();
    }
}
?>
