<!--<script>

<?php // if ( isset($_SESSION['newUserAdded']) && $_SESSION['newUserAdded'] ):
//    unset($_SESSION['newUserAdded']);?>
    
$("<div></div>").html("Your account was created successfully.").dialog({
    title: "Success",
    buttons: {OK: function () { $(this).dialog("close"); }},
    dialogClass: "no-close-button",
    resizable: false,
    draggable: false,
    modal: true
});

<?php // endif; ?>

</script>

<div id="login">
    <h2> Welcome to YouSpeak. Please log-in or create a student account. </h2>
    
    <form class='bordered' name='login' method='POST' action='<?php // echo Page::getRealURL(); ?>' onsubmit='return ValidateLogin()'>
    
        <input type='hidden' name='act' value='auth' />
    
        <div id='formError'>
            <?php // if ( isset($_SESSION["formError"]) ) echo $_SESSION["formError"]["msg"]; ?>
        </div>
    
        <span id="label_username">Username</span>
        <input type='text' name='username' placeholder='Username' /><br />
        
        <script>document.login.username.focus()</script>
        
        <span id="label_password">Password</span>
        <input type='password' name='password' placeholder='Password' /><br />
        
        <a href="<?php // echo Page::getRealURL("Registration"); ?>">Create an account</a>
        <input type='submit' value='Log In' />
        
        <?php // unset($_SESSION["formError"]); ?>
        
    </form>
</div> -->

<div id="login">
    <h2> Welcome to YouSpeak. Please log-in with your Gmail account. </h2>
    <span id="signinButton">
        <span
            class="g-signin"
            data-callback="signinCallback"
            data-clientid="713027941752-qdeubi7vou741vp1en7s4jk5uutt86ig.apps.googleusercontent.com"
            data-cookiepolicy="single_host_origin"
            data-scope="email">
        </span>
    </span>
    <form class="coursesLink" name='login' method='POST' action='<?php  echo Page::getRealURL(); ?>'>
        <input type='hidden' name='act' value='auth' />
        <input type='hidden' name='code' value='' />
        <input type='hidden' name='access_token' value='' />
<!--        <input type='hidden' name='name' value='' />
        <input type='hidden' name='email' value='' />
        <input type='hidden' name='gender' value='' />-->
        <input type='submit' value='My Courses' style="display: none"/>
    </form>
    <script>
            function signinCallback(authResult) {
                if (authResult['status']['signed_in']) {
                    console.log(authResult);
                    $("#signinButton").hide();
                    var access_token = authResult.access_token;
                    var code = authResult.code;
//                    gapi.client.load('plus', 'v1', function() {
//                        var request = gapi.client.plus.people.get({
//                            'userId': 'me'
//                        });
//                        request.execute(function(resp) {
//                            var name = resp.displayName;
//                            var gender = resp.gender;
//                            var primaryEmail;
//                            for (var i=0; i < resp.emails.length; i++) {
//                              if (resp.emails[i].type === 'account') primaryEmail = resp.emails[i].value;
//                            }
                            var form = $(".coursesLink");
                            form.find("input[name='access_token']").attr("value",access_token);
                            form.find("input[name='code']").attr("value",code);
//                            form.find("input[name='name']").attr("value",name);
//                            form.find("input[name='gender']").attr("value",gender);
//                            form.find("input[name='email']").attr("value",primaryEmail);
                            form.find("input[type='submit']").show();
//                        });
//                    });
                } else {
                    console.log('Sign-in state: ' + authResult['error']);
                    $("#signinButton").show();
                    $("input[type='submit']").hide();
                }
            }
    </script>
</div> 
<!-- login -->