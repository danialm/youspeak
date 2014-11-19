<?php

global $thisPage;
global $classroomExtraNavi;

$logged = isset($_SESSION['currentUserId']);

$navi = array (
    "About"        => array($logged?"Courses":"Login"),
    "Classroom"    => array("Courses"),
    "Courses"      => array(),
    "Login"        => array(),
    "Profile"      => array("Courses"),
    "Registration" => array("Login")
);

echo "<div id='navNav'>";

echo "<i class='fa fa-home fa-lg' title='Home'></i> &gt; ";

if ( isset($navi[$thisPage]) )
for ($i=0; $i<sizeof($navi[$thisPage]); $i++)
{
    $n = $navi[$thisPage][$i];
     echo "<a href='".Page::getRealURL($n)."'>$n</a> &gt; ";
}

if ($thisPage == "Profile")
    echo "My ";

echo $thisPage;

if ( isset($classroomExtraNavi) )
    echo $classroomExtraNavi;

echo "</div>";

// right side of the bar
echo "<div id='navRight'>";

if ($thisPage == "Login" )
{
    echo "
        <span class='right-side' id='aboutLink'>
            <a href='".Page::getRealURL("About")."'><i class='fa fa-info fa-lg' title='About Us'></i></a>
        </span>
    ";
}

if ( strstr($thisPage, "Classroom") && isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'] )
{ 
    echo "
        <span id='quizLink'>
            <a href='#' id='iplus' 
                    onclick='$(\"#AddQuizDialog\").dialog(\"open\"); return false;'><i class='fa fa-plus fa-lg green'></i>Questionnaire</a>
        </span>
    ";//style='padding-left: 16px;' class='icons'
}

if ( $thisPage == "Courses" && isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'] )
{
    echo "<span id='analLink'>";
    echo "<a ";
    echo "href='#'>";
    echo '<i class="fa fa-cloud-download fa-lg" title="Download Database""></i>';
    echo "</a></span>";
}

if ( $thisPage == "Courses" )
{
    echo "<span id='profileLink'>";
    echo "<a ";
    echo "href='".Page::getRealURL("Profile")."'>";
    echo '<i class="fa fa-user fa-lg" title="Profile"></i>';
    echo "</a></span>";
}

if ( isset($_SESSION['currentUserId']) || isset($_SESSION['newUserId']) )
{
    echo "<span id='logoutLink' class='right-side'>";
    echo "<a href='#' onclick='FormIt({act:\"logout\"},\"".Page::getRealURL("Login")."\"); return false;'>";
    //echo "<a href='#' onclick='FormIt({act:\"logout\"},\"".Page::getRealURL("Login")."\");'>";
    echo '<i class="fa fa-sign-out fa-lg" title="Logout"></i></a></span>';
}

echo "</div>";
?>

<div class='clearer'></div>