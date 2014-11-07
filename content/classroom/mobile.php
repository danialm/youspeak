<?php

global $userInfo;
global $sessionInfo;
global $courseInfo;
global $comments;
global $userrates;
global $roleInCourse;
global $presents;

global $quizzesAnswered;

global $sessionId;
global $instructor;


//print_r($quizzesAnswered); exit;?>
<div data-role="page" id="mobclass">
    <script>
        var takingQuiz = false;
        var quizzesAnswered = <?php echo json_encode($quizzesAnswered); ?>;
    
        window.hideaddressedComments = true;
        window.hidehiddenComments = true;
        window.UpdateCommentsEvent = function ()
        {
            UpdateSessionComments( <?php echo $sessionId; ?>, true );
            getQuizzes(<?php echo $sessionId; ?>);
            
            if (!takingQuiz)
            {
                var takeQuiz = decideOnQuiz(quizzesAnswered);
                if (takeQuiz)
                    mobileShowQuiz(takeQuiz);
            }
        }
        var updateCommentsEvent = setInterval(UpdateCommentsEvent,1000);
        $.mobile.transitionFallbacks.flip = "none";
        
        quizzes = new Array();
        function mobileShowQuiz (qid)
        {
            takingQuiz = true;
            
            var name = quizzes[qid].form.name;
            var options = quizzes[qid].form.num_options;
            
            $("#quizDialog #title").html(name);
            
            var buttons = "";
            buttons += "<fieldset data-role='controlgroup'>";
            for (i=0; i<options; i++)
            {
                var letter = String.fromCharCode(65+i);
                buttons += "<input data-theme='a' id='q"+qid+"i"+i+"' value="+(i+1)+" type='radio' name='q"+qid+"' />";
                buttons += "<label for='q"+qid+"i"+i+"'>"+letter+"</label>";
            }
            buttons += "</fieldset>";
            
            buttons += "<br/><br/>";
            
            buttons += "<a data-role='button' data-theme='b' href='#' onclick='mobileSubmitQuiz(); return false;'>Submit</a>";
            
            $("#quizDialog #content").html(buttons);
            
            $.mobile.changePage("#quizDialog");
        }
        
        function mobileSubmitQuiz ()
        {
            var answer = $(":radio:checked");
            if (answer.length == 0) return;
            
            var quiz = answer.attr("name").substring(1);
            answer = answer.val();
            
            var url = "<?php echo Page::getRealURL(); ?>";
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    act: "submit_quiz",
                    answer: answer,
                    quiz: quiz
                },
                success: function (data) {
                    var html = "Your selection was submitted.";
                    html += "<br/><br/>";
                    html += "<a href='"+url+"' data-role='button' data-ajax='false' data-theme='b'>Back</a>";
                    $("#quizDialog #content").html(html);
                    $("#quizDialog").trigger('create');
                    quizzesAnswered.push(quiz);
                }
            });
        }
        
    </script>
    <div data-theme="b" data-role="header" data-position="fixed">
        <h2>
            YouSpeak
        </h2>
        <a data-icon='arrow-1' data-ajax=false data-theme="b" 
                data-transition='slidedown' data-role='button' 
                class="ui-btn-left" href='<?php echo Page::getRealURL("Courses"); ?>'>
            Back
        </a>
        <a data-theme="b" data-role='button' class="ui-btn-right" href='<?php echo Page::getRealURL("Login"); ?>'>
            Log Out
        </a>
    </div>
    <div data-role="content">
        <div id='commentTable'>
            <?php GenerateCommentsTable($comments,$sessionId,$instructor,$userrates,true); ?>
        </div>
    </div>
    <div data-theme="b" data-role="footer" data-position="fixed">
        <div id='footcom' data-role="collapsible" data-theme="b" data-content-theme="c">
            <h2>Add a Comment</h2>
            <form name='formCom'>
                <textarea name='comment' placeholder='Enter your comment or question here.'></textarea>
                <a id='addcombut' data-role='button' href='#'>Add</a>
                <a id='cancelcombut' data-role='button' href='#'>Cancel</a>
            </form>
        </div>
        <script>
            
            $("#addcombut").click(function()
            {
                if ($("#footcom form")[0].comment.value.length>0)
                {
                    AddComment($("#footcom form")[0].comment.value,<?php echo $sessionId; ?>,true);
                    $("#footcom form")[0].comment.value="";
                    $("#footcom").trigger("collapse");
                }
            });
            $("#cancelcombut").click(function()
            {
                $("#footcom form")[0].comment.value = "";
                $("#footcom").trigger("collapse");
            });
        </script>
    </div>
</div>
<div data-role="page" id="diatest">
    <div data-role="header">
        <h2>Are You Sure?</h2>
    </div>
    <div data-role="content">
        <p>Remove this comment?</p>
        <a data-role='button' id='yesbox' href="#mobclass">
            Yes
        </a>
        <a data-role='button' href='#mobclass'>
            No
        </a>
    </div>
</div>

<div id='quizDialog' data-role="page">
    <div data-role="header">
        <h2 id='title'>Title</h2>
    </div>
    <div data-role="content" id='content'>
        Content
    </div>
</div>