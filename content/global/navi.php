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

echo "YouSpeak &gt; ";

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
            <a href='".Page::getRealURL("About")."'>About</a>
        </span>
    ";
}

if ( strstr($thisPage, "Classroom") && isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'] )
{ 
    echo "
        <span id='quizLink'>
            <a href='#' class='icons' id='iplus' style='padding-left: 16px;'
                    onclick='$(\"#AddQuizDialog\").dialog(\"open\"); return false;'>
                Questionnaire
            </a>
        </span>
    ";
}

if ( $thisPage == "Courses" && isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'] )
{
    echo "<span id='analLink'>";
    echo "<a ";
    echo "href='#'>";
    echo "Download Database";
    echo "</a></span>";
}

if ( $thisPage == "Courses" )
{
    echo "<span id='profileLink'>";
    echo "<a ";
    echo "href='".Page::getRealURL("Profile")."'>";
    echo "My Profile";
    echo "</a></span>";
}

if ( isset($_SESSION['currentUserId']) )
{
    echo "<span id='logoutLink' class='right-side'>";
    echo "<a href='#' onclick='FormIt({act:\"logout\"},\"".Page::getRealURL("Login")."\"); return false;'>";
    echo "Log Out</a></span>";
}

echo "</div>";
?>

<div class='clearer'></div>