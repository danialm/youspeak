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

function MakeLeaveCourseLink ($userId, $courseId)
{
    $html  = "<a href='#' ";//class='icons' id='iminus' 
    $html .= "title='Drop Course' ";
    $html .= "onclick='AreYouSure(\"Drop this course?\", document.leavecourse$courseId); return false;'>";
    $html .= '<i class="fa fa-remove fa-lg red"></i>';
    $html .= "</a>";
    $html .= "<form class='hide' name='leavecourse$courseId' ";
    $html .= "action='' method='POST'>";
    $html .= "<input type='hidden' name='act' value='leave_course' />";
    $html .= "<input type='hidden' name='courseId' value='$courseId' />";
    $html .= "<input type='hidden' name='userId' value='$userId' /></form>";
    return $html;
}

function MakeSessionLink ($sessionId, $linkText, $instruct)
{
    $html  = "<form class='hide' name='sf$sessionId' action='".Page::getRealURL("Classroom")."' method='POST'>";
    $html .= "<input type='hidden' name='sessionId' value=$sessionId /></form>";
    $html .= "<a href='#' onclick='document.sf$sessionId.submit(); return false;'>$linkText</a>";
    return $html;
}

function MakeRemoveSessionLink ($sessionId, $first)
{
    $html  = "<a href='#' ";
    $html .= "title='Remove Session' ";
    $html .= "onclick='AreYouSure(\"Remove this session?\", document.remses$sessionId); return false;'";
    $html .= $first ? " data-intro='Remove Session' data-position='right'" : "";
    $html .= " >";
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
    
    $html  = "<dd class='addSession'>";
    $html .= "<a id='iplus' class='addses$courseId'";
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
    
    case "leave_course":

        $cId = $_POST["courseId"];
        $uId = $_POST["userId"];

        Dbase::Connect();
        Dbase::RemoveUserFromCourse($uId, $cId);
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

    case "add_user":
        Dbase::Connect();

        $fname = $_POST["firstname"];
        $lname = $_POST["lastname"];
        $email = $_POST["email"];
        $institude = $_POST["institude"];
        $role = $_POST["role"];

        echo  Dbase::AddUser($email,$fname,$lname,$institude,$role);

        Dbase::Disconnect();
        break;
        
    }
    
    exit;
}
?>