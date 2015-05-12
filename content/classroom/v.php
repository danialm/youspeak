<?php

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
        <?php if(isset($_SESSION['studentView']) && $_SESSION['studentView']){ ?>
            $('#studentView').attr('title', 'Back to instructor view');
            $('#studentView a').attr('style', 'color: #7E7E7E; text-shadow: 0px 0px 4px white');
        <?php }else{?>
            $('#studentView').hide();
        <?php }?>
    });
<?php } ?>
var quizzesAnswered = <?= json_encode($quizzesAnswered) ?>;
var width = $(window).width();
$( window ).resize(function(){
    width = $(window).width();
});
window.showHiddenComments = false;
window.showAddressedComments = false;
window.UpdateCommentsEvent = function (){
    UpdateSessionComments( <?php echo $sessionId; ?>, width );
    getQuizzes(<?php echo $sessionId; ?>);
};
var interval = setInterval(UpdateCommentsEvent,<?= getenv("RELOAD_CALSSROOM") ?>);
$('body').on('chardinJs:start', function() {
    window.clearInterval(interval);
});
$('body').on('chardinJs:stop', function() {
    interval = setInterval(UpdateCommentsEvent,<?= getenv("RELOAD_CALSSROOM") ?>);
});

$(".right-side").css("top","5px");

</script>
<style> body { background-color: black; background-image: none; } div#content{padding: 3px 5px}</style>

<div id="classroom">    
    <div id='confirmation' title='Are You Sure?'></div>
    <script>
        $("#confirmation").dialog({autoOpen:false});
        $("#confirmation").dialog("option","resizable",false);
        $("#confirmation").dialog("option","modal",true);
        $("#heading").hide();
    </script>
    
    <div id="userComments" class='ui-widget ui-widget-content'>
        <div id='commentTable'>
            <?php  GenerateCommentsTable($comments, $sessionId, $instructor, $userrates, false ,false, $studentView, false, false, false); ?>
        </div>
        <!--<div>-->
            <a class="button" href='#' onclick='window.showAddressedComments = !window.showAddressedComments; faClassToggle(this) ; return false;' data-intro="Toggle addressed comments" data-position="bottom">
                <i class="fa fa-toggle-off fa-lg green"></i>Addressed comments</a>
        <!--</div>-->
        <?php if ($instructor){?>
        <!--<div>-->
            <a class="button" href='#' onclick='window.showHiddenComments = !window.showHiddenComments; faClassToggle(this) ; return false;' data-intro="Toggle hidden comments" data-position="bottom">
                <i class="fa fa-toggle-off fa-lg green"></i>Hidden comments</a>
        <!--</div>-->
        <div>
            <span id='classroom-quiz-link'>
                <a class="button" href='#' onclick='showEditQuiz(); return false;'><i class='fa fa-plus fa-lg green'></i>Questionnaire</a>
            </span>
            <?php } ?>
            <span id='classroom-comment-link'>
                <a class="button" href='#' onclick='ClassroomReply(); return false;'><i class='fa fa-plus fa-lg green'></i>Comment</a>
            </span>
        </div>
        <div id='ShowQuizDialog' qopenid="0"></div>
        <script>
            session = <?php echo $sessionId; ?>;
            quizzes = new Array();
            takingQuiz = false;
            ins = <?php echo $instructor?"true":"false"; ?>;
            $("#ShowQuizDialog").hide().dialog({
                    autoOpen: false,
                    buttons: { 
                    },
                    dialogClass: "no-close-button",
                    hide: { effect: "slide", duration: 200, direction: "down" },
                    show: { effect: "slide", duration: 200, direction: "down" },
                    modal: false,
                    resizable: false,
                    draggable: true,
                    position: { my: "top", at: "top", of: "#navigation" },
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
        $(document).ready(function(){
            getQuizzes(<?php echo $sessionId; ?>);
        });
        </script>
    </div>
    <div id="AddQuizDialog" data-intro="Design and post or save your question" data-position="right"></div>
    <script>
        $("#AddQuizDialog").hide()
            .dialog({
                autoOpen: false,
                buttons: {
                    Post:    function () { 
                        var re = ValidateAddQuiz(<?php echo $sessionId; ?>, false); 
                        if(re)
                            $(this).dialog("close"); 
                    },
                    Save: function(){
                        var re = ValidateAddQuiz(<?php echo $sessionId; ?>, true); 
                        if(re)
                            $(this).dialog("close"); 
                    },
                    Cancel: function () { $(this).dialog("close"); }
                },
                dialogClass: "no-close-button",
                hide: { effect: "slide", duration: 200, direction: "down" },
                show: { effect: "slide", duration: 200, direction: "down" },
                modal: false,
                resizable: false,
                draggable: true,
                position: { my: "top", at: "top", of: "#navigation" },
                width: 300,
                title: "Add a Questionnaire"
        });
        $("#AddQuizDialog div").buttonset();
    </script>
    <div id="reply" data-intro="Post a comment" data-position="bottom"></div>
    <script>
        $("#reply").hide()
            .dialog({
                autoOpen: false,
                dialogClass: "no-close-button",
                hide: { effect: "slide", duration: 200, direction: "down" },
                show: { effect: "slide", duration: 200, direction: "down" },
                modal: false,
                resizable: false,
                draggable: true,
                position: { my: "top", at: "top", of: "#navigation" },
                width: 300,
                title: "Reply"
        });
    </script>
</div>
<div id="saved_quizzes" onclick="$(this).toggleClass('open').children('i').toggleClass('fa-caret-left').toggleClass('fa-caret-right'); return false;"></div>
<!-- classroom -->