<?php
$studentView = (isset($_SESSION['studentView']) && $_SESSION['studentView']) ? true : false ;

if ( isset($_POST['act']) )
{
    $userId = $_SESSION['currentUserId'];
    $ret = null;
    
    extract($_POST);
    
    Dbase::Connect();
    
    $courseInfo = Dbase::GetCourseFromSession($sessionId);
    $roleInCourse = Dbase::GetUserRoleInCourse($userId,$courseInfo["id"]);
    $instructor = ($roleInCourse == "in") && !$studentView;
    
    switch($act){

    case "update_comments":
        $mobile = isset($mobile)&&$mobile ? true : false;
        $comments = Dbase::GetCommentsFromSession($sessionId,$userId);
        $userrates = Dbase::GetCommentRatingsForUser($userId);

        GenerateCommentsTable($comments, $sessionId, $instructor, $userrates, $studentView, $width, false, false);
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
            if ($parrentId)
                Dbase::AddComment($sessionId, $userId, $comment, $parrentId);
            else
                Dbase::AddComment($sessionId, $userId, $comment);

        break;        
        
    case "add_quiz":
        if ($instructor){
            $adding = Dbase::AddQuiz($sessionId, $name, $numOptions, $options, $open);
        }

        // must stay right above "get_quizzes"
        
    case "get_quizzes":
        $ret = Dbase::GetQuizzes($sessionId);
        
        foreach ($ret as $i => $quiz)
        {
            if ( !$instructor )
                unset($ret[$i]['choices']);
            
            if ( !$quiz['form']['open'] && !$instructor) 
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
        
    case "quiz_correct_answer":
        if ($instructor){
            $temp = Dbase::AddCorrectAnswerToQuiz($quizId, $optionNumber);
            $ret = json_encode($temp);
        }
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
    
    case "changeView":
        $_SESSION['studentView'] = (isset($_SESSION['studentView']) && $_SESSION['studentView']) ? false : true ;
        header("location: " . Page::getRealURL("Classroom"));
        exit;
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
        $html .= "<p class='prating'><a  id='iminus' href='#' ";//class='icons'
        $html .= "title='Remove Comment' ";
        $html .= "onclick='AreYouSure(\"Remove this comment?\", RemoveComment,$commentId); return false;'>";
        $html .= "<i class='fa fa-trash-o red'></i>";
        $html .= "</a></p>";
    }
    return $html;
}

function MakeRateLinks ($commentId,$rating,$rates,$studentView,$mobile=false)
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
    else{
        $activeUp  = "<a  id='iup' href='#'";//class='icons'
        $activeUp .= " title='Rate Comment Up' onclick='RateUp($commentId); return false;'><i class='fa fa-arrow-up'></i></a>";
        $inactiveUp  = "<span  id='iup'><i class='fa fa-arrow-up inactive'></i></span>";//class='icons'
        
        $activeDown  = "<a  id='idown' href='#'";//class='icons'
        $activeDown .= " title='Rate Comment Down' onclick='RateDown($commentId); return false;'><i class='fa fa-arrow-down'></i></a>";
        $inactiveDown = "<span  id='idown'><i class='fa fa-arrow-down inactive'></i></span>";//class='icons'
    }
    
    if($studentView){
        $up = $inactiveUp;
        $down = $inactiveDown;
    }elseif ( !isset($rates[$commentId]) || $rates[$commentId] == 0 ){
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

function MakeFlagLinks ($commentId, $flagId)
{
    $NOFLAG_ID = 0;
    $ADDRESS_ID = 3;
    $HIDE_ID = 4;
    
    //$flagId = $comments[$commentId]['flag_id'];
    $html = "";
    if ($flagId == $NOFLAG_ID){
        $html .= "<a title='Address'";
        $html .= " href='#' id='iaddress'";//class='icons'
        $html .= " onclick='FlagComment($commentId,$ADDRESS_ID); return false;'>";
        $html .= '<i class="fa fa-comment-o"></i>';
        $html .= "</a>";
        
        $html .= "<a title='Hide'";
        $html .= " href='#' id='ihide'";//class='icons'
        $html .= " onclick='FlagComment($commentId,$HIDE_ID); return false;'>";
        $html .= '<i class="fa fa-toggle-off"></i>';
        $html .= "</a>";
    }else{ // Can be $ADDRESS_ID && $HIDE_ID
        $html .= "<a title='";
        $html .= $flagId == $ADDRESS_ID ? "Unaddress" : ($flagId == $HIDE_ID  ? "Unhide" : "");;
        $html .= "'";
        $html .= " href='#' id='"; 
        $html .= $flagId == $ADDRESS_ID ? "iresaddr" : ($flagId == $HIDE_ID  ? "ireshide" : "");
        $html .= "'";//class='icons'
        $html .= " onclick='FlagComment($commentId,$NOFLAG_ID); return false;'>";
        $html .= "<i class='fa fa-";
        $html .= $flagId == $ADDRESS_ID ? "comment" : ($flagId == $HIDE_ID  ? "toggle-on" : "");
        $html .= "'></i>";
        $html .= "</a>";
    }
    
    return $html;
}

function MakeReplyLink($id){
    $html =   "<span title='Reply' id='reply$id'>";
    $html.=   "<a href='#' id='iplus' onclick='ClassroomReply($id); return false;'>";
    $html.=   "<i class='fa fa-reply green'></i></a></span>";
    
    return $html;
}

function GenerateCommentsTable($comments,$sessionId,$instructor,$userates,$studentView,$width=false,$indent=false,$mobile=false){
    $ulClass = $indent ? "indent": "";
    $pStyle = $width ? ( $indent ? "width:".($width*0.95-80)."px" : "width:".($width-80)."px") : "display: none";
    echo "<ul class='$ulClass'>";
    if ( $comments ) {
        foreach ($comments as $c){
            //if ( $c["flag_id"] > 0 )
                //continue;
            if (!$instructor && $c["flag_id"]==4)//Hidden comments not shown for non-instructor
                continue;
            
            //$newComment = isNewComment($c['time'])? "newComment" : "";
            $newComment = "";
            
            $course = Dbase::GetCourseFromSession($sessionId);
            $roleInCourse = Dbase::GetUserRoleInCourse($c["user_id"], $course["id"]);
            $instructorComment = ($roleInCourse === "in");
            
            $liClass = $c["flag_id"]==4 ? "hiddenComment" : ($c["flag_id"]==3 ? "addressedComment" : ($instructorComment ? "instComment" : ""));

            echo "<li class='$liClass'><div class='control $newComment' >";
            
            // if comment owner
            if ($_SESSION['currentUserId'] == $c['user_id'] || $instructor)
            {
                $removeLink = MakeRemoveCommentLink($c['id'],$mobile);
                echo "$removeLink";
                echo "<center><p class='prating'>$c[rating]</p></center>";
            }
            
            else if (!$instructor)
            {
                $rateLinks = MakeRateLinks($c["id"],$c['rating'],$userates,$studentView,$mobile);
                echo "$rateLinks ";
            }
            
            if ($instructor)
            {
                $flagLinks = MakeFlagLinks($c["id"], $c['flag_id']);
                echo "$flagLinks ";
            }
            if ($c['parent_id'] === "0"){
                echo MakeReplyLink($c['id']);
            }
            
            echo "</div><div id='cid$c[id]'";
            if ($mobile) echo " style='padding-left: 20px'";
            echo " class='$newComment'><p style='$pStyle'>$c[comment]</p>";
            echo "</div>";
            if ($_SESSION['currentUserId'] == $c['user_id'])
            {
                $mobString = $mobile?"true":"false";
                $jsFunc = "function () {}";
                $edits[] = "$(\"#cid$c[id] p\").click($jsFunc);";
            }
            echo "</li>";
            if($c['children'] && count($c['children'])>0)
                GenerateCommentsTable($c['children'],$sessionId,$instructor,$userates,$studentView,$width,true,false);
        }
        
        if ( isset($addressedComments) )
        {
            $count = count($addressedComments);
            $initLinkText = "&lt;Show $count addressed comment".(($count>1)?"s":"")."&gt;";
            $jsCall = "ClassroomShowOrHideComments(\"addressed\")";
            echo "<li id='showOrHideButaddressed'><div colspan=2><a href='#' onclick='$jsCall; return false;'>$initLinkText</a></div></li>";
            foreach ($addressedComments as $c) echo $c;
        }
        
        if ( isset($hiddenComments) )
        {
            $count = count($hiddenComments);
            $initLinkText = "&lt;Show $count hidden comment".(($count>1)?"s":"")."&gt;";
            $jsCall = "ClassroomShowOrHideComments(\"hidden\")";
            echo "<li id='showOrHideButhidden'><div colspan=2><a href='#' onclick='$jsCall; return false;'>$initLinkText</a></div></li>";
            foreach ($hiddenComments as $c) echo $c;
        }
        
    }

    else 
        echo "<li><div colspan=2>No comments to display</div></li>";
    
    echo "</ul>";
    
    if ( isset($edits) )
    {
        echo "<script>function EditsWhenReady() {";
        foreach ($edits as $e)
            echo "$e ";
        echo "};$(document).ready(EditsWhenReady());</script>";
    }


}

?>