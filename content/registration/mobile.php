
<?php

global $errorMsg;

global $institutions;

global $firstname;
global $lastname;
global $email;
global $username;
global $inst;
global $major;
global $gpa;
global $schoolYear;
global $sex;
global $age;
global $race;
global $haveDisa;
global $disability;

?>


<script>
    $(document).bind("mobileinit", function()
    {
        $.mobile.transitionFallbacks.flip = "none";
        $.mobile.transitionFallbacks.slideup = "none";
    });
    
    function ValidateReg1 (caller)
    {
        var empty=false;
        if ($("#freg1")[0].firstName.value=="") {empty=true;}
        if ($("#freg1")[0].lastName.value=="")  {empty=true;}
        if ($("#freg1")[0].username.value=="")  {empty=true;}
        if ($("#freg1")[0].email.value=="")     {empty=true;}
        if ($("#freg1")[0].passone.value=="")   {empty=true;}
        if ($("#freg1")[0].passtwo.value=="")   {empty=true;}
        
        if (empty)
        {
            caller.href = "#drequired";
            caller.setAttribute("data-transition","flip");
            return false;
        }
        
        if ($("#freg1")[0].passone.value != $("#freg1")[0].passtwo.value)
        {
            $("#freg1")[0].passone.value = "";
            $("#freg1")[0].passtwo.value = "";
            
            caller.href = "#dpasses";
            caller.setAttribute("data-transition","flip");
            return false;
        }
        
        caller.href = "#reg2"
        caller.setAttribute("data-transition","slideup");
        return true;
    }
    
    function MobileSubmit()
    {
        $.ajax(
        {
            type: "POST",
            url: "<?php echo Page::getRealURL(); ?>",
            data: {
                act: "register",
                firstName:  $("#freg1")[0].firstName.value,
                lastName:   $("#freg1")[0].lastName.value,
                username:   $("#freg1")[0].username.value,
                email:      $("#freg1")[0].email.value,
                institute:  $("#institute").val(),
                passone:    $("#freg1")[0].passone.value,
                major:      $("#freg2")[0].major.value,
                gpa:        $("#freg2")[0].gpa.value,
                schoolYear: $("#freg2")[0].schoolYear.value,
                sex:        $("#freg3 :radio[name=sex]:checked").length?$("#freg3 :radio[name=sex]:checked").val():"",
                race:       $("#freg3")[0].race.value,
                age:        $("#freg3")[0].age.value,
                haveDisa:   $("#freg3 :radio[name=haveDisa]:checked").length?$("#freg3 :radio[name=haveDisa]:checked").val():"",
                disability: $("#freg3")[0].disability.value,
                async:      1
            },
            dataType: "json",
            success: function (data)
            {
                if (!data.error)
                    window.location.href="#dsuccess";
                    
                else
                {
                    $("#derror p").html(data.msg);
                    window.location.href="#derror";
                }
            },
            error: function (j,t,e) { console.log(e); }
        });
    }
