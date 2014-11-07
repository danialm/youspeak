<?php

function SessionsForCourse($courseId)
{
    $n = 0;
    $sessions = $GLOBALS["sessions"];
    
    if ($sessions) foreach ($sessions as $s)
    {
        if ($s["course_id"] != $courseId)
            continue;

        $sfc[$n++] = $s;
    }

    if ( !isset($sfc) )
        return null;

    return $sfc;
}

function MakeRemoveCourseLink ($courseId)
{
    $html  = "<a class='icons' id='iminus' href='#' ";
    $html .= "title='Remove Course'";
    $html .= "onclick='AreYouSure(\"Remove this course?\", document.remcourse$courseId); return false;'></a>";
    $html .= "<form class='hide' name='remcourse$courseId' ";
    $html .= "action='' method='POST'>";
    $html .= "<input type='hidden' name='act' value='remove_course' />";
    $html .= "<input type='hidden' name='courseId' value='$courseId' /></form>";
    return $html;
}

function MakeSessionLink ($sessionId, $linkText, $instruct)
{
    $html  = "<form class='hide' name='sf$sessionId' action='".Page::getRealURL("Classroom")."' method='POST'>";
    $html .= "<input type='hidden' name='sessionId' value=$sessionId /></form>";
    $html .= "<a href='#' onclick='document.sf$sessionId.submit(); return false;'>$linkText</a>";
    return $html;
}

function MakeRemoveSessionLink ($sessionId)
{
    $html  = "<a class='icons' id='iminus' href='#' ";
    $html .= "title='Remove Session' ";
    $html .= "onclick='AreYouSure(\"Remove this session?\", document.remses$sessionId); return false;'></a>";
    $html .= "<form class='hide' name='remses$sessionId' ";
    $html .= "action='' method='POST'>";
    $html .= "<input type='hidden' name='act' value='remove_session' />";
    $html .= "<input type='hidden' name='sessionId' value='$sessionId' /></form>";
    return $html;
}

function MakeAddSessionLink ($courseId)
{
    $linkText = "Add a Session";
    
    $html  = "<dd><form class='hide' name='addses$courseId'";
    $html .= " action='' method='POST'>";
    $html .= "<input type='hidden' name='act' value='add_session' />";
    $html .= "<input type='hidden' name='courseId' value='$courseId' /></form>";
    $html .= "<a class='icons' id='iplus' style='padding-left: 16px; width: auto;'";
    $html .= " href='#' onclick='pickDate($courseId); return false;'>";
    $html .= "$linkText</a></dd>";
    
    return $html;
}
function MakeExpandLink ($courseId)
{
    $tagId = "";
    $linkText = "";
    
    if ($courseId == $GLOBALS['expand'])
    {
        $linkText = "Show Less";
        $tagId = "iplus";
        $courseId = 0;
    }
    else
    {
        $linkText = "Show More";
        $tagId = "iplus";
    }
    
    echo "<dd><a class='icons' id='$tagId' style='padding-left: 16px; width: auto;'";
    echo " href='#' onclick='FormIt({expand:$courseId},\"".Page::getRealURL()."\"); return false;'>$linkText</a></dd>";
}

if ( isset( $_POST['act'] ) )
{
    switch($_POST['act'])
    {
    case "add_session":
        $courseId = $_POST["courseId"];
        $utime = $_POST["utime"];

        Dbase::Connect();
        $_SESSION['sessionId'] = Dbase::AddSession($courseId,$utime);
        Dbase::Disconnect();


        header("Location: ".Page::getRealURL("Classroom"));
        break;
        
    case "join_course":
        Dbase::Connect();

        $cid = $_POST['c'];
        $uid = $_POST["u"];
        $role = "st";

        Dbase::AddUserToCourse($uid, $cid, $role);

        Dbase::Disconnect();

        header("Location: ".Page::getRealURL("Courses"));
        break;

    case "remove_course":

        $cId = $_POST["courseId"];

        Dbase::Connect();
        Dbase::RemoveCourse($cId);
        Dbase::Disconnect();


        header("Location: ".Page::getRealURL("Courses"));
        break;

    case "remove_session":

        $sId = $_POST["sessionId"];

        Dbase::Connect();
        Dbase::RemoveSession($sId);
        Dbase::Disconnect();

        header("Location: ".Page::getRealURL("Courses"));
        break;

    case "add_course":
        Dbase::Connect();

        $title = $_POST["courseName"];
        $userId = $_SESSION["currentUserId"];

        $termCode = "fa";
        $year = 2012;

        $newCourseId = Dbase::AddCourse($title, $termCode, $year);
        Dbase::AddUserToCourse($userId, $newCourseId, "in");

        Dbase::Disconnect();

        header("Location: ".Page::getRealURL("Courses"));
        break;
        
    }
    
    exit;
}
?>