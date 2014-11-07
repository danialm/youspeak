<?php 

global $thisPage;
global $title;
global $navi;

$navi = new Part("navi.php");
    
$thisPage = URI;
$title = strip_tags($thisPage);

?>