</script>
<div data-role="page" id="reg1">
    <div data-theme="b" data-role="header" data-position="fixed">
        <h3>
            YouSpeak
        </h3>
        <a data-theme="b" data-role='button' data-ajax='false' class="ui-btn-left" href='<?php echo Page::getRealURL("Login"); ?>'>
            Back
        </a>
    </div><!-- header -->
    <div data-role="content">
        <form id='freg1' name='freg1' action="" method="POST">
            <input type='text' name='firstName' id='firstname' placeholder='First Name' />
            <input type='text' name='lastName' id='lastname' placeholder='Last Name' />
            <input type='text' name='username' id='username' placeholder='Desired Username' />
            <input type='text' name='email' id='email' placeholder='Email' />
            <select id='institute' name='institute'>
                <option disabled selected>Select Institution</option>
                <?php foreach ($institutions as $i): ?>
                    <option value='<?php echo $i['id']; ?>'><?php echo $i['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type='password' name='passone' id='passone' placeholder='Password' />
            <input type='password' name='passtwo' id='passtwo' placeholder='Re-Enter Password' />
        </form>
        <a data-theme="b" data-role='button' onclick='return ValidateReg1(this)' href='#'>
            Next 
        </a>
    </div><!-- content -->
    
</div><!-- reg1 -->
<div data-role="page" id="reg2">
    <div data-theme="b" data-role="header" data-position="fixed">
        <h3>
            YouSpeak
        </h3>
        <a data-theme="b" data-transition='slideup' data-role='button' class="ui-btn-left" href='#reg1'>
            Back
        </a>
    </div>
    <div data-role="content">
        <form id='freg2' name='freg2' action="" method="POST">
            <input type='text' name='major' id='major' placeholder='Major' />
            <input type='text' name='gpa' id='gpa' placeholder='GPA' />
            <select name='schoolYear' id='schoolYear'>
                <option value=0 selected disabled>Select School Year</option>
                <option value=1>Freshman</option>
                <option value=2>Sophomore</option>
                <option value=3>Junior</option>
                <option value=4>Senior</option>
                <option value=5>Graduate</option>
                <option value=6>Other</option>
            </select>
        </form>
        <a data-theme="b" data-transition='slideup' data-role='button' href='#reg3'>
            Next
        </a>
        <a data-theme="b" data-transition='slideup' data-role='button' href='#reg3'>
            Skip
        </a>
    </div><!-- content -->
</div><!-- reg2 -->
<div data-role="page" id="reg3">
    <div data-theme="b" data-role="header" data-position="fixed">
        <h3>
            YouSpeak
        </h3>
        <a data-theme="b" data-transition='slideup' data-role='button' class="ui-btn-left" href='#reg2'>
            Back
        </a>
    </div>
    <div data-role="content">
        <form id='freg3' name='freg3' action="" method="POST">
            <fieldset data-role="controlgroup" data-type="horizontal">
                <legend>Gender:</legend>
                <input id="smale" name="sex" value="male" type="radio">
                <label for="smale">M</label>
                <input id="sfemale" name="sex" value="female" type="radio">
                <label for="sfemale">F</label>    </body>
</html>

                <input id="snone" name="sex" value="unspecified" type="radio">
                <label for="snone">Don't Specify</label>
            </fieldset>
            <input type='text' name='race' id='race' placeholder='Ethnicity' />
            <input type='text' name='age' id='age' placeholder='Age' />
            <fieldset data-role="controlgroup" data-type="horizontal">
                <legend>Do you have a disability?</legend>
                <input id="haveDisaYes" name="haveDisa" value="yes" type="radio">
                <label for="haveDisaYes">Yes</label>
                <input id="haveDisaNo" name="haveDisa" value="no" type="radio">
                <label for="haveDisaNo">No</label>
                <input id="haveDisaNone" name="haveDisa" value="unspecified" type="radio">
                <label for="haveDisaNone">Don't Specify</label>
            </fieldset>
            <input type='text' name='disability' placeholder='Please describe your disability.' />
        </form>
        <a data-theme="b" data-role='button' href='#' onclick="MobileSubmit()">
            Finish Registration
        </a>
        <a data-theme="b" data-role='button' href='#' onclick="MobileSubmit()">
            Skip &amp; Finish
        </a>
    </div><!-- content -->
</div><!-- reg3 -->
<div data-role="page" id="drequired">
    <div data-role="header">
        <h2>Required Fields</h2>
    </div>
    <div data-role="content">
        <p>All fields on this page are required.</p>
        <a data-role='button' href="#reg1" data-transition='flip'>
            Ok
        </a>
    </div>
</div>
<div data-role="page" id="dpasses">
    <div data-role="header">
        <h2>Mismatch</h2>
    </div>
    <div data-role="content">
        <p>Your re-entered password did not match the original.</p>
        <a data-role='button' href="#reg1" data-transition='flip'>
            Ok
        </a>
    </div>
</div>
<div data-role="dialog" id="dsuccess">
    <div data-role="header">
        <h2>Success</h2>
    </div>
    <div data-role="content">
        <p>Your new account has been successfully registered. You may now log in.</p>
        <a data-role='button' href="<?php echo Page::getRealURL("Login"); ?>" data-transition='flip' data-ajax='false'>
            Back to Login
        </a>
    </div>
</div>
<div data-role="dialog" id="derror">
    <div data-role="header">
        <h2>Error</h2>
    </div>
    <div data-role="content">
        <p></p>
        <a data-role='button' href="#reg1" data-transition='flip'>
            Back to Registration
        </a>
    </div>
</div>
