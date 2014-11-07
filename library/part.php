<?php

class Part
{
    protected $file;
    
    public function Part ($_file)
    {
        $this->file = $_file;
    }
    
    public function run ()
    {
        $file = $this->file;
        $path = PARTS_LOC.$file;

        if ( file_exists($path) )
            include $path;
            
        else
            throw new Exception("Part: File not found - $path");
    }
}

?>