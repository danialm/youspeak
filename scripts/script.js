EditsWhenReady = null;
function ValidateLogin()
{
    var ERRMSG_EMPTY_FIELDS = "One or more fields were left empty.";

    var form = document.forms["login"];
    var fields = form.elements;
    var emptyFields;
    
    // Check Required Fields
    for (var i=0; i<form.length; i++)
    {
        var fieldName = fields[i].name;
        var fieldValue = fields[i].value;
        var fieldLabel = document.getElementById("label_"+fieldName);
        
        if (fieldValue == "")
        {
            if (window.originalColor == null)   // save the original color
                window.originalColor = fieldLabel.style.color;
                
            fieldLabel.style.color = "Red";
            emptyFields = true;
        }
        else if (fieldLabel != null && window.originalColor != null)
            fieldLabel.style.color = window.originalColor;
    }
    
    // action
    if (emptyFields)
    {
        var errorMsg = "";
        if (emptyFields) errorMsg += ERRMSG_EMPTY_FIELDS;
        alert(errorMsg);
        return false;
    }
    
    return true;
}

function FormIt (obj,url)
{
    if (!url) url = "";
    
    var html = "<form method='POST' action='"+url+"'>";
    for (key in obj)
    {
        var val = obj[key];
        html += "<input type='hidden' name='"+key+"' value='"+val+"' />";
    }
    html += "</form>";
    document.write(html);
    $("form")[0].submit();
}

function pickDate (courseId)
{
    var dpick = $("<div id='dpick'></div>").datepicker();
    var dialog = $("<div></div>").dialog(
    {
        title: "Session Date",
        modal: true,
        resizable: false,
        draggable: false,
        width: 350,
        dialogClass: "no-close-button",
        show: "scale",
        hide: "scale",
        close: function () { $(this).dialog("destroy"); },
        buttons: {Create: function () { $(document.forms['addses'+courseId]).append("<input type='hidden' name='utime' value='"+(dpick.datepicker("getDate").valueOf()/1000)+"' />").submit(); }, Cancel: function () { $(this).dialog("close"); }}
    }).append(dpick);
}

function ValidateRegistration ()
{
    var ERRMSG_EMPTY_FIELDS = "One or more fields were left empty. ";
    var ERRMSG_DIFFERENT_PASSWORDS = "The re-entered password did not match the first.";
    
    var form = document.forms["register"];
    var fields = form.elements;
    var emptyFields;
    var differentPasswords;
    var passOneElem;
    var passOneLabel;
    var passTwoElem;
    var passTwoLabel;

    // Check Required Fields
    for (var i=0; i<form.length; i++)
    {
        var fieldName = fields[i].name;
        var fieldValue = fields[i].value;
        var fieldLabel = document.getElementById("label_"+fieldName);
        
        // optional input
        if (fieldName == "instructor") continue;
        if (!fieldLabel) continue;
        
        if (fieldValue == "" || fieldValue == null || (fieldName=="institute" && fields[i].selectedIndex==0))
        {
            if (window.originalColor == null)   // save the original color
                window.originalColor = fieldLabel.style.color;
                
            fieldLabel.style.color = "Red";
            emptyFields = true;
        }
        else if (fieldLabel != null && window.originalColor != null)
            fieldLabel.style.color = window.originalColor;
        
        // hold on to password fields for validation
        if (fieldName == "passone")
        {
            passOneLabel = fieldLabel;
            passOneElem = fields[i];
        }
        if (fieldName == "passtwo")
        {
            passTwoLabel = fieldLabel;
            passTwoElem = fields[i];
        }
    }

    // validate passwords are repeated
    if ( passOneElem.value != passTwoElem.value )
    {
        passOneLabel.style.color = "Red";
        passOneElem.value = "";
        
        passTwoLabel.style.color = "Red";
        passTwoElem.value = "";
        
        differentPasswords = true;
    }
    
    // action
    if (emptyFields || differentPasswords)
    {
        var errorMsg = "";
        if (emptyFields) errorMsg += ERRMSG_EMPTY_FIELDS;
        if (differentPasswords) errorMsg += ERRMSG_DIFFERENT_PASSWORDS;
        alert(errorMsg);
        return false;
    }
    
    
    // return true;
}

