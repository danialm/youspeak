<?php

if ( isset($_POST['act']) )
{
    $userId = $_SESSION['currentUserId'];
    $ret = null;
    
    extract($_POST);
    
    Dbase::Connect();
    
    switch($act)
    {
    case "update_comments":
        $mobile = isset($mobile)&&$mobile ? true : false;
        $courseInfo = Dbase::GetCourseFromSession($sessionId);
        $roleInCourse = Dbase::GetUserRoleInCourse($userId,$courseInfo["id"]);
        $instructor = ($roleInCourse == "in");
        $comments = Dbase::GetCommentsFromSession($sessionId,$userId);
        $userrates = Dbase::GetCommentRatingsForUser($userId);

        GenerateCommentsTable($comments, $sessionId, $instructor, $userrates, $mobile);
        break;
        
    case "add_rating":
        if ( isset($up) ) Dbase::RateCommentUp  ($userId,$commentId);
        else              Dbase::RateCommentDown($userId,$commentId);

        break;
        
    case "flag_comment":
        Dbase::FlagComment($commentId,$flagId);
        break;
        
    case "remove_comment":
        Dbase::RemoveComment($commentId);
        break;
        
    case "add_comment":
        if ( !isset($sessionId) ) $sessionId = 0;
        if ( !isset($commentId) ) $commentId = 0;

        if ($commentId > 0)
            Dbase::EditComment($commentId, $comment);
        else
            Dbase::AddComment($sessionId, $userId, $comment);

        break;
        
    case "add_quiz":
        if ($_SESSION['isInstructor'])
            $adding = Dbase::AddQuiz($sessionId,$name,$numOptions,$open);

        // must stay right above "get"
        
    case "get_quizzes":
        $ret = Dbase::GetQuizzes($sessionId);
        
        foreach ($ret as $i => $quiz)
        {
            if ( !$_SESSION['isInstructor'] )
                unset($ret[$i]['choices']);
            
            if ( !$quiz['form']['open'] && !$_SESSION['isInstructor']) 
                unset($ret[$i]);
        }
        
        if ( isset($adding) && $adding )
        {
            $tmp = $ret;
            $ret = array();
            $ret['quizzes'] = $tmp;
            $ret['lastquiz'] = $adding;
        }
        
        $ret = json_encode($ret);
        break;
        
    case "remove_quiz":
        Dbase::RemoveQuiz($quizId);
        break;
        
    case "submit_quiz":
        Dbase::SubmitQuizAnswer($userId,$quiz,$answer);
        break;
        
    case "open_quiz":
        Dbase::OpenQuiz($quiz,$open);
        break;
    }
    
    Dbase::Disconnect();
    exit($ret);
}

// UPDATE
function isNewComment ($time, $cutoff = 30)
{
    $now = new DateTime();
    $then = new DateTime($time);
    $diff = $now->diff($then);
    $d = intval($diff->format("%a"));
    $h = $diff->h;
    $m = $diff->i;
    $s = $diff->s;
    $totalSec = 24*60*60*$d + 60*60*$h + 60*$m + $s;
    return ($totalSec < $cutoff);
}

function MakeRemoveCommentLink ($commentId,$mobile=false)
{
    $html = "";
    if ($mobile)
    {
        $html .= "<p class='prating'><a data-role='button' data-icon='delete' data-iconpos='notext' ";
        $html .= "onclick='$(\"#yesbox\").click(function(){RemoveComment($commentId);})' href='#diatest' ";
        $html .= "data-rel='dialog' title='Remove Comment' data-transition='flip'></a>";
        $html .= "</p><script>$('#rmc$commentId').trigger('create')</script>";
    }
    else
    {
        $html .= "<p class='prating'><a class='icons' id='iminus' href='#' ";
        $html .= "title='Remove Comment' ";
        $html .= "onclick='AreYouSure(\"Remove this comment?\", RemoveComment,$commentId); return false;'></a></p>";
    }
    return $html;
}

function MakeRateLinks ($commentId,$rating,$rates,$mobile=false)
{
    $up = $down = "";
    $html = "";
    
    if ($mobile)
    {
        $activeUp  = "<a data-role='button' data-icon='arrow-u' data-iconpos='notext' href='#'";
        $activeUp .= " title='Rate Comment Up' onclick='RateUp($commentId); return false;'></a>";
        $inactiveUp  = "<span  class='ui-disabled' data-role='button' data-icon='arrow-u' data-iconpos='notext'></span>";
        
        $activeDown  = "<a data-role='button' data-icon='arrow-d' data-iconpos='notext' href='#'";
        $activeDown .= " title='Rate Comment Down' onclick='RateDown($commentId); return false;'></a>";
        $inactiveDown = "<span class='ui-disabled' data-role='button' data-icon='arrow-d' data-iconpos='notext'></span>";
    }
    else
    {
        $activeUp  = "<a class='icons' id='iup' href='#'";
        $activeUp .= " title='Rate Comment Up' onclick='RateUp($commentId); return false;'></a>";
        $inactiveUp  = "<span class='icons' id='iup'></span>";
        
        $activeDown  = "<a class='icons' id='idown' href='#'";
        $activeDown .= " title='Rate Comment Down' onclick='RateDown($commentId); return false;'></a>";
        $inactiveDown = "<span class='icons' id='idown'></span>";
    }
    
    if ( !isset($rates[$commentId]) || $rates[$commentId] == 0 )
    {
        $up = $activeUp;
        $down = $activeDown;
    }
    elseif ( isset($rates[$commentId]) && $rates[$commentId] > 0 )
    {
        $up = $inactiveUp;
        $down = $activeDown;
    }
    elseif ( isset($rates[$commentId]) && $rates[$commentId] < 0 )
    {
        $up = $activeUp;
        $down = $inactiveDown;
    }

    $html .= "<p class='prating'>$up</p>";
    $html .= "<center><p class='prating'>$rating</p></center>";
    $html .= "<p class='prating'>$down</p>";
    
    return $html;
}

