<?php


/*
 * sorts associative array by column name
 */
function array_sort_bycolumn(&$array,$column,$dir = 'asc') {
    foreach($array as $a) $sortcol[$a[$column]][] = $a;
    ksort($sortcol);
    foreach($sortcol as $col) {
        foreach($col as $row) {
            $newarr[] = $row;
        }
    }
    
    if($dir=='desc') $array = array_reverse($newarr);
    else $array = $newarr;
}


function MakeRemoveCourseLink ($courseId)
{
    $html  = "<a id='iminus' href='#' ";//class='icons'
    $html .= "title='Remove Course'";
    $html .= "onclick='AreYouSure(\"Remove this course?\", document.remcourse$courseId); return false;'>";
    $html .= '<i class="fa fa-trash-o fa-lg red"></i>';
    $html .= "</a>";
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
    $html  = "<a id='iminus' href='#' ";//class='icons'
    $html .= "title='Remove Session' ";
    $html .= "onclick='AreYouSure(\"Remove this session?\", document.remses$sessionId); return false;'>";
    $html .= '<i class="fa fa-trash-o red"></i>';
    $html .= "</a>";
    $html .= "<form class='hide' name='remses$sessionId' ";
    $html .= "action='' method='POST'>";
    $html .= "<input type='hidden' name='act' value='remove_session' />";
    $html .= "<input type='hidden' name='sessionId' value='$sessionId' /></form>";
    return $html;
}

function MakeAddSessionLink ($courseId)
{
    $linkText = "Add a Session";
    
    $html  = "<dd>";
    $html .= "<a id='iplus' class='addses$courseId'";//class='icons' //style='padding-left: 16px; width: auto;'
//    $html .= " href='#' onclick='pickDate($courseId); return false;'>";
    $html .= " href='#' onclick='toggleAddSessionForm($courseId); return false;'>";
    $html .= '<i class="fa fa-plus green"></i>';
    $html .= "$linkText</a>";
    $html .= "<form style='display: none' class='addses$courseId' action='' method='POST'>";
    $html .= "<input type='hidden' name='act' value='add_session' />";
    $html .= "<input type='hidden' name='courseId' value='$courseId' />";
    $html .= "<input required type='date' name='utime' />";
    $html .= "<input type='submit' value='Add' />";
    $html .= "<input type='button' value='Cancel' onclick='toggleAddSessionForm($courseId); return false;' /></form>";
    $html .= "</dd>";
    
    return $html;
}
function MakeExpandLink ($courseId)
{
    $tagId = "";
    $linkText = "";
    $upOrDown = "";
    
    if ($courseId == $GLOBALS['expand'])
    {
        $linkText = "Show Less";
        $tagId = "iplus";
        $courseId = 0;
        $upOrDown = 'up';
    }
    else
    {
        $linkText = "Show More";
        $tagId = "iplus";
        $upOrDown = 'down';
    }
    
    echo "<dd><a id='$tagId' ";
    echo " href='#' onclick='FormIt({expand:$courseId},\"".Page::getRealURL()."\"); return false;'>";
    echo '<i class="fa fa-caret-'.$upOrDown.'"></i>';
    echo "$linkText</a></dd>";
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

        if($_SESSION['sessionId'] == 0){
            $_SESSION['error'] = $utime." already exists!";
            header("Location: ".Page::getRealURL("Courses"));
            exit;
        }else{
            header("Location: ".Page::getRealURL("Classroom"));
            exit;
        }
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

        $termCode = $_POST["term"];//"fa";
        $year = $_POST["year"];//2012;

        $newCourseId = Dbase::AddCourse($title, $termCode, $year);
        Dbase::AddUserToCourse($userId, $newCourseId, "in");

        Dbase::Disconnect();

        header("Location: ".Page::getRealURL("Courses"));
        break;

    case "edit_course":
        Dbase::Connect();

        $title = $_POST["courseName"];
        $courseId = $_POST["courseId"];
        $termCode = $_POST["term"];//"fa";
        $year = $_POST["year"];//2012;

        Dbase::EditCourse($courseId ,$title, $termCode, $year);

        Dbase::Disconnect();

        header("Location: ".Page::getRealURL("Courses"));
        break;
        
    }
    
    exit;
}
?>