<?php

global $userInfo;
global $sessionInfo;
global $courseInfo;
global $comments;
global $userrates;
global $roleInCourse;
global $presents;
global $sessionId;
global $instructor;
global $quizzesAnswered;

$studentView = (isset($_SESSION['studentView']) && $_SESSION['studentView']) ? true : false ;
$instructor = $instructor && !$studentView;

?>

<script>
<?php if(!$instructor){?>
    $(document).ready(function(){
        $('#quizLink').hide();
    });
<?php } ?>
var quizzesAnswered = <?= json_encode($quizzesAnswered) ?>;

window.hideaddressedComments = true;
window.hidehiddenComments = true;
window.UpdateCommentsEvent = function (){
    UpdateSessionComments( <?php echo $sessionId; ?> );
    getQuizzes(<?php echo $sessionId; ?>);
};
var updateCommentsEvent = setInterval(UpdateCommentsEvent,1000);

$(".right-side").css("top","5px");
</script>
<style> body { background-color: black; background-image: none; } </style>

<div id="classroom">        
    <div id='confirmation' title='Are You Sure?'></div>
    <script>
        $("#confirmation").dialog({autoOpen:false});
        $("#confirmation").dialog("option","resizable",false);
        $("#confirmation").dialog("option","modal",true);
        $("#heading").hide();
        //$("#navigation")[0].style.border = "3px ridge #394C6B";
    </script>
    <h4>
        <?php if($studentView) { ?> 
        <span class="orange">This is Student View. </span>
        <span id='studentView'>
            <a href='#' id='iplus' onclick='FormIt({act:"changeView"},"<?= Page::getRealURL("Classroom")?>"); return false;'>Instructor View<i class='fa fa-undo fa-lg'></i></a>
        </span>
        <?php } ?>
    </h4>
    <div id="userComments" class='ui-widget ui-widget-content'>

        <div id='commentTable'>
            <?php GenerateCommentsTable($comments, $sessionId, $instructor, $userrates, $studentView); ?>
        </div>
        <div id='addComment'>
            <a href='#' id='iplus' onclick='ClassroomSwitchToAddComment(<?= $sessionId ?>); return false;'>
                <i class="fa fa-plus green"></i>Comment</a>
        </div><!-- Add Comment -->
        <?php if ( $instructor ){ ?> 
            <span id='quizLink'>
                <a href='#' id='iplus' onclick='ClassroomSwitchToAddQuiz(<?= $sessionId ?>); return false;'><i class='fa fa-plus green'></i>Questionnaire</a><!-- $("#AddQuizDialog").dialog("open"); return false; -->
            </span>
        <?php } ?>
        <div id='ShowQuizDialog' qopenid="0"></div>
        <script>
            session = <?php echo $sessionId; ?>;
            quizzes = new Array();
            takingQuiz = false;
            ins = <?php echo $instructor?"true":"false"; ?>;
            $("#ShowQuizDialog").hide().dialog({
                    autoOpen: false,
                    buttons: { Close: function () { $(this).dialog("close"); } },
                    dialogClass: "no-close-button",
                    hide: { effect: "slide", duration: 200, direction: "down" },
                    show: { effect: "slide", duration: 200, direction: "down" },
                    modal: false,
                    resizable: false,
                    draggable: true,
                    position: { my: "center", at: "center", of: "body" },
                    width: 300,
                    title: "View a Questionnaire",
                    open: function () {
                        getQuizzes(<?php echo $sessionId; ?>);
                    },
                    close: function () {
                        $("#ShowQuizDialog").attr("qopenid",0);
                    }
                }
            );
            getQuizzes(<?php echo $sessionId; ?>);
        </script>
    </div><!-- userComments -->

</div><!-- classroom -->