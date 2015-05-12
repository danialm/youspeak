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

        GenerateCommentsTable($comments, $sessionId, $instructor, $userrates, ($showHidden === 'true'), ($showAddressed === 'true'), $studentView, $width, false, false);
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
            $adding = Dbase::AddQuiz($sessionId, $name, $numOptions, $options, $open, $save);
        }

        // must stay right above "get_quizzes"
        
    case "get_quizzes":
        $ret = Dbase::GetQuizzes($sessionId);
        
        foreach ($ret as $i => $quiz)
        {
            if ( !$instructor )
                unset($ret[$i]['choices']);
            
            if ( (!$quiz['form']['open'] || $quiz['form']['save'] == "1" )&& !$instructor) 
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
function isNewComment ($time, $cutoff = 15){
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

function MakeRemoveCommentLink ($commentId, $isFirst)
{
    $html  = "<p class='prating'><a href='#' ";//class='icons'
    $html .= "title='Remove Comment' ";
    $html .= "onclick='AreYouSure(\"Remove this comment?\", FlagComment,$commentId,4); return false;'";
    $html .= $isFirst ? " data-intro='Remove yours' data-position='right'" : "";
    $html .= " >";
    $html .= "<i class='fa fa-trash-o red'></i>";
    $html .= "</a></p>";

    return $html;
}

function MakeRateLinks ($comment,$rating,$rates,$studentView,$isFirst){
    
    $commentId = $comment['id'];
    $selfComment = $comment['user_id'] === $_SESSION["currentUserId"];
    $up = $down = "";
    $html = "";
    
    $activeUp  = "<a  id='iup' href='#' title='Rate Comment Up' onclick='RateUp($commentId); return false;'><i class='fa fa-arrow-up'";    
    $activeUp .= $isFirst ? " data-intro='Rate up' data-position='right'" : "";
    $activeUp .= " ></i></a>";
    
    $inactiveUp  = "<span  id='iup'><i class='fa fa-arrow-up inactive'";
    $inactiveUp .= $isFirst ? " data-intro='Inactive rate up' data-position='right'" : "";
    $inactiveUp .= " ></i></span>";

    $activeDown  = "<a  id='idown' href='#' title='Rate Comment Down' onclick='RateDown($commentId); return false;'><i class='fa fa-arrow-down'";
    $activeDown .= $isFirst ? " data-intro='Rate down' data-position='right'" : "";
    $activeDown .= " ></i></a>";
    
    $inactiveDown  = "<span  id='idown'><i class='fa fa-arrow-down inactive'";
    $inactiveDown .= $isFirst ? " data-intro='Inactive rate down' data-position='right'" : "";
    $inactiveDown .= " ></i></span>";
    
    if($studentView || $selfComment){
        $up = $inactiveUp;
        $down = $inactiveDown;
    }elseif ( !isset($rates[$commentId]) || $rates[$commentId] == 0 ){
        $up = $activeUp;
        $down = $activeDown;
    }elseif ( isset($rates[$commentId]) && $rates[$commentId] > 0 ){
        $up = $inactiveUp;
        $down = $activeDown;
    }elseif ( isset($rates[$commentId]) && $rates[$commentId] < 0 ){
        $up = $activeUp;
        $down = $inactiveDown;
    }

    $html .= "<p class='prating'>$up</p>";
    $html .= "<center><p class='prating'";
    $html .= $isFirst ? "data-intro='Rating' data-position='right'" : "";
    $html .= " >$rating</p></center>";
    $html .= "<p class='prating'>$down</p>";
    
    return $html;
}

function MakeFlagLinks ($commentId, $flagId, $isFirst)
{
    $NOFLAG_ID = 0;
    $ADDRESS_ID = 3;
    $HIDE_ID = 4;
    
    $html = "";
    if ($flagId == $NOFLAG_ID){
        $html .= "<a title='Address'";
        $html .= " href='#' id='iaddress'";//class='icons'
        $html .= " onclick='FlagComment($commentId,$ADDRESS_ID); return false;'";
        $html .= $isFirst ? " data-intro='Address comment' data-position='right'" : "";
        $html .= " >";
        $html .= '<i class="fa fa-comment-o"></i>';
        $html .= "</a>";
        
        $html .= "<a title='Hide'";
        $html .= " href='#' id='ihide'";//class='icons'
        $html .= " onclick='FlagComment($commentId,$HIDE_ID); return false;'";
        $html .= $isFirst ? " data-intro='Hide comment' data-position='right'" : "";
        $html .= " >";
        $html .= '<i class="fa fa-toggle-off"></i>';
        $html .= "</a>";
    }else{ // Can be $ADDRESS_ID && $HIDE_ID
        $html .= "<a title='";
        $html .= $flagId == $ADDRESS_ID ? "Unaddress" : ($flagId == $HIDE_ID  ? "Unhide" : "");
        $html .= "'";
        $html .= " href='#' id='"; 
        $html .= $flagId == $ADDRESS_ID ? "iresaddr" : ($flagId == $HIDE_ID  ? "ireshide" : "");
        $html .= "'";//class='icons'
        $html .= " onclick='FlagComment($commentId,$NOFLAG_ID); return false;'";
        $html .= $isFirst ? ($flagId == $ADDRESS_ID ? " data-intro='Unaddress' data-position='right'" : ($flagId == $HIDE_ID  ? " data-intro='Unhide' data-position='right'" : "")) : "";
        $html .= " >";
        $html .= "<i class='fa fa-";
        $html .= $flagId == $ADDRESS_ID ? "comment" : ($flagId == $HIDE_ID  ? "toggle-on" : "");
        $html .= "'></i>";
        $html .= "</a>";
    }
    
    return $html;
}

function MakeReplyLink($id, $isFirst){
    $html =   "<p title='Reply' id='reply$id' class='prating'>";
    $html.=   "<a href='#' onclick='ClassroomReply($id); return false;'";
    $html.=   $isFirst ? " data-intro='Reply' data-position='right'" : "";
    $html.=   " >";
    $html.=   "<i class='fa fa-reply green'></i></a></p>";
    
    return $html;
}

function GenerateCommentsTable($comments,$sessionId,$instructor,$userates,$showHidden,$showAddressed,$studentView,$width=false,$indent=false,$mobile=false){
    $ulClass = $indent ? "indent": "";
    $pStyle = $width ? ( $indent ? "width:".($width*0.95-80)."px" : "width:".($width-80)."px") : "display: none";
    echo "<ul class='$ulClass'>";
    if ( $comments ) {
        $commentCounter = 0;
        foreach ($comments as $c){
            $isFirst = $commentCounter === 0 && !$indent;//first comment not 
            if (!$instructor && $c["flag_id"]==4)//Hidden comments not shown for non-instructor
                continue;
            
            $newComment = isNewComment($c['time'])? "newComment" : "";
            
            $course = Dbase::GetCourseFromSession($sessionId);
            $roleInCourse = Dbase::GetUserRoleInCourse($c["user_id"], $course["id"]);
            $instructorComment = ($roleInCourse === "in");
            
            $liClass = $c["flag_id"]==4 ? "hiddenComment" : ($c["flag_id"]==3 ? "addressedComment" : ($instructorComment ? "instComment" : ""));


            
            if ( (!$c["flag_id"]==4 && !$c["flag_id"]==3) || ($c["flag_id"]==4 && $showHidden) || ($c["flag_id"]==3 && $showAddressed)){
                
                echo "<li class='$liClass $newComment'>";
                echo "<div class='control' >";
                
                if ($instructor){
                    
                    $flagLinks = MakeFlagLinks($c["id"], $c['flag_id'], $isFirst);
                    echo "$flagLinks ";
                    echo "<center><p class='prating'";
                    echo $isFirst ? " data-intro='Rating' data-position='right'": "";
                    echo " >$c[rating]</p></center>";
                    
                }else{
                    
                    $rateLinks = MakeRateLinks($c,$c['rating'],$userates,$studentView,$isFirst);
                    echo "$rateLinks ";
                    
                    if ($_SESSION['currentUserId'] == $c['user_id']){// if comment owner and non instructor
                
                        $removeLink = MakeRemoveCommentLink($c['id'], $isFirst);
                        echo "$removeLink";
                        
                    }    
                }
                
                if ($c['parent_id'] === "0"){
                    echo MakeReplyLink($c['id'], $isFirst);
                }

                echo "</div><div id='cid$c[id]'";
                if ($mobile) echo " style='padding-left: 20px'";
                echo " ><p style='$pStyle'>$c[comment]</p>";
                echo "</div>";
                echo "</li>";
                
                $commentCounter++;   
                
            }
            
            if($c['children'] && count($c['children'])>0)
                GenerateCommentsTable($c['children'],$sessionId,$instructor,$userates,$showHidden,$showAddressed,$studentView,$width,true,false);
        
         
        }
        
    }

    else 
        echo "<li><div colspan=2>No comments to display</div></li>";
    
    echo "</ul>";
    
}

?>