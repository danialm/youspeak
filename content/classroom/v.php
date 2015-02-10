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
UpdateCommentsEvent;
var updateCommentsEvent = setInterval(UpdateCommentsEvent,1000);

$(".right-side").css("top","5px");

//var setSize = function(){
//    var width = $(window).width()-80;
//    $('#commentTable ul li div:nth-child(2) p').width(width).show();
//};
//setSize = setInterval(setSize,1);
</script>
<style> body { background-color: black; background-image: none; } </style>
<div id="saved_quizzes" onclick="$(this).toggleClass('open').children('i').toggleClass('fa-caret-left').toggleClass('fa-caret-right'); return false;"></div>
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
            <?php  GenerateCommentsTable($comments, $sessionId, $instructor, $userrates, false ,false, $studentView, false, false, false); ?>
        </div>
        <div>
            <a href='#' onclick='window.showAddressedComments = !window.showAddressedComments; faClassToggle(this) ; return false;'>
                <i class="fa fa-toggle-off fa-lg green"></i>Addressed comments</a>
        </div>
        <?php if ($instructor){?>
        <div>
            <a href='#' onclick='window.showHiddenComments = !window.showHiddenComments; faClassToggle(this) ; return false;'>
                <i class="fa fa-toggle-off fa-lg green"></i>Hidden comments</a>
        </div>
        <?php } ?>
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
                    position: { my: "top", at: "top", of: "#container" },
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
    <div id="AddQuizDialog"></div>
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
                position: { my: "top", at: "top", of: "#classroom" },
                width: 300,
                title: "Add a Questionnaire"
        });
        $("#AddQuizDialog div").buttonset();
    </script>
    <div id="reply"></div>
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
                position: { my: "top", at: "top", of: "#classroom" },
                width: 300,
                title: "Reply"
        });
    </script>
</div><!-- classroom -->