<?php

define("PAGE_ERROR_VIEW_NOT_FOUND","view not found for page ");
define("PAGE_ERROR_FILE_NOT_FOUND","special page file not found - ");
define("PAGE_ERROR_PAGE_NOT_FOUND","page not found - ");

define("M","m.php");
define("V","v.php");
define("C","c.php");
define("MB","mobile.php");

class Page
{
    private static $allPages = array();
    private static $notFound = null;
    private static $mobile = null;
    
    private $dir;
    private $file;
    private $url;
    private $template;
    
    public function Page ($_url, $_template=false, $_dir=false, $_file=false)
    {
        $this->dir      = $_dir ? $_dir : strtolower($_url."/");
        $this->url      = $_url;
        $this->template = $_template;
        $this->file     = $_file;
        
        self::$allPages[$_url] = $this;
        
        // sneak in a mobile check
        if (self::$mobile == null)
        {
            self::$mobile = new Mobile_Detect();
            self::$mobile = self::$mobile->isMobile();
            if ($_SESSION["settings"]['flags']['forceMobile']) self::$mobile = true;
        }
    }
    
    public static function isMobile ()
    { return self::$mobile; }
    
    public static function get ($_url)
    {
        if (!isset(self::$allPages[$_url]))
            return null;
        
        return self::$allPages[$_url];
    }
    
    public static function getRealURL ($_url=false)
    {
        if (!$_url) $_url = URI;
        
        if ( defined("NO_REWRITE") && NO_REWRITE )
            return WEB_LOC."?p=$_url";
        else
            return WEB_LOC.$_url;
    }
    
    public static function pageExists ($_url)
    {
        return isset(self::$allPages[$_url]);
    }
    
    public static function setPageNotFound ($_url)
    {
        self::$notFound = $_url;
    }
    
    public function prepare ()
    {  
        if ($this->file)
            return;

        $dir = $this->dir;
        if ( file_exists(CONTENT.$dir.C) )
            include(CONTENT.$dir.C);
        
        if ( file_exists(CONTENT.$dir.M) )
            include(CONTENT.$dir.M);
    }
    
    public function render ()
    {
        $dir  = $this->dir;
        $file = $this->file;
        $mobi = self::$mobile;
        
        if ( $file && !file_exists($dir.$file) )
            throw new Exception("Page: ".PAGE_ERROR_FILE_NOT_FOUND.$dir.$file);
        
        elseif ( !$file && $mobi && !file_exists(CONTENT.$dir.MB) )
            throw new Exception("Page: ".PAGE_ERROR_VIEW_NOT_FOUND.$this->url);
        
        elseif ( !$file && !$mobi && !file_exists(CONTENT.$dir.V) )
            throw new Exception("Page: ".PAGE_ERROR_VIEW_NOT_FOUND.$this->url);
            
            
        if ($this->template)
            $this->template->renderHeader();
        
        if     ($file) include($dir.$file);
        elseif ($mobi) include(CONTENT.$dir.MB);
        else           include(CONTENT.$dir.V);
                
        if ($this->template)
            $this->template->renderFooter();
    }
    
    public static function prepareAndRender ($_url)
    {
        $page = Page::get($_url); 
        if ($page)
        {
            $page->prepare();
            $page->render();
        }
        
        elseif ( self::$notFound )
            self::prepareAndRender( self::$notFound );
            
        else
            throw new Exception("Page: ".PAGE_ERROR_PAGE_NOT_FOUND.$_url);
    }
}

?>