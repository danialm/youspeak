<?php
//require_once LIBRARY.'google-api-php-client/autoload.php';
class GmailAuth{
    
    private static $user;

    public static function Authenticate ($access_token)
    {
    $req = new HTTP_Request2('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $access_token );
    $req->setConfig(array(
        'ssl_verify_peer' => FALSE,
        'ssl_verify_host' => FALSE
    ));

    $req->setMethod('GET');
    try {
        $response = $req->send();
        if (200 == $response->getStatus()) {
            $res_body = $response->getBody();
            var_dump($res_body);
        } else {
            $error = 'Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase();
        }
    } catch (HTTP_Request2_Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
        return false;
    }
}

?>