function ValidateAddCourse()
{
    var ERRMSG_EMPTY_FIELDS = "The text field was left blank.";

    var form = document.forms["addCourse"];
    var fields = form.elements;
    var emptyFields;
    
    // Check Required Fields
    for (var i=0; i<form.length; i++)
    {
        var fieldName = fields[i].name;
        var fieldValue = fields[i].value;
        
        if (fieldValue == "")
            emptyFields = true;
    }
    
    // action
    if (emptyFields)
    {
        var errorMsg = "";
        if (emptyFields) errorMsg += ERRMSG_EMPTY_FIELDS;
        alert(errorMsg);
        return false;
    }
    
    return true;
}

function ValidateAddComment(sessionId)
{
    var ERRMSG_EMPTY_FIELDS = "The text field was left blank.";

    var form = document.forms["formCom"];
    var fields = form.elements;
    var emptyFields;

    // Check Required Fields
    for (var i=0; i<form.length; i++)
    {
        var fieldName = fields[i].name;
        var fieldValue = fields[i].value;
        
        if (fieldValue == "")
            emptyFields = true;
    }
    
    // action
    if (emptyFields)
    {
        var errorMsg = "";
        if (emptyFields) errorMsg += ERRMSG_EMPTY_FIELDS;
        alert(errorMsg);
    }
    else
    {
        AddComment(form.comment.value,sessionId);
        form.addbutton.disable = true;
        ClassroomCancelAddComment();
    }
    
    return false;
}

function MobValidateAddComment(sessionId)
{
    var ERRMSG_EMPTY_FIELDS = "The text field was left blank.";

    var form = document.forms["formCom"];
    var fields = form.elements;
    var emptyFields;

    // Check Required Fields
    for (var i=0; i<form.length; i++)
    {
        var fieldName = fields[i].name;
        var fieldValue = fields[i].value;
        
        if (fieldValue == "")
            emptyFields = true;
    }
    
    // action
    if (emptyFields)
    {
        var errorMsg = "";
        if (emptyFields) errorMsg += ERRMSG_EMPTY_FIELDS;
        alert(errorMsg);
    }
    else
    {
        AddComment(form.comment.value,sessionId);
        form.addbutton.disable = true;
        ClassroomCancelAddComment();
    }
    
    return false;
}

function ValidateAddPresent(sessionId)
{
    var ERRMSG_EMPTY_FIELDS = "The text field was left blank.";

    var form = document.forms["formPres"];
    var fields = form.elements;
    var emptyFields;
    var name;
    var url;
    
    // Check Required Fields
    for (var i=0; i<form.length; i++)
    {
        var fieldName = fields[i].name;
        var fieldValue = fields[i].value;
        
        if (fieldValue == "")
            emptyFields = true;
            
        else if (fieldName == "name")
            name = fieldValue;
        
        else if (fieldName == "url")
            url = fieldValue;
    }

    // action
    if (emptyFields)
    {
        var errorMsg = "";
        if (emptyFields) errorMsg += ERRMSG_EMPTY_FIELDS;
        alert(errorMsg);
    }
    else
    {
        AddPresent(sessionId,name,url);
        ClassroomCancelAddPresent();
    }
    
    return false;
}

function DisaShow(caller)
{
    var ele = caller.form.disability;
    
    if (caller.value == "yes")
        ele.disabled = false;
        
    else
        ele.disabled = true;
}

function AreYouSure (dialog, func, param)
{
    var yes = function ()
    {
        if (!func)
            $("#confirmation").dialog("close");
        
        else if (!param)
        {
            if (func.name)
            {
                func.submit();
            }
            else func();
            
            $("#confirmation").dialog("close");
        }
        
        else
        {
            func(param);
            $("#confirmation").dialog("close");
        }
    }
    
    var no = function ()
    {
        $("#confirmation").dialog("close");
    }
    
    $("#confirmation").dialog("option","show","bounce");
    $("#confirmation").html(dialog);
    $("#confirmation").dialog("option","buttons",
    [
        {text:"Yes", click: yes},
        {text:"No", click: no}
    ]);
    $("#confirmation").dialog("open");
}

