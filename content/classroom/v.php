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
        $('#studentView').attr('title', 'Back to instructor view');
        $('#studentView a').attr('style', 'color: #7E7E7E; text-shadow: 0px 0px 4px white');
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
    
    <div id="userComments" class='ui-widget ui-widget-content'>

        <div id='commentTable'>
            <?php GenerateCommentsTable($comments, $sessionId, $instructor, $userrates, $studentView); ?>
        </div>
        <div id='addComment'>
            <a href='#' id='iplus' onclick='ClassroomReply(); return false;'>
                <i class="fa fa-plus green"></i>Comment</a>
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
//                        Close: function () { $(this).dialog("close"); } 
                    },
                    dialogClass: "no-close-button",
                    hide: { effect: "slide", duration: 200, direction: "down" },
                    show: { effect: "slide", duration: 200, direction: "down" },
                    modal: false,
                    resizable: false,
                    draggable: true,
                    position: { my: "top", at: "top", of: "#classroom" },
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
    <div id="AddQuizDialog">
            <input type='hidden' name='act' value='add_quiz'>
            <textarea style='width: 100%; box-sizing: border-box;' rows=2 name='question' placeholder='Type your question here.' ></textarea><br />
            Number Of Choices: 
            <select onchange='addOptions();' name='NumberOfChoises'>
                <option value='2'>2</option>
                <option value='3'>3</option>
                <option value='4'>4</option>
                <option value='5'>5</option>
                <option value='6'>6</option>
                <option value='7'>7</option>
                <option value='8'>8</option>
                <option value='9'>9</option>
                <option value='10'>10</option>
            </select>
            <div class='choises' >
                <span>1. <input type='text' name='a' value='a'/></span>
                <span>2. <input type='text' name='b' value='b'/></span>
            </div>
    </div>
    <script>
        $("#AddQuizDialog").hide()
            .dialog({
                autoOpen: false,
                buttons: {
                    Add:    function () { 
                        var re = ValidateAddQuiz(<?php echo $sessionId; ?>); 
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