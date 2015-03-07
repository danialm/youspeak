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
    "Reports"      => array("Courses"),
    "Report"      => array("Courses","Reports")
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



if ( strstr($thisPage, "Classroom")){
        echo "<span id='commentLink'><a href='#' id='iplus' onclick='ClassroomReply(); return false;'><i class='fa fa-plus fa-lg green'></i>Comment</a></span>";
    if($instructor){   
        echo "
            <span id='quizLink'>
                <a href='#' onclick='showEditQuiz(); return false;'><i class='fa fa-plus fa-lg green'></i>Questionnaire</a>
            </span>
        ";
        echo "
            <span id='studentView' title='Student View'>
                <a href='#' onclick='FormIt({act:\"changeView\"},\"".Page::getRealURL("Classroom")."\"); return false;' data-intro='Student View' data-position='left'><i class='fa fa-binoculars fa-lg'></i></a>
            </span>
        ";
    }
}
if ( $thisPage == "Courses" && !$student){
    
    echo "<span id='analLink'>";
    echo "<a ";
    echo "href='".Page::getRealURL("Reports")."'";
    echo " data-intro='Report' data-position='left'";
    echo " >";
    echo '<i class="fa fa-bar-chart fa-lg" title="Report"></i>';
    echo "</a></span>";
}

if ( $logged && $thisPage != "Profile" ){
    
    echo "<span id='profileLink' class='";
    echo !$allfields ? "notComplete" : "";
    echo "'>";
    echo "<a href='".Page::getRealURL("Profile")."'";
    echo $thisPage == "Courses" ? " data-intro='Profile' data-position='bottom'" : "";
    echo " >";
    echo '<i class="fa fa-user fa-lg" title="Profile"></i>';
    echo "</a></span>";
}

if ($thisPage != "About" ){
    
    echo "<span class='right-side' id='aboutLink'>";
    echo "<a href='".Page::getRealURL("About")."'";
    //echo $thisPage == "Courses" ? " data-intro='About Us' data-position='bottom'" : "";
    echo " ><i class='fa fa-info fa-lg no-margin' title='About Us'></i></a>";
    echo "</span>";
    
    if(in_array($thisPage, array("Classroom","Courses","Profile"))){
        echo "
            <span id='help-link' class='right-side'>
                <a href='#' onclick='$(\"body\").chardinJs(\"start\"); return false'><i class='fa fa-question fa-lg no-margin' title='Help'></i></a>
            </span>
        ";        
    }
}

if ( isset($_SESSION['currentUserId']) || isset($_SESSION['newUserId']) ){
    
    echo "<span id='logoutLink' class='right-side'>";
    echo "<a href='#' onclick='FormIt({act:\"logout\"},\"".Page::getRealURL("Login")."\"); return false;'";
    echo $thisPage != "Classroom" ? " data-intro='Logout' data-position='bottom'" : "";
    echo '><i class="fa fa-sign-out fa-lg no-margin" title="Logout"></i></a></span>';
}
    
echo "</div>";

?>

<div class='clearer'></div>