function ChooserSwitchToAddCourse ()
{
    var elem = document.getElementById("addCourse");
    var str = "";
    
    str += "<form name='addCourse' onsubmit='return ValidateAddCourse()' action='' method='post'>";
    str +=      "<input type='hidden' name='act' value='add_course' />";
    str +=      "<input type='text' name='courseName' placeholder='Name of the new course.' />";
    str +=      "<select name='term'><option value='' selected='selected' disabled='disabled'>Term</option><option value='fa'>Fall</option><option value='su'>Summer</option><option value='sp'>Spring</option><option value='wi'>Winter</option></select>";
    str +=      "<input type='text' name='year' placeholder='YYYY' style='width:4em' />";
    str +=      "<input type='submit' value='Add' />";
    str +=      "<input type='button' value='Cancel' onclick='ChooserCancelAddCourse()' />";
    str += "</form>";
    
    window.originalAddCourse = elem.innerHTML;
    elem.innerHTML = str;
    document.addCourse.courseName.focus();
}

function ChooserCancelAddCourse ()
{
    var ele = document.getElementById("addCourse");
    ele.innerHTML = window.originalAddCourse;
}

function ClassroomAddQuiz (sessionId)
{
    var name = $("#AddQuizDialog input:text").val();
    var numOptions = $("#AddQuizDialog select").val();
    var open = $("#AddQuizDialog div :radio:checked").attr("value");
    
    addQuiz(sessionId, name, numOptions, open);
}

function UpdateQuizState ()
{
    var dialog = $("#ShowQuizDialog");
    $.each(quizzes, function (i,q)
    {
        var id = q.form.id;
        var open = q.form.open;
        
        // check if the quiz is open
        if ( dialog.attr("qopenid") == id )
        {
            // if instructor just refresh the quiz dialog
            if (ins)
                ShowQuiz(id);
           
           // if student, only refresh if quiz is closed
            if (!ins && !open)
            {
                dialog.html("This questionnaire has been closed.");
                dialog.dialog("option","buttons",{ Close: function () {dialog.dialog("close");} });
            }
        }
        
        // if student and there is an active quiz to take, pop it up (unless its been answered already)
        if ( !ins && (dialog.attr("qopenid")==0) && open && ($.inArray(String(id),quizzesAnswered)==-1) )
        {
            ShowQuiz(id);
        }
        
        // if instructor and there is an active quiz, pop it up
        if ( ins && (dialog.attr("qopenid")==0) && open==1 )
            ShowQuiz(id);
    });
}

function decideOnQuiz (answered)
{
    var needToTake = new Array();
    
    $.each(quizzes,function(i,e){
        if ( $.inArray(e.form.id,answered) == -1 )
            needToTake.push(e);
    });
    
    var res = null;
    if (needToTake.length)
        res = needToTake[0].form.id;
        
    return res;
}

