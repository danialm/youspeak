<?php
global $logout;

if($logout){
?>
<!--<img src="https://mail.google.com/mail/u/0/?logout&hl=en" width="0" height="0"/>-->
<div id="login">
    <h2> You have successfully logged out of YouSpeak. </h2>
    <a href="<?= Page::getRealURL('Login'); ?>" rel="login"><i class="fa fa-sign-in fa-2x" title="Login"></i></a>
</div>
<?php }else{?>
<!-- login -->
<div id="login">
    <h2> Welcome to YouSpeak. Please log-in with your Gmail account. </h2>
    <i id="log-spin" class="fa fa-gear fa-2x fa-spin green"></i>
    <div id="signinButton"  style="display: none">
        <span
            class="g-signin"
            data-callback="signinCallback"
            data-clientid="713027941752-qdeubi7vou741vp1en7s4jk5uutt86ig.apps.googleusercontent.com"
            data-cookiepolicy="single_host_origin"
            data-scope="email">
        </span>
    </div>
    <form class="coursesLink" name='login' method='POST' action='<?php  echo Page::getRealURL(); ?>'>
        <input type='hidden' name='act' value='auth' />
        <input type='hidden' name='code' value='' />
    </form>

    <script>

            function signinCallback(authResult) {
                if (authResult['status']['signed_in']) {
                    //console.log(authResult);
                    
                    var code = authResult.code;
                    var form = $(".coursesLink");
                    form.find("input[name='code']").attr("value",code);
                    $(document).ready(function(){
                        form.submit();
                        $("#signinButton").hide();
                        $("#log-spin").show();
                    });
                } else {
                    $("#log-spin").hide();
                    $("#signinButton").show();
                }
            }
    </script>
</div>
<?php } ?>