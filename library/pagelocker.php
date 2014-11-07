<?php

class PageLocker
{
    private static $verification = null;
    private static $page = null;
    
    public static function set ($veri)
    {
        /*self::$verification = $veri;
        
        if (!self::$page)
        {
            self::$page = new Part("pagelocker.php");
            self::$page->prepare();
            
            if ( !isset($_SESSION['pagelocker']) )
            {
                $_SESSION["pagelocker"] = array();
                $_SESSION["pagelocker"]["verification"] = null;
            }
        }*/
    }
    
    public static function lockThisPage ()
    {
        /*if (!self::$verification)
        {
            Page::get("Error")->render();
            error_log("PageLocker: Attempting to access a locked page, but pagelocker does not have a verification code.");
            exit;
        }
        
        if ($_SESSION["pagelocker"]["verification"] != self::$verification)
        {
            self::$page->render();
            exit;
        }*/
    }
}

?>