function ShowQuiz (quiz)
{

    if ( !(quiz in quizzes) )
        return false;
        
    var html = "";
    var choices = ["A","B","C","D","E","F"];
    var q = quizzes[quiz];
    var buttons;
    
    $("#ShowQuizDialog").dialog("option","title",q.form.name);
    
    if (ins)
    {
        var oChecked = q.form.open==1?"checked":"";
        var cChecked = q.form.open==1?"":"checked";
    
        html += "<div>";
        html += "<input id='modopen'  value=1 type='radio' name='modopen' "+oChecked+" /><label for='modopen' >Active</label>";
        html += "<input id='modclose' value=0 type='radio' name='modopen' "+cChecked+" /><label for='modclose'>Inactive</label>";
        html += "</div>";
        
        var total = 0;
        for (i=0; i<q.form.num_options; i++)
            total += parseInt(q.choices[ choices[i] ]);
        
        if (cChecked)
        {
        
            html += "<center><table id='showAnswers' cellspacing=0>";
            html += "<tr><th>Choice</th><th>Count</th><th>%</th></tr>";
            
            total = total>0?total:1;
            
            for (i=0; i<q.form.num_options; i++)
            {
                var count = q.choices[ choices[i] ];
                var perc = 100*count/total;
                perc = Math.round(perc);
                html += "<tr><td>"+choices[i]+"</td><td>"+count+"</td><td style='text-align:right'>("+perc+"%)</td></tr>";
            }
            html += "</table></center>";
        }
        else
        {
            html += "<h4>"+ q.form.name +" is active.</h4>";
            html += "<p>"+total+ (total==1?" has ":" have ")+"answered.</p>";
        }
        
        buttons = {
            Close: function () {
                takingQuiz = false;
                $("#ShowQuizDialog").dialog("close");
            }
        };
        
        $("#ShowQuizDialog").html(html).dialog("option", "buttons", buttons);
        $("#ShowQuizDialog div").buttonset();
        
        $("#ShowQuizDialog :radio").click(function (e)
        {
            var open = $(e.target).attr("value")==1;

            OpenQuiz(quiz,open);
        });
    }
    else
    {
        html += "<div>";
        
        for (i=0; i<q.form.num_options; i++)
            html += "<input id='rb"+i+"' answer="+(i+1)+" type='radio' name='quiz' /><label for='rb"+i+"'>"+choices[i]+"</label>";
        
        html += "</div>";
        
        buttons = {
            Submit: function () {
                var answer = $("#ShowQuizDialog :radio:checked").attr("answer");
                SubmitQuizAnswer(answer,quiz);
            },
            Cancel: function () {
                takingQuiz = false;
                $("#ShowQuizDialog").dialog("close");
            }
        };
        
        $("#ShowQuizDialog").html(html).dialog("option", "buttons", buttons);
        $("#ShowQuizDialog div").buttonset();
    }
    
    if ( $("#ShowQuizDialog").attr("qopenid") != quiz )
    {
        $("#ShowQuizDialog")
            .dialog("option","hide",{effect:"null"})
            .dialog("close")
            .dialog("option","hide",{effect:"slide",direction:"down",duration:200})
            .dialog("open")
            .attr("qopenid",quiz);
    }
    else
    {
        // reposition
        $("#ShowQuizDialog").dialog("option","position",{ my: "right bottom", at: "right bottom", of: "#classroom" });
    }
}

function ClassroomSwitchToAddComment (sessionId)
{
    var ele = document.getElementById("addComment");
    var str = "";
    
    str += "<form name='formCom'>";
    str += "<textarea style='width: 100%; box-sizing: border-box;' rows=5 name='comment' placeholder='Type your comment or question here.' ></textarea><br />";
    str += "<input type='button' name='addbutton' onclick='ValidateAddComment("+sessionId+")' value='Add' />";
    str += "<input type='button' value='Cancel' onclick='ClassroomCancelAddComment();' />";
    str += "</form>";
    
    window.originalAddComment = ele.innerHTML;
    ele.innerHTML = str;
    document.formCom.comment.focus();
}

//Depricated because no one can edit any comment
//function ClassroomSwitchToEditComment (commentId, sessionId, mobile)
//{
//    var cell = $("#cid"+commentId);
//    var comment = $("#cid"+commentId+" p").html();
//    
//    var editHtml = "";
//    editHtml += "<textarea id='editcom"+commentId+"'>";
//    editHtml += comment;
//    editHtml += "</textarea>";
//    editHtml += "<input id='submitEditCom"+commentId+"' type='button' value='Submit Change' />";
//    editHtml += "<input id='cancelEditCom"+commentId+"' type='button' value='Cancel' />";
//    
//    cell.html(editHtml);
//    
//    $("#submitEditCom"+commentId).click( function () {
//        var newComment = $("#editcom"+commentId)[0].value;
//        
//        EditComment(newComment, commentId, sessionId, mobile);
//        window.updateCommentsEvent = setInterval(window.UpdateCommentsEvent,10000);
//    });
//    
//    $("#cancelEditCom"+commentId).click( function () {
//        cell.html("<p>"+comment+"</p>");
//        $("#cid"+commentId+" p").click(function () {
//            ClassroomSwitchToEditComment(commentId, sessionId, mobile);
//        });
//        window.updateCommentsEvent = setInterval(window.UpdateCommentsEvent,10000);
//    });
//    
//    clearInterval(window.updateCommentsEvent);
//}

