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

var quizzesAnswered = <?php echo json_encode($quizzesAnswered); ?>;

window.hideaddressedComments = true;
window.hidehiddenComments = true;
window.UpdateCommentsEvent = function ()
{
    UpdateSessionComments( <?php echo $sessionId; ?> );
    getQuizzes(<?php echo $sessionId; ?>);
}
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
                <?php GenerateCommentsTable($comments, $sessionId, $instructor, $userrates); ?>
            </div>
            <div id='addComment'>
                <a href='#' id='iplus'
                    onclick='ClassroomSwitchToAddComment(<?php echo $sessionId; ?>); return false;'>
                    <i class="fa fa-plus green"></i>Comment</a>
            </div><!-- addComment -->
            
            <?php if ($instructor): ?>
                <div id='AddQuizDialog'>
                    <label>
                        <span>Questionnaire Name: </span>
                        <input type='text' />
                    </label>
                    <div>
                        <span>Initial Status: </span>
                        <input type='radio' name='newopen' id='newopen' value=1 checked /><label for='newopen'>Active</label>
                        <input type='radio' name='newopen' id='newclose' value=0 /><label for='newclose'>Inactive</label>
                    </div>
                    <label>
                        <span>Number of Options: </span>
                        <select>
                            <option value=2>2</option>
                            <option value=3>3</option>
                            <option value=4 selected>4</option>
                            <option value=5>5</option>
                        </select>
                    </label>
                </div>
                
                <script>
                    $("#AddQuizDialog").hide()
                        .dialog({
                            autoOpen: false,
                            buttons: {
                                Add:    function () { ClassroomAddQuiz(<?php echo $sessionId; ?>); $(this).dialog("close"); },
                                Cancel: function () { $(this).dialog("close"); }
                            },
                            dialogClass: "no-close-button",
                            hide: { effect: "slide", duration: 200, direction: "down" },
                            show: { effect: "slide", duration: 200, direction: "down" },
                            modal: false,
                            resizable: false,
                            draggable: false,
                            position: { my: "right bottom", at: "right bottom", of: "#classroom" },
                            width: 300,
                            title: "Add a Questionnaire",
                            open: function () {
                                $("#AddQuizDialog input:text").val("Questionnaire");
                                $("#AddQuizDialog select").val(4);
                            }
                    });
                    $("#AddQuizDialog div").buttonset();
                </script>
            <?php endif; ?>
            <div id='ShowQuizDialog' qopenid="0"></div>
            <script>
                quizzes = new Array();
                takingQuiz = false;
                ins = <?php echo $_SESSION['isInstructor']?"true":"false"; ?>;
                $("#ShowQuizDialog").hide()
                    .dialog(
                    {
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
        
        <script>
        /*
            $("#userComments").width( $("#classroom").width() - 5 );
            $("#classroom").add("#userComments").height( $(window).height() * 0.85 - 20 );
            window.onresize = function ()
            {
                $("#classroom").add("#userComments").height( $(window).height() * 0.85 - 20 ); 
            }
            */
        </script>
        
    </div><!-- classroom -->