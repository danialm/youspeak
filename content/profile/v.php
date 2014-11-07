<?php

global $errorMsg;
global $instructor;
global $institutions;
global $firstname;
global $lastname;
global $email;
global $username;
global $major;
global $gpa;
global $inst;
global $schoolYear;
global $sex;
global $age;
global $race;
global $haveDisa;
global $disability;
global $updatedFields;
?>

<div id="register">

    <h2> My Profile </h2>
    
    <form class='bordered' name='updateprofile' method='POST' action='<?php echo Page::getRealURL(); ?>'>
    
        <input type='hidden' name='act' value='update_profile' />
    
        <div id='formError'>
            <?php echo isset($errorMsg)?$errorMsg:""; ?>
        </div>
        
        <div id='updatedFields'>
            <?php if (isset($updatedFields)): ?>
            The following fields were successfully updated: <br/><br/>
            <?php foreach ($updatedFields as $k=>$v) echo "$v<br/>";
            endif; ?>
        </div>
        
        <table><tr><td>
        <fieldset>
        <legend>User Info (required)</legend>
        

        
            <span id='label_username'><label for='username'>Desired Username</label></span>
            <input type='text' name='username' id='username' placeholder='Username' value='<?php echo $username; ?>' /><br />
            
            <span id='label_firstName'><label for='firstName'>First Name</label></span>
            <input type='text' name='firstname' id='firstName' placeholder='First Name' value='<?php echo $firstname; ?>' /><br />                
        
            <span id='label_lastName'><label for='lastName'>Last Name</label></span>
            <input type='text' name='lastname' id='lastName' placeholder='Last Name' value='<?php echo $lastname; ?>' /><br />
        

        
            <span id='label_email'><label for='email'>E-mail</label></span>
            <input type='text' name='email' id='email' placeholder='E-mail' value='<?php echo $email; ?>' /><br />

            <span id='label_institute'><label for='inst'>Institution</label></span>
            <select name='institute' id='selInst' style='width:154px'>
                <option disabled selected>Select Institution</option>
            
                <?php foreach ($institutions as $i): ?>
                    <option value='<?php echo $i['id']; ?>'><?php echo $i['name']; ?></option>
                <?php endforeach; ?>
            
            </select>
            <script>updateprofile.selInst.selectedIndex = <?php echo is_numeric($inst)?$inst:0; ?>;</script>
        

        
        </fieldset>
        </td>
        
        <td>
        <fieldset>
        <legend>Password Change (optional)</legend>
        <table style='float:right'>
            <tr>
                <td>Existing Password</td>
                <td><input type='password' name='pass0' placeholder='Password' /></td>
            </tr><tr>
                <td>New Password</td>
                <td><input type='password' name='pass1' placeholder='Password' /></td>
            </tr><tr>
                <td>Retype New Password</td>
                <td><input type='password' name='pass2' placeholder='Password' /></td>
            </tr>
        </table>
        </fieldset>
        </td></tr>
        
        <?php if (!$instructor) : ?>
        
        <tr><td>
        <fieldset>
        <legend>Academic Info (optional)</legend>
        
        <table><tr>
        <td><label for='major'>Major</label></td>
        <td><input type='text' name='major' id='major' placeholder='Major' value='<?php echo $major; ?>' /></td>
        </tr><tr>
        <td><label for='schoolYear'>School Year</label></td>
        <td class='rightColumn'><select name='school_year' id='schoolYear'>
            <option value=0 selected disabled>Select School Year</option>
            <option value=1>Freshman</option>
            <option value=2>Sophomore</option>
            <option value=3>Junior</option>
            <option value=4>Senior</option>
            <option value=5>Graduate</option>
            <option value=6>Other</option>
        </select>
        <script> $("#schoolYear")[0].selectedIndex = <?php echo is_numeric($schoolYear)?$schoolYear:0; ?>; </script>
        </td>
        </tr><tr>
        <td><label for='gpa'>GPA</label></td>
        <td><input type='text' name='gpa' id='gpa' placeholder='GPA' value='<?php echo $gpa; ?>' /></td>
        </tr></table>
        
        </fieldset>
        </td>
        
        <td>
        <fieldset>
        <legend>Personal Info (optional)</legend>
        
        <table id='tablePerson'><tr>
        <td id='leftColumn'>Gender</td>
        <td class='rightColumn'>
            <input type='radio' name='gender' value='male' id='smale' <?php if ($sex=="male") echo "checked"; ?> />
            <label for='smale'>Male</label>
            
            <input type='radio' name='gender' value='female' id='sfemale' <?php if ($sex=="female") echo "checked"; ?> />
            <label for='sfemale'>Female</label>
            
            <br />
            <input type='radio' name='gender' value='unspecified' id='snone' <?php if ($sex=="unspecified") echo "checked"; ?> />
            <label for='snone'>Don't Specify</label>
        </td>
        </tr><tr>
        <td><label for='race'>Ethnicity</label></td>
        <td><input type='text' name='race' id='race' placeholder='Ethnicity' value='<?php echo $race; ?>' /></td>
        </tr><tr>
        <td><label for='age'>Age</label></td>
        <td><input type='text' name='age' id='age' placeholder='Age' value='<?php echo $age; ?>' /></td>
        </tr></table>
        <br />
        <table id='tableDisa'><tr>
        <td id='leftColumn'>Do you have any disabilities?</td>
        <td class='rightColumn'>
        <input type='radio' name='have_disa' id='haveDisaYes' value='yes' onchange='DisaShow(this)' <?php if ($haveDisa=="yes") echo "checked"; ?> />
        <label for='haveDisaYes'>Yes</label>
        <input type='radio' name='have_disa' id='haveDisaNo' value='no' onchange='DisaShow(this)' <?php if ($haveDisa=="no") echo "checked"; ?> />
        <label for='haveDisaNo'>No</label>
        <br />
        <input type='radio' name='have_disa' id='haveDisaNone' value='unspecified' onchange='DisaShow(this)' <?php if ($haveDisa=="unspecified") echo "checked"; ?> />
        <label for='haveDisaNone'>Don't Specify</label>
        </td>
        </tr><tr>
        <td colspan=2><textarea <?php if ($haveDisa=="yes") echo ""; else echo "disabled"; ?> style='display: inline-block' name='disability' width='100%' placeholder='Briefly describe your disability.'><?php if ($haveDisa=="yes") echo $disability; ?></textarea></td>
        </tr></table>
        </fieldset>
        </td></tr>
        
        <?php endif ?>
        
        <tr><td colspan=2>
        
        <center>
            <input type='submit' value='Submit Changes' style='width: 200px' />
            <input type='button' value='Go Back' onclick='window.location.href="<?php echo Page::getRealURL("Courses"); ?>"' />
        </center>
        
        </td></tr></table>
    
        <?php unset($_SESSION["formError"]); ?>
        
    </form>
    
</div>