function ClassroomCancelAddComment ()
{
    var ele = document.getElementById("addComment");
    ele.innerHTML = window.originalAddComment;
}

function ClassroomShowOrHideComments (flagtype)
{
    var elems = document.getElementsByTagName('*'), i;
    var matchClass = flagtype+"Comment";
    for (i in elems) {
        if ((' ' + elems[i].className + ' ').indexOf(' ' + matchClass + ' ') > -1)
        {
            if (elems[i].style.display == "none" || elems[i].style.display == "")
            {
                elems[i].style.display = "table-row";
                var h = $("#showOrHideBut"+flagtype+" a").html();
                $("#showOrHideBut"+flagtype+" a").html(h.replace("Show","Hide"));
            }
            else
            {
                elems[i].style.display = "none";
                var h = $("#showOrHideBut"+flagtype+" a").html();
                $("#showOrHideBut"+flagtype+" a").html(h.replace("Hide","Show"));
            }
        }
    }
    
    if (window["hide"+flagtype+"Comments"] == true)
        window["hide"+flagtype+"Comments"] = false;
    else
        window["hide"+flagtype+"Comments"] = true;
}

function getQuizzes (sessionId)
{
    if ( !sessionId ) sessionId = lastSId;
    else              lastSId = sessionId;
    
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.ajax({
        type: "POST",
        url: url,
        data: {
            act: "get_quizzes",
            sessionId: sessionId
        },
        success: function (data) {
            quizzes = data;
            UpdateQuizState();
        },
        error: function (j,t,e) { console.log("Quiz Request Cancelled"); },
        dataType: "json"
    });
}

function addQuiz (sessionId,name,numOptions,open)
{
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.ajax({
        type: "POST",
        url: url,
        data: {
            act: "add_quiz",
            sessionId: sessionId,
            name: name,
            numOptions: numOptions,
            open: open
        },
        success: function (data) {
            quizzes = data['quizzes'];
            ShowQuiz( data['lastquiz'] );
        },
        error: function (j,t,e) { console.log("Add Quiz Cancelled"); },
        dataType: "json"
    });
}

function removeQuiz (quizId)
{
    var removeFunc = function (quizId)
    {
        var url = NO_REWRITE?"?p=Classroom":"Classroom";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                act: "remove_quiz",
                quizId: quizId
            },
            success: function () {
                getQuizzes();
                $("#ShowQuizDialog").dialog("close");
            },
            error: function (j,t,e) { console.log("Remove Quiz Cancelled"); }
        });
    }
    $("<div></div>").dialog(
    {
        buttons: 
        { 
            Yes: function () 
            {
                removeFunc(quizId);
                $(this).html("Deleted");
                $(this).dialog("option","buttons",{Close:function(){$(this).dialog("close").dialog("destroy");}});
            },
            No:  function () { $(this).dialog("close").dialog("destroy"); } 
        },
        dialogClass: "no-close-button",
        show: { effect: "shake" },
        modal: true,
        resizable: false,
        draggable: false,
        width: 400,
        title: "Delete Questionnaire"
    }).html("Delete Questionnaire: "+quizzes[quizId].form.name+"?");
}

function SubmitQuizAnswer (answer, quiz)
{
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.ajax({
        type: "POST",
        url: url,
        data: {
            act: "submit_quiz",
            answer: answer,
            quiz: quiz
        },
        success: function (data) {
            var html = "<h3> Your selection was submitted. </h3>";
            quizzesAnswered.push(quiz);
            var buttons = {
                Close: function () {
                    $("#ShowQuizDialog").dialog("close");
                }
            };
            $("#ShowQuizDialog").html(html).dialog("option","buttons",buttons);
        }
    });
}

