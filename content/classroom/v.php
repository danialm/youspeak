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

?>

<script>
session = <?= $sessionId ?>;    
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
var updateCommentsEvent = setInterval(UpdateCommentsEvent,10000);

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

    <div id="userComments" class='ui-widget ui-widget-content'>

        <div id='commentTable'>
            <?php GenerateCommentsTable($comments, $sessionId, $instructor, $userrates); ?>
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
            quizzes = new Array();
            takingQuiz = false;
            ins = <?php echo $_SESSION['isInstructor']?"true":"false"; ?>;
            $("#ShowQuizDialog").hide().dialog({
                    autoOpen: false,
                    buttons: { Close: function () { $(this).dialog("close"); } },
                    dialogClass: "no-close-button",
                    hide: { effect: "slide", duration: 200, direction: "down" },
                    show: { effect: "slide", duration: 200, direction: "down" },
                    modal: false,
                    resizable: false,
                    draggable: false,
                    position: { my: "right bottom", at: "right bottom", of: "#classroom" },
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