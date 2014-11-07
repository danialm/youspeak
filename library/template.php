<?php

class Template
{
    private $header;
    private $footer;
    
    private $mobhead;
    private $mobfoot;
    
    private $counter;

    public function Template ($_header, $_footer, $_mobhead=false, $_mobfoot=false)
    {
        
        $this->header = $_header;
        $this->footer = $_footer;
        
        if ($_mobhead && $_mobfoot)
        {
            $this->mobhead = $_mobhead;
            $this->mobfoot = $_mobfoot;
        }
        else
        {
            $this->mobhead = false;
            $this->mobfoot = false;
        }
        
        $this->counter = 0;
        
    }
    
    public function renderHeader ()
    {
        if ( Page::isMobile() && $this->mobhead )
            $this->mobhead->run();
        
        else
            $this->header->run();
            
        $this->counter = 1;
    }
    
    public function renderFooter ()
    {
        if ( Page::isMobile() && $this->mobfoot )
            $this->mobfoot->run();
        
        else
            $this->footer->run();
            
        $this->counter = 2;
    }
    
    public function render ()
    {
        switch ($this->counter)
        {
        case 0:
            $this->renderHeader();
            break;
            
        case 1:
            $this->renderFooter();
            break;
        
        default:
            break;
        }
        
        $this->counter++;
    }
}

?>