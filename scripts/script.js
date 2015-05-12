EditsWhenReady = null;
function ValidateLogin(){
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
    
    var html = "<form id='addedForm' method='POST' action='"+url+"'>";
    for (key in obj)
    {
        var val = obj[key];
        html += "<input type='hidden' name='"+key+"' value='"+val+"' />";
    }
    html += "</form>";
    $("body").append(html);
    $("#addedForm").submit();
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
        ClassroomCancelAdd();
    }
    
    return false;
}

function ValidateAddQuiz(sessionId, save){
    
    var open = !save;
    var ERRMSG_EMPTY_FIELDS = "Something was left blank.";

    var form = $("#AddQuizDialog");
    var fields = form.find("input");
    var emptyFields;
    var options = new Array();

    // Check Required Fields
    for (var i=0; i<fields.length; i++){
        var fieldName = fields[i].name;
        var fieldValue = fields[i].value;
        
        if (fieldValue === ""){
            emptyFields = true;
        }
    }
    
    // action
    if (emptyFields){
        var errorMsg = "";
        errorMsg += ERRMSG_EMPTY_FIELDS;
        alert(errorMsg);
        return false;
    }
    else{
        for(var i=2; i<12; i++){
            var temp = $("#AddQuizDialog input[name="+String.fromCharCode(95 + i)+"]").val();
            if(temp && temp.trim()){
                options.push(temp.trim());
            }
        }
        addQuiz(sessionId, form.find("textarea").val(), options.length, options, open, save);
        return true;
    }
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

function AreYouSure (dialog, func, param1, param2)
{
    var yes = function ()
    {
        if (!func)
            $("#confirmation").dialog("close");
        
        else if (!param1)
        {
            if (func.name)
            {
                func.submit();
            }
            else func();
            
            $("#confirmation").dialog("close");
        }
        
        else if (!param2)
        {
            func(param1);
            $("#confirmation").dialog("close");
        }else{
            func(param1, param2);
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

function UpdateQuizState (){

    var dialog = $("#ShowQuizDialog");
    var saveds = [];
    
    if(!ins && quizzes.length===0){ //No active quiz for student 
        dialog.html("Quiz is closed!");
        dialog.dialog("option","buttons",{});
        setTimeout(function(){dialog.dialog("close");}, 2000);
    }
    
    
    $.each(quizzes, function (i,q){
        var id = q.form.id;
        var open = q.form.open;
        
        if ( dialog.attr("qopenid") == id ){// check if the quiz is open
            if (ins)// if instructor just refresh the quiz dialog
                ShowQuiz(id);
        }
           // if student, only refresh if quiz is closed
        if (!ins && !open){
            dialog.html("Quiz is closed!");
            dialog.dialog("option","buttons",{ Close: function () {dialog.dialog("close");} });
        }

        
        // if student and there is an active quiz to take, pop it up (unless its been answered already)
        if ( !ins && (dialog.attr("qopenid")==0) && open && ($.inArray(String(id),quizzesAnswered)==-1) ){
            ShowQuiz(id);
        }
        
        // if instructor and there is an active quiz, pop it up
        if ( ins && (dialog.attr("qopenid")==0) && open==1 )
            ShowQuiz(id);
        
        if(q.form.save == 1){
            saveds.push(id);
        }
    });
    
    showSavedQuizzes(saveds);
}

function showSavedQuizzes(saveds){
    $("#saved_quizzes").empty().hide();
    if($("#saved_quizzes").attr("class") === "open"){
        $("#saved_quizzes").append("<i class='fa fa-caret-right fa-lg' data-intro='Saved questions' data-position='left'></i>");
    }else{
        $("#saved_quizzes").append("<i class='fa fa-caret-left fa-lg' data-intro='Saved questions' data-position='left'></i>");
    }
    $("#saved_quizzes").hide();
    if(saveds.length > 0){
        $("#saved_quizzes").show();
    }
    $.each(saveds, function(i, id){
            $("#saved_quizzes").append("<span onclick='showEditQuiz("+id+")'><i>"+quizzes[id].form.name+"</i></span>");
    }); 
}

function showEditQuiz(id){
    var saved = (typeof id == "undefined") ? false : true;
    
    
    var html ="<input type='hidden' name='act' value='add_quiz'>";
        html+="<textarea style='width: 100%; box-sizing: border-box;' rows=2 name='question' placeholder='Type your question here.' >";
    if(saved)    
        html+= quizzes[id].form.name;
        html+="</textarea><br />";
        html+="Number Of Choices:";  
        html+="<select onchange='addOptions();' name='NumberOfChoises'>";
    for(var i=2; i<11; i++){
        var sel = (saved && i===quizzes[id].options.length)?"select":""; 
        html+="    <option value='";
        html+=i;
        html+="' ";
        html+=sel;
        html+=">";
        html+=i;
        html+="</option>";
    }
        html+="</select>";
        html+="<div class='choises' >";
    var char = 97;
    if(saved){
        for(var index in quizzes[id].options){
            var val = quizzes[id].options[index];
            html+="    <span>";
            html+=index;
            html+=". <input type='text' name='";
            html+=String.fromCharCode(char++);
            html+="' value='";
            html+=val;
            html+="'/></span>";
        }
    }else{
        html+="<span><input tupe='text' name='a' value='a' /></span>";
        html+="<span><input tupe='text' name='b' value='b' /></span>";
    }
        html+="</div>";
    $("#AddQuizDialog").html(html).dialog("open");
    removeQuiz(id);
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
    var choices = ["A","B","C","D","E","F","G","H","I","J"];
    var q = quizzes[quiz];
    var options = q["options"];
    var saved = q.form.save === "1" ? true : false;
    var buttons;
    
    if (saved) return false;
    
    $("#ShowQuizDialog").dialog("option","title","Questionnaire");
    
    if (ins)
    {
        var oChecked = q.form.open==1?"checked":"";
        var cChecked = q.form.open==1?"":"checked";
    
        html += "<div>";
        html += "<input id='modopen'  value=1 type='radio' name='modopen' "+oChecked+" /><label for='modopen' >Active</label>";
        html += "<input id='modclose' value=0 type='radio' name='modopen' "+cChecked+" /><label for='modclose'>Inactive</label>";
        html += "</div>";
        
        var total = 0;
        for (var i=0; i<q.form.num_options; i++)
            total += parseInt(q.choices[ choices[i] ]);
        
        if (cChecked){
            html += "<br><span>Click on the right Answer:</span>";
            html += "<center><table id='showAnswers' cellspacing=0>";
            html += "<tr><th>Choice</th><th>Count</th><th>%</th></tr>";

            total = total>0?total:1;

            for (var i=0; i<q.form.num_options; i++)
            {
                var count = q.choices[ choices[i] ];
                var perc = 100*count/total;
                perc = Math.round(perc);
                html += "<tr class='clickable "+(i+1)+"' onclick='chooseCorrectAnswer("+quiz+","+(i+1)+", \""+q['form']['name']+"\", \""+options[i+1]+"\")' ><td title="+options[i+1]+">"+options[i+1]+"</td><td>"+count+"</td><td style='text-align:right'>("+perc+"%)</td></tr>";
            }
            html += "</table></center>";
            buttons = {
                Close: function () {
                    takingQuiz = false;
                    var text = "Options: ";
                    for(var i=0; i < q.form.num_options-1 ; i++){
                        text += options[i+1];
                        text += " -- ";
                    }
                    text += options[q.form.num_options];
                    AddComment(q['form']['name']+"<br><br>"+text , session);
                    $("#ShowQuizDialog").dialog("close");
                }
            };
        }else{
            html += "<br><p>"+ q.form.name +"<br><b>is active</b></p>";
            html += "<p>"+total+ (total==1?" has ":" have ")+"answered</p>";
            buttons = {};
        }
        
        
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
        html += "<p>"+ q.form.name +"</p>";
        html += "<div>";
        
        for (var i=0; i<q.form.num_options; i++)
            html += "<input id='rb"+i+"' answer="+(i+1)+" type='radio' name='quiz' /><label class='options' for='rb"+i+"' title="+options[i+1]+" onclick='addSubmit(\""+quiz+"\")'>"+options[i+1]+"</label>";
        
        html += "</div>";
        
        buttons = {
            Submit: function () {
                alert("You needt to select an option!");
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

}
function addSubmit(quiz){
    var buttons = {
            Submit: function () {
                var answer = $("#ShowQuizDialog :radio:checked").attr("answer");
                SubmitQuizAnswer(answer,quiz);
            }
        };
    $("#ShowQuizDialog").dialog("option", "buttons", buttons);
}
function chooseCorrectAnswer(quizId, optionNumber, question, option){
    $(".clickable."+optionNumber).css("background", "green");
    var url = NO_REWRITE?"?p=Classroom":"Classroom";
    $.ajax({
        type: "POST",
        url: url,
        data: {
            act: "quiz_correct_answer",
            quizId: quizId,
            optionNumber: optionNumber,
            sessionId: session
        },
        success: function () {
            AddComment(question+"<br><br>Answer: "+option , session);
            takingQuiz = false;
            $("#ShowQuizDialog").dialog("close");
        },
        error: function (j,t,e) { console.log("Add correct answer to quiz cancelled"); },
        dataType: "json"
    });
}
function ClassroomSwitchToAddComment (sessionId){
    var ele1 = $("#addComment");
    var ele2 = $("#quizLink");
    var str = "";
    
    str += "<form name='formCom'>";
    str += "<textarea style='width: 100%; box-sizing: border-box;' rows=5 name='comment' placeholder='Type your comment or question here.' ></textarea><br />";
    str += "<input type='button' name='addbutton' onclick='ValidateAddComment("+sessionId+")' value='Add' />";
    str += "<input type='button' value='Cancel' onclick='ClassroomCancelAdd();' />";
    str += "</form>";
    
    window.originalAddComment = ele1.html();
    window.originalAddQuiz = ele2.html();
    ele1.html(str);
    ele2.html('');
    document.formCom.comment.focus();
}

function ClassroomSwitchToAddQuiz (sessionId){
    var ele1 = $("#quizLink");
    var ele2 = $("#addComment");
    var str = "";
    
    str += "<form name='formQuiz' method='post'>";
    str += "<input type='hidden' name='act' value='add_quiz'>";
    str += "<textarea style='width: 100%; box-sizing: border-box;' rows=2 name='question' placeholder='Type your question here.' ></textarea><br />";
    str += "Number Of Choices: <select onchange='addOptions();' name='NumberOfChoises'><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option></select>";
    str += "<div class='choises' ><span>1. <input type='text' name='a' value='a'/></span><span>2. <input type='text' name='b' value='b'/></span></div>";
    str += "<input type='button' name='addbutton' onclick='ValidateAddQuiz("+sessionId+")' value='Add' />";
    str += "<input type='button' value='Cancel' onclick='ClassroomCancelAdd();' />";
    str += "</form>";
    
    window.originalAddQuiz = ele1.html();
    window.originalAddComment = ele2.html();
    ele1.html(str);
    ele2.html('');
    document.formQuiz.question.focus();
}

function addOptions(){
    var form = $("#AddQuizDialog");
    var num = parseInt(form.find("select option:selected").first().text());
    var currentOptions = form.find(".choises").find("input");
    var currentValues = [];
    currentOptions.each(function(i, option){
       var val = $(option).val();
       currentValues.push(val);
    });
    
    form.find(".choises").empty();
    if(num == currentValues.length){
        return false;
    }else {
        for(var i=1;i<num+1;i++){
            var char = String.fromCharCode(96 + i);
            form.find(".choises").append("<span>"+i+". <input type='text' name='"+char+"' value='"+(currentValues[i-1] || char)+"'></span>");
        }
    }
}

function ClassroomCancelAdd (){
    $("#addComment").html(window.originalAddComment);
    $("#quizLink").html(window.originalAddQuiz);
}

function getQuizzes (sessionId)
{
    if ( !sessionId ) sessionId = lastSId;
    else              lastSId = sessionId;
    
    var url = (NO_REWRITE?"?p=Classroom":"Classroom");
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

function addQuiz (sessionId, name, numOptions, options, open, save)
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
            options: options,
            open: open,
            save: save
        },
        success: function (data) {
            quizzes = data['quizzes'];
            ShowQuiz( data['lastquiz'] );
        },
        error: function (j,t,e) { console.log("Add Quiz Cancelled"); },
        dataType: "json"
    });
}

function removeQuiz (quizId){
        var url = NO_REWRITE?"?p=Classroom":"Classroom";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                act: "remove_quiz",
                quizId: quizId
            },
            success: function () {
            },
            error: function (j,t,e) { console.log("Remove Quiz Cancelled"); }
        });
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

function AddComment (comment, sessionId, parrentId)
{
    var vars = {
        act: "add_comment",
        comment: comment,
        sessionId: sessionId,
        parrentId: parrentId
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

function UpdateSessionComments (sessionId,width,mobile)
{
    var vars = {
        act: "update_comments",
        sessionId: sessionId,
        mobile: mobile?1:null,
        width: width,
        showHidden: showHiddenComments,
        showAddressed: showAddressedComments
    };
    var url = (NO_REWRITE?"?p=Classroom":"Classroom");
    $.post(url,vars,function (res)
    {   
        var eleSect = document.getElementById("commentTable");
            
        eleSect.innerHTML = res;
        
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
function ClassroomReply(id){
    var html = "<textarea name='comment' value=''></textarea>";
    if(id){
        var buttons = {
            Reply: function(){
                var comment = $(this).find('textarea').val();
                if(comment && comment.trim() !== ""){
                    comment = comment.trim();
                    AddComment(comment, session, id);
                    $(this).dialog('close');
                }else{
                    alert("The textfiled was left blank!");
                }
            },
            Cancel: function () { 
                $(this).dialog("close");
                $("reply").html("");
            }
        };
    }else{
        var buttons = {
            Post: function(){
                var comment = $(this).find('textarea').val();
                if(comment && comment.trim() !== ""){
                    comment = comment.trim();
                    AddComment(comment, session);
                    $(this).dialog('close');
                }else{
                    alert("The textfiled was left blank!");
                }
            },
            Cancel: function () { 
                $(this).dialog("close");
                $("reply").html("");
            }
        };

    }
    $("#reply").dialog("option", "title", id ? "Reply" : "Comment");
    $("#reply").dialog("option", "buttons", buttons);
    $("#reply").html(html).dialog("open");
}

function openManageInstitude(){
    var html = "<ul>";
    for(var i in institutes){
        var ins = institutes[i];
        html +="<li><a href='#' onclick='goToInstitude(";
        html +=i;
        html +=")'>";
        html +=ins.name;
        html +="</a></li>";
    }
    html +="</ul>";
    

    var buttons = {
        Add: function(){
            goToInstitude();
            return false;
        },
        Close: function () { 
            $(this).dialog("close");
            return false;
        }
    };
    $("#mngInst").dialog("option", "title", "Manage Institude");
    $("#mngInst").dialog("option", "buttons", buttons);
    $("#mngInst").html(html).dialog("open");
}

function goToInstitude(index){
    var institution = institutes[index];
    var html = "";
    var buttons = {};
    if(typeof index === "undefined"){
        //new institude
        html += "<p>Enter the institution's name:</p>";
        html += "<input type='text' name='instName' placeholder='Institution Name' />";
        buttons = {
            Cancel: function(){
                    openManageInstitude();
            },
            Submit: function(){
                    var newName = $("#mngInst").find("input").val();
                    if(!newName || newName.trim() === ""){
                        alert("Something has left empty!");
                        return false;
                    }
                    addInstitution(newName,function(){
                        updateInsitutes(function(){
                            openManageInstitude();
                        });
                    });
            }
        };
    }else{
        html += "<p>This institution has <em>";
        html += institution["users"].length || "no";
        html += "</em> users.";
        if(institution["users"].length>0){
            buttons = {
                "<": function(){
                    openManageInstitude();
                },
                Edit: function(){
                    openEditInst(index);
                }
            };
        }else{
            buttons = {
                "<": function(){
                    openManageInstitude();
                },
                Edit: function(){
                    openEditInst(index);
                },
                Remove: function(){
                    openRemoveInst(index); 
                }
            };            
        }
    }
    
    $("#mngInst").dialog("option", "title", institution ? institution['name'] : "New Institution");
    $("#mngInst").dialog("option", "buttons", buttons);
    $("#mngInst").html(html).dialog("open");
}

function openEditInst(index){
    var institution = institutes[index];
    
    $("#mngInst").html("<p>Institution name:</p><input type='text' name='instName' value='"+institution['name']+"' placeholder='Institution Name'/>");
    $("#mngInst").dialog("option", "title", "Edit "+institution['name']);
    $("#mngInst").dialog("option", "buttons", {
                                        Cancel: function(){
                                            openManageInstitude();
                                        },
                                        Submit: function(){
                                            var newName = $("#mngInst").find("input").val();
                                            if(!newName || newName.trim() === ""){
                                                alert("Something has left empty!");
                                                return false;
                                            }
                                            editInstitution(institution['id'], newName,function(){
                                                updateInsitutes(function(){
                                                    openManageInstitude();
                                                });
                                            });
                                        }
    });
    $("#mngInst").dialog();
}

function openRemoveInst(index){
    var institution = institutes[index];
    
    $("#mngInst").html("<p>Are you sure you want to remove"+institution['name']+"?");
    $("#mngInst").dialog("option", "title", "Remove "+institution['name']);
    $("#mngInst").dialog("option", "buttons", {
                                        No: function(){
                                            openManageInstitude();
                                        },
                                        Yes: function(){
                                            removeInstitution(institution['id'],function(){
                                                updateInsitutes(function(){
                                                    openManageInstitude();
                                                });
                                            });
                                        }
    });
    $("#mngInst").dialog();
}

function addInstitution(newName, callback){
    var vars = {
        act: "add_institute",
        name: newName
    };
    var url = (NO_REWRITE?"?p=Courses":"Courses");
    $.post(url,vars, function(){
        callback();
    })
    .fail(function(e){
        console.log(e);
        alert("Error: Institution is not added!");
    }); 
}

function editInstitution(id, newName, callback){
    var vars = {
        act: "edit_institute",
        id: id,
        name: newName
    };
    var url = (NO_REWRITE?"?p=Courses":"Courses");
    $.post(url,vars, function(){
        callback();
    })
    .fail(function(e){
        console.log(e);
        alert("Error: Institution is not edited!");
    }); 
}

function removeInstitution(id, callback){
    var vars = {
        act: "remove_institute",
        id: id
    };
    var url = (NO_REWRITE?"?p=Courses":"Courses");
    $.post(url,vars, function(){
        callback();
    })
    .fail(function(e){
        console.log(e);
        alert("Error: Institution is not removed!");
    }); 
}

function openAddUser(){
    var html = "";
        html +="<input type='text' name='firstname' id='firstName' placeholder='First Name' value=''/><br />";
        html +="<input type='text' name='lastname' id='lastName' placeholder='Last Name' value=''/><br />";
        html +="<input type='email' name='email' id='email' placeholder='E-mail' value=''/><br />";
        html +="<select name='institute' id='selInst'>";
        html +="<option disabled selected value='0'>Select Institution</option>";
        for(var i in institutes){
            var ins = institutes[i];
            html +="<option value='";
            html +=ins['id'];
            html +="'>";
            html +=ins['name'];
            html +="</option>";
        }
        html +="</select>";
        html +="<input type='checkbox' name='role' value='in' checked> "+roles["in"];
        html +=" <input type='checkbox' name='role' value='as'> "+roles["as"];
    var buttons = {
        Add: function(){
            var usr = {};
            usr.fn = $(this).find("input[name=firstname]").prop("value");
            usr.ln = $(this).find("input[name=lastname]").prop("value");
            usr.em = $(this).find("input[name=email]").prop("value");
            usr.nt = $(this).find("select").prop("value");
            usr.rl = [];
            $(this).find("input[name=role]:checked").each(function(c,v){
                    usr.rl.push($(v).val());
            });
            for(var i in usr){//Check for empty inputs.
                if(typeof usr[i] !== "object" && usr[i].trim() === ""){
                    alert("Something hs left empty!");
                    return false;
                }
            }
            if(!validateEmail(usr.em)){
                alert("Email address is not valid!");
                return false;
            }
            if(usr.nt.trim() == 0 ){//Check for institution.
                alert("Please select the institution!");
                return false;
            }
            
            addUser(usr);
        },
        Cancel: function () { 
            $(this).dialog("close");
        }
    };
    $("#addIns").dialog("option", "title", "Add User");
    $("#addIns").dialog("option", "buttons", buttons);
    $("#addIns").html(html).dialog("open");
}

function addUser(usr){
    var vars = {
        act: "add_user",
        firstname: usr.fn,
        lastname: usr.ln,
        email: usr.em,
        institude: usr.nt,
        role: usr.rl
    };
    var url = (NO_REWRITE?"?p=Courses":"Courses");
    $.post(url,vars,function (res){
        if(res == 0){
            $("#addIns").html("<h3>User is updated.</h3><p>Email: "+usr.em+"<br>Role: "+(roles[usr.rl] || "Student")+"</p>"); 
        }else{
            $("#addIns").html("<h3>User is added.</h3><p>Email: "+usr.em+"<br>Role: "+(roles[usr.rl] || "Student")+"</p>");
        }
        $("#addIns").dialog("option", "buttons", {OK: function () { $(this).dialog("close");}});
        $("#addIns").dialog();
        
    }).fail(function(e){
        console.log(e);
        alert("Error: Instructor is not added");
    });    
}

function openJoinACourse(){
    var html = "<ul>";
    for (var i=0; i<joinCourseList.length; i++){
        var profCourse = joinCourseList[i];
        html += "<li><a href='#' onClick='goToCourses(";
        html += i;
        html += "); return false;'>";
        html += profCourse[0];
        html += "</a></li>";
    }
    html +="</ui>";
    var buttons = {
        Cancel: function () { 
            $(this).dialog("close");
            $("join").html("");
        }
    };
    $("#join").dialog("option", "title", "Instructors");
    $("#join").dialog("option", "buttons", buttons);
    $("#join").html(html).dialog("open");
}

function goToCourses(i){
    var courses = joinCourseList[i];
    var html = "<ul>";
    for (var j=1; j<courses.length; j++){
        var course = courses[j];
        html += "<li><a href='#' onClick='joinDialog(";
        html += course["id"];
        html += ", \"";
        html += course["title"];
        html += "\"); return false;'>";
        html += course["title"];
        html += "</a></li>";
    }
    html +="</ui>";
    var buttons = {
        Cancel: function () { 
            $(this).dialog("close");
            $("join").html("");
        }
    };
    $("#join").dialog("option", "title", courses[0]+" Courses");
    $("#join").dialog("option", "buttons", buttons);
    $("#join").html(html).dialog("open");
}
function joinDialog (c,title){
    var html = "<br><p>"+"Join "+title+"?</p>";
    var buttons = {
            Yes: function () {
                FormIt({act:"join_course",u:userId,c:c}, URL);
            },
            No:  function () {
                $(this).dialog("close");
                $(this).html("");
            }
    };
    $("#join").dialog("option", "title", "Confirm");
    $("#join").dialog("option", "buttons", buttons);
    $("#join").html(html).dialog("open");
}

function faClassToggle(t){
    var cls = $(t).find("i").attr("class");
    if(cls.search("off")>0){
        $(t).find("i").attr("class", "fa fa-toggle-on fa-lg green");
    }else{
        $(t).find("i").attr("class", "fa fa-toggle-off fa-lg green");
    }
}

function showCommentChatrs(){
    if(sesRep.length > 1){
        var comLineData = { 
            labels: [],
            datasets: [
                        {
                            label: "total",
                            fillColor: "rgba(77, 83, 96,0)",
                            strokeColor: "rgba(77, 83, 96,1)",
                            highlightFill: "rgba(77, 83, 96,0.8)",
                            highlightStroke: "rgba(77, 83, 96,1)",
                            pointColor : "rgba(77, 83, 96,1)",
                            pointStrokeColor : "#fff",
                            data: []
                        },
                        {
                            label: "addressed",
                            fillColor: "rgba(70, 191, 189,0)",
                            strokeColor: "rgba(70, 191, 189,1)",
                            highlightFill: "rgba(70, 191, 189,0.8)",
                            highlightStroke: "rgba(70, 191, 189,1)",
                            pointColor : "rgba(70, 191, 189,1)",
                            pointStrokeColor : "#fff",
                            data: []
                        },
                        {
                            label: "hidden",
                            fillColor: "rgba(247, 70, 74,0)",
                            strokeColor: "rgba(247, 70, 74,1)",
                            highlightFill: "rgba(247, 70, 74,0.8)",
                            highlightStroke: "rgba(247, 70, 74,1)",
                            pointColor : "rgba(247, 70, 74,1)",
                            pointStrokeColor : "#fff",
                            data: []
                        }
                    ]
        };

        for(var i in sesRep){
            var ses = sesRep[i];
            comLineData.labels.push(ses.date);
            comLineData.datasets[0].data.push(ses.comments);
            comLineData.datasets[1].data.push(ses.addressed_comments);
            comLineData.datasets[2].data.push(ses.hidden_comments);
        }
        var line = $("#comments_line canvas").get(0).getContext("2d");
        var lineChart = new Chart(line).Line(comLineData,   {
                                                                legendTemplate : "<span class=\"<%=name.toLowerCase()%>-legend\">(<% for (var i=0; i<datasets.length; i++){%><span style=\"color:<%=datasets[i].strokeColor%>\"><%if(datasets[i].label){%><%=datasets[i].label%><%}%></span><%if(i<datasets.length-1){%>, <%}%><%}%>)<span>"
                                                            }); 
        $("#comments_line .caption").append(lineChart.generateLegend());
            
    }else{
        $("#comments_line").hide();
    }
    
    if(comRep.number_of_comments>0){
        var comDoughData = [
            {
                value: comRep.number_of_comments_by_students,
                color:"#46BFBD",
                highlight: "#5AD3D1",
                label: "Students Comments"
            },
            {
                value: comRep.number_of_comments - comRep.number_of_comments_by_students,
                color: "#949FB1",
                highlight: "#A8B3C5",
                label: "Instructor Comments"
            }
        ];
        var dn = $("#comments_doughnut canvas").get(0).getContext("2d");
        var dnChart = new Chart(dn).Doughnut(comDoughData, {
                                                    animationSteps : 30,
                                                    animateRotate : true,
                                                    animateScale : true,
                                                    tooltipFontSize: 11
                                                });                                
    }else{
        $("#comments_doughnut").hide();
    }
    
}

function showQuizChatrs(){
    console.log(quzRep);
    console.log(sesRep);
    if(sesRep.length > 1){
        var quzLineData = { 
            labels: [],
            datasets: [
                        {
                            label: "participants",
                            fillColor: "rgba(77, 83, 96,0)",
                            strokeColor: "rgba(77, 83, 96,1)",
                            highlightFill: "rgba(77, 83, 96,0.8)",
                            highlightStroke: "rgba(77, 83, 96,1)",
                            pointColor : "rgba(77, 83, 96,1)",
                            pointStrokeColor : "#fff",
                            data: []
                        },
                        {
                            label: "correct answers",
                            fillColor: "rgba(70, 191, 189,0)",
                            strokeColor: "rgba(70, 191, 189,1)",
                            highlightFill: "rgba(70, 191, 189,0.8)",
                            highlightStroke: "rgba(70, 191, 189,1)",
                            pointColor : "rgba(70, 191, 189,1)",
                            pointStrokeColor : "#fff",
                            data: []
                        },
                        {
                            label: "wrong answers",
                            fillColor: "rgba(247, 70, 74,0)",
                            strokeColor: "rgba(247, 70, 74,1)",
                            highlightFill: "rgba(247, 70, 74,0.8)",
                            highlightStroke: "rgba(247, 70, 74,1)",
                            pointColor : "rgba(247, 70, 74,1)",
                            pointStrokeColor : "#fff",
                            data: []
                        }
                    ]
        };

        for(var i in sesRep){
            var ses = sesRep[i];
            quzLineData.labels.push(ses.date);
            quzLineData.datasets[0].data.push(ses.participant);
            quzLineData.datasets[1].data.push(ses.correct_answres);
            quzLineData.datasets[2].data.push(ses.wrong_answres);
        }
        var line = $("#quizzes_line canvas").get(0).getContext("2d");
        var lineChart = new Chart(line).Line(quzLineData, {
            legendTemplate : "<span class=\"<%=name.toLowerCase()%>-legend\">(<% for (var i=0; i<datasets.length; i++){%><span style=\"color:<%=datasets[i].strokeColor%>\"><%if(datasets[i].label){%><%=datasets[i].label%><%}%></span><%if(i<datasets.length-1){%>, <%}%><%}%>)<span>"
        });
        $("#quizzes_line .caption").append(lineChart.generateLegend());
            
    }else{
        $("#quizzes_line").hide();
    }
    
    if(quzRep.number_of_questionnaires>0){
        var quzDoughData = [
            {
                value: quzRep.number_of_answered_questionnaires,
                color:"#46BFBD",
                highlight: "#5AD3D1",
                label: "Answered"
            },
            {
                value: quzRep.number_of_questionnaires - quzRep.number_of_answered_questionnaires,
                color: "#949FB1",
                highlight: "#A8B3C5",
                label: "Not answered"
            }
        ];
        var dn = $("#quizzes_doughnut canvas").get(0).getContext("2d");
        var dnChart = new Chart(dn).Doughnut(quzDoughData, {
                                                    animationSteps : 30,
                                                    animateRotate : true,
                                                    animateScale : true,
                                                    tooltipFontSize: 11
                                                });                                
    }else{
        $("#quizzes_doughnut").hide();
    }
    
}

function updateRoles(){
    var vars = {
        act: "get_roles"
    };
    var url = (NO_REWRITE?"?p=Courses":"Courses");
    $.post(url,vars,function (res){
        roles = JSON.parse(res);
    }).fail(function(e){
        console.log(e,"Error: updateRoles() failed!");
    });   
}

function updateInsitutes(callback){
    var vars = {
        act: "get_intitutes"
    };
    var url = (NO_REWRITE?"?p=Courses":"Courses");
    $.post(url,vars,function (res){
        institutes = JSON.parse(res);
        if(typeof callback !== "undefined") callback();
    }).fail(function(e){
        console.log(e,"Error: updateRoles() failed!");
    });   
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

/*
 *Excel Download functions start
 *******************************
*/
function datenum(v, date1904) {
        if(date1904) v+=1462;
        var epoch = Date.parse(v);
        return (epoch - new Date(Date.UTC(1899, 11, 30))) / (24 * 60 * 60 * 1000);
}

function sheet_from_array_of_arrays(data, opts) {
        var ws = {};
        var range = {s: {c:10000000, r:10000000}, e: {c:0, r:0 }};
        for(var R = 0; R != data.length; ++R) {
                for(var C = 0; C != data[R].length; ++C) {
                        if(range.s.r > R) range.s.r = R;
                        if(range.s.c > C) range.s.c = C;
                        if(range.e.r < R) range.e.r = R;
                        if(range.e.c < C) range.e.c = C;
                        var cell = {v: data[R][C] };
                        if(cell.v == null) continue;
                        var cell_ref = XLSX.utils.encode_cell({c:C,r:R});

                        if(typeof cell.v === 'number') cell.t = 'n';
                        else if(typeof cell.v === 'boolean') cell.t = 'b';
                        else if(cell.v instanceof Date) {
                                cell.t = 'n'; cell.z = XLSX.SSF._table[14];
                                cell.v = datenum(cell.v);
                        }
                        else cell.t = 's';

                        ws[cell_ref] = cell;
                }
        }
        if(range.s.c < 10000000) ws['!ref'] = XLSX.utils.encode_range(range);
        return ws;
}

function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
}

function Workbook() {
        if(!(this instanceof Workbook)) return new Workbook();
        this.SheetNames = [];
        this.Sheets = {};
}

function GetReport(courseId, callBack){
    var data;
    if(typeof courseId === "undefined" ){
        data = {
            act: "get_all_report"
        };
    }else{
        data = {
            act: "get_course_report",
            courseId: courseId
        };
    }
    $.ajax({
        method: "post",
        url: NO_REWRITE?"?p=Reports":"Reports",
        data: data,
        dataType: "json"})
        .done(function (data) {
            callBack(data);
        })
        .fail(function (a,b,c) { console.log("Get report error: ",a,b,c); }
        );
}
function SaveFile(courseId){

    var fileName;
    var date = new Date();

    GetReport(courseId, function(d){
        var rep = d.report;
        fileName = "YouSpeak "+d.title +" Report ("+date.getMonth()+"-"+date.getDay()+"-"+date.getFullYear()+").xlsx";
        /* original data */
        var data = [];
        for(var i= 0; i<rep.length; i++){
            var student = rep[i];
            var title = [];
            var temp = [];
            for (var key in student){
                var value = student[key];
                if(i == 0){//title
                    title.push(key);
                }
                temp.push(Array.isArray(value) ? null : value);
            }
            if(i == 0){//title
                data.push(title);
            }
            data.push(temp);
        }
        var ws_name = "SheetJS";
        var wb = new Workbook(), ws = sheet_from_array_of_arrays(data);

        /* add worksheet to workbook */
        wb.SheetNames.push(ws_name);
        wb.Sheets[ws_name] = ws;
        var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});


        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), fileName);
    });
}

/*
 *Excel Download functions end
 *******************************
*/

/*
 *Overlay Instruction (HELP)
 *******************************
*/      
        var savedElements = [];
        $('body').on('chardinJs:start', function() {
            $(".ui-front").css("z-index", "auto");
//            $("*").each(function(i,e){
//                if($(e).attr('onclick')){
//                    savedElements.push({"e": e, "c": $(e).attr('onclick')});
//                    $(e).attr('onclick', function(){ return false;});
//                }
//            });
        });
        $('body').on('chardinJs:stop', function() {
            $(".ui-front").css("z-index", "100");
//            $.each(savedElements, function(i, n){
//                $(n.e).attr("onclick", n.c);
//            });
        });
/*
 *Overlay Instruction (HELP) end
 *******************************
*/

updateRoles();//Updates the global variable roles based on the ref_rol table.
updateInsitutes();