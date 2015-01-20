<?php

global $thisPage;
global $classroomExtraNavi;

$instructor = isset($_SESSION['isInstructor']) && $_SESSION['isInstructor'];
$assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor'];
$student = !($instructor || $assessor);

$id = isset($_SESSION['currentUserId']) && $_SESSION['currentUserId'] ? $_SESSION['currentUserId'] : $_SESSION['newUserId'];
Dbase::Connect();
$allfields = Dbase::allFields(Dbase::GetUserInfo($id));
Dbase::Disconnect();

$logged = isset($_SESSION['currentUserId']);

$navi = array (
    "About"        => array($logged?"Courses":"Login"),
    "Classroom"    => array("Courses"),
    "Courses"      => array(),
    "Login"        => array(),
    "Profile"      => array("Courses"),
    "Report"       => array("Courses")
    //"Registration" => array("Login")
);

echo "<div id='navNav'>";

echo "<i class='fa fa-home fa-lg' title='Home'></i> &gt; ";

if ( isset($navi[$thisPage]) )
for ($i=0; $i<sizeof($navi[$thisPage]); $i++){
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



if ( strstr($thisPage, "Classroom") && $instructor){   
    echo "
        <span id='quizLink'>
            <a href='#' 
                    onclick='$(\"#AddQuizDialog\").dialog(\"open\"); return false;'><i class='fa fa-plus fa-lg green'></i>Questionnaire</a>
        </span>
    ";
    echo "
        <span id='studentView' title='Student View'>
            <a href='#' 
                    onclick='FormIt({act:\"changeView\"},\"".Page::getRealURL("Classroom")."\"); return false;'><i class='fa fa-binoculars fa-lg'></i></a>
        </span>
    ";
}

if ( $thisPage == "Courses" && !$student)
{
    echo "<span id='analLink'>";
    echo "<a ";
    echo "href='".Page::getRealURL("Report")."'>";
    echo '<i class="fa fa-bar-chart fa-lg" title="Report""></i>';
    echo "</a></span>";
}

if ( $logged && $thisPage != "Profile" )
{
    echo "<span id='profileLink' class='";
    if(!$allfields)
        echo "notComplete";
    echo "'>";
    echo "<a ";
    echo "href='".Page::getRealURL("Profile")."'>";
    echo '<i class="fa fa-user fa-lg" title="Profile"></i>';
    echo "</a></span>";
}

if ($thisPage != "About" ){
    echo "
        <span class='right-side' id='aboutLink'>
            <a href='".Page::getRealURL("About")."'><i class='fa fa-info fa-lg no-margin' title='About Us'></i></a>
        </span>
    ";
}

if ( isset($_SESSION['currentUserId']) || isset($_SESSION['newUserId']) )
{
    echo "<span id='logoutLink' class='right-side'>";
    echo "<a href='#' onclick='FormIt({act:\"logout\"},\"".Page::getRealURL("Login")."\"); return false;'>";
    echo '<i class="fa fa-sign-out fa-lg no-margin" title="Logout"></i></a></span>';
}
    
echo "</div>";

?>

<div class='clearer'></div>