function OpenQuiz (quiz,open)
{
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.ajax({
        type: "POST",
        url: url,
        data: {
            act: "open_quiz",
            quiz: quiz,
            open: open
        },
        success: function (data)
        {
            var onum = open?"1":"0";
            quizzes[quiz].form.open = onum;
        },
        dataType: "json"
    });
}

function FlagComment (commentId, flagId)
{
    var vars = {
        act: "flag_comment",
        commentId: commentId,
        flagId: flagId
    };
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.post(url,vars,function(){window.UpdateCommentsEvent();});
}

function RateUp (commentId)
{
    var vars = {
        act: "add_rating",
        commentId: commentId,
        up: 1
    };
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.post(url,vars,function(){window.UpdateCommentsEvent();});
}

function RateDown (commentId)
{
    var vars = {
        act: "add_rating",
        commentId: commentId,
        down: 1
    };
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.post(url,vars,function(){window.UpdateCommentsEvent();});
}

function RemoveComment (commentId)
{
    var vars = {
        act: "remove_comment",
        commentId: commentId
    };
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.post(url,vars,function(){window.UpdateCommentsEvent();});
}

function URLSafe (str)
{
    var newStr = "";
    newStr = str.replace("&","%26");
    newStr = str.replace("=","%3D");
    newStr = str.replace("?","%3F");
    newStr = str.replace(" ","%20");
    
    return newStr;
}

function AddComment (comment, sessionId, mobile)
{
    var vars = {
        act: "add_comment",
        comment: comment,
        sessionId: sessionId
    };
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.post(url,vars,function(){window.UpdateCommentsEvent();});
}

function EditComment (comment, commentId, sessionId, mobile)
{
    var vars = {
        act: "add_comment",
        commentId: commentId,
        comment: comment
    };
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.post(url,vars,function(){window.UpdateCommentsEvent();});
}

function UpdateSessionComments (sessionId,mobile)
{
    var vars = {
        act: "update_comments",
        sessionId: sessionId,
        mobile: mobile?1:null
    };
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.post(url,vars,function (res)
    {   
        var eleSect = document.getElementById("commentTable");
            
        eleSect.innerHTML = res;
        
        if (!window.hideaddressedComments)
        {
            ClassroomShowOrHideComments("addressed");
            window.hideaddressedComments = false;
        }
        
        if (!window.hidehiddenComments)
        {
            ClassroomShowOrHideComments("hidden");
            window.hidehiddenComments = false;
        }
        
        $("#mobclass").trigger('create');
        if (EditsWhenReady) EditsWhenReady();
    });
}

function MaximizeDisplay ()
{
    var dispEle = $("#displayBox")[0];
    window.origDispWidth = dispEle.style.width;
    window.origDispLeft = dispEle.style.left;
    window.origDispRight = dispEle.style.right;
    window.origDispHeight = dispEle.style.height;
    window.origDispPosition = dispEle.style.position;
    
    dispEle.style.position = "absolute";
    dispEle.style.left = "0";
    dispEle.style.right = "0";
    dispEle.style.width = "100%";
    dispEle.style.height = "100%";
}

function RestoreDisplay ()
{
    var dispEle = $("#displayBox")[0];
    dispEle.style.position = window.origDispPosition;
    dispEle.style.width = window.origDispWidth;
    dispEle.style.left = window.origDispLeft;
    dispEle.style.right = window.origDispRight;
    dispEle.style.height = window.origDispHeight;
}

function toggleAddSessionForm(courseId){
    var today = $.datepicker.formatDate('yy-mm-dd', new Date());
    $('.addses'+courseId).toggle();
    $('.addses'+courseId).find("input[type=date]").attr("value",today);
    return false;
}
function toggleEditCourseForm(courseId){
    $('.course'+courseId).toggle();
    return false;
}