function MakeFlagLinks ($commentId, $comments)
{
    $NOFLAG_ID = 0;
    $ADDRESS_ID = 3;
    $HIDE_ID = 4;
    
    $flagId = $comments[$commentId]['flag_id'];
    $html = "";
 
    if ($flagId == $NOFLAG_ID)
    {
        $html .= "<a title='Address'";
        $html .= " href='#' class='icons' id='iaddress'";
        $html .= " onclick='FlagComment($commentId,$ADDRESS_ID); return false;'></a>";
        
        $html .= "<a title='Hide'";
        $html .= " href='#' class='icons' id='ihide'";
        $html .= " onclick='FlagComment($commentId,$HIDE_ID); return false;'></a>";
    }
    
    elseif ($flagId == $ADDRESS_ID)
    {
        $html .= "<a title='Restore'";
        $html .= " href='#' class='icons' id='iresaddr'";
        $html .= " onclick='FlagComment($commentId,$NOFLAG_ID); return false;'></a>";
    }
    
    elseif ($flagId == $HIDE_ID)
    {
        $html .= "<a title='Restore'";
        $html .= " href='#' class='icons' id='ireshide'";
        $html .= " onclick='FlagComment($commentId,$NOFLAG_ID); return false;'></a>";
    }
    return $html;
}

function GenerateCommentsTable($comments,$sessionId,$instructor,$userates,$mobile=false)
{
    echo "<table>";
    if ( $comments ) 
    {
        foreach ($comments as $c)
        {
            if ( $c["flag_id"] > 0 )
                continue;

            $newComment = isNewComment($c['time'])?"newComment":"";
             
            echo "<tr><td class='control $newComment' style='width: 20px'>";
            
            // if comment owner
            if ($_SESSION['currentUserId'] == $c['user_id'])
            {
                $removeLink = MakeRemoveCommentLink($c['id'],$mobile);
                echo "$removeLink";
                echo "<center><p class='prating'>$c[rating]</p></center>";
            }
            
            else if (!$instructor)
            {
                $rateLinks = MakeRateLinks($c["id"],$c['rating'],$userates,$mobile);
                echo "$rateLinks ";
            }
            
            if ($instructor)
            {
                $flagLinks = MakeFlagLinks($c["id"],$comments);
                echo "$flagLinks ";
                echo "<p title='Comment Rating' class='prating'>$c[rating]</p>";
            }
            
            echo "</td><td id='cid$c[id]'";
            if ($mobile) echo " style='padding-left: 20px'";
            echo " class='$newComment'><p>$c[comment]</p>";
            echo "</td>";
            if ($_SESSION['currentUserId'] == $c['user_id'])
            {
                $mobString = $mobile?"true":"false";
                $jsFunc = "function () {ClassroomSwitchToEditComment($c[id],$sessionId,$mobString);}";
                $edits[] = "$(\"#cid$c[id] p\").click($jsFunc);";
            }
            echo "</tr>";
        }
        
        foreach ($comments as $c)
        {
            if ($c["flag_id"] == 0)
                continue;
            
            if (!$instructor && ($c["flag_id"]!=3) )   // flag_id==3 means it was addressed
                continue;                              // by instructor meaning students
                                                       // still have accesss to it
            
            $html = "";
            $hidden = null;
            $addressed = null;
            if ($c["flag_id"]==4)   // hidden
            {
                $html .= "<tr class='hiddenComment'><td class='control'>";
                $hidden = true;
            }
            else    // addressed
            {
                $html .= "<tr class='addressedComment'><td class='control'>";
                $addressed = true;
            }
            
            // if comment owner
            if ($_SESSION['currentUserId'] == $c['user_id'])
            {
                $removeLink = MakeRemoveCommentLink($c['id'],$mobile);
                $html .= "$removeLink";
            }
            
            if ($instructor)
            {
                $flagLinks = MakeFlagLinks($c["id"],$comments);
                $html .= "$flagLinks ";
            }
            
            $html .= "<center><p title='Comment Rating' class='prating'>$c[rating]</p></center>";
            $html .= "</td><td><p>$c[comment]</p></td></tr>";
            
            if ($hidden)    $hiddenComments[]    = $html;
            if ($addressed) $addressedComments[] = $html;
        }
        
        if ( isset($addressedComments) )
        {
            $count = count($addressedComments);
            $initLinkText = "&lt;Show $count addressed comment".(($count>1)?"s":"")."&gt;";
            $jsCall = "ClassroomShowOrHideComments(\"addressed\")";
            echo "<tr id='showOrHideButaddressed'><td colspan=2><a href='#' onclick='$jsCall; return false;'>$initLinkText</a></td></tr>";
            foreach ($addressedComments as $c) echo $c;
        }
        
        if ( isset($hiddenComments) )
        {
            $count = count($hiddenComments);
            $initLinkText = "&lt;Show $count hidden comment".(($count>1)?"s":"")."&gt;";
            $jsCall = "ClassroomShowOrHideComments(\"hidden\")";
            echo "<tr id='showOrHideButhidden'><td colspan=2><a href='#' onclick='$jsCall; return false;'>$initLinkText</a></td></tr>";
            foreach ($hiddenComments as $c) echo $c;
        }
        
    }

    else 
        echo "<tr><td colspan=2>No comments to display.</td></tr>";
    
    echo "</table>";
    
    if ( isset($edits) )
    {
        echo "<script>function EditsWhenReady() {";
        foreach ($edits as $e)
            echo "$e ";
        echo "};$(document).ready(EditsWhenReady());</script>";
    }
}

?>