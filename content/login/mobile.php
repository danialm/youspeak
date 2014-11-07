
<div data-role="page" id="page1">
<div data-theme="b" data-role="header" data-position="fixed">
    <h3>YouSpeak</h3>
</div>

<div data-role="content">

    <?php if ( isset($_SESSION['formError']) ): 
        unset($_SESSION['formError']); ?>
    
    <h3>These credentials don't match with our records.</h3>
    <a data-role='button' href='<?php echo Page::getRealURL(); ?>' data-ajax=false>Back</a>
    
    <?php else: ?>
    
    <form id='login' action="" method="POST">
        <div data-role="fieldcontain">
            <fieldset data-role="controlgroup">
                <label for="textinput1">
                    Username
                </label>
                <input name="username" id="textinput1" placeholder="" value="" type="text" />
            </fieldset>
        </div>
        <div data-role="fieldcontain">
            <fieldset data-role="controlgroup">
                <label for="textinput2">
                    Password
                </label>
                <input name="password" id="textinput2" placeholder="" value="" type="password" />
            </fieldset>
        </div>
        <input type='hidden' name='mobile' value=1 />
        <input id='submitBut' type="button" data-theme="b" value="Log In" />
    </form>
    
    <a data-transition='slideup' href="<?php echo Page::getRealURL("Registration"); ?>" data-ajax='false' data-role='button' data-theme='a'>Create an Account</a>

    <?php endif; ?>
</div>

<script>
    $("#submitBut").click(function()
    {
        if ($("form#login")[0].username.value=="")
            return false;
            
        FormIt({
            act: "auth",
            username: $("form#login")[0].username.value,
            password: $("form#login")[0].password.value
        }, "<?php echo Page::getRealURL(); ?>");
    });
</script>

</div>
