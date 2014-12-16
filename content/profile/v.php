<?php
global $errorMsg;
global $instructor;
global $institutions;
global $firstname;
global $lastname;
global $email;
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
            <?php echo isset($errorMsg) ? $errorMsg : ""; ?>
        </div>

<!--        <div id='updatedFields'>
            <?php //if (isset($updatedFields) && count($updatedFields) > 0) { ?>
                The following fields were successfully updated: <br/><br/>
                <?php
//                foreach ($updatedFields as $k => $v)
//                    echo "$v<br/>";
//            }
            ?>
        </div>-->
        <fieldset>
            <legend>User Info (required)</legend>



            <!--<span id='label_username'><label for='username'>Desired Username</label></span>-->
            <!--<input type='hidden' name='username' id='username' placeholder='Username' value='' /><br />-->

            <label for='firstName'>First Name</label>
            <input type='text' name='firstname' id='firstName' placeholder='First Name' value='<?php echo $firstname; ?>' /><br />                

            <label for='lastName'>Last Name</label>
            <input type='text' name='lastname' id='lastName' placeholder='Last Name' value='<?php echo $lastname; ?>' /><br />



            <label for='email'>E-mail</label>
            <input type='email' name='email' id='email' placeholder='E-mail' value='<?php echo $email; ?>' /><br />

            <label for='inst'>Institution</label>
            <select name='institute' id='selInst'>
                <option disabled selected>Select Institution</option>

                <?php foreach ($institutions as $i): ?>
                    <option value='<?php echo $i['id']; ?>'><?php echo $i['name']; ?></option>
                <?php endforeach; ?>

            </select>
            <script>updateprofile.selInst.selectedIndex = <?php echo is_numeric($inst) ? $inst : 0; ?>;</script>



        </fieldset>




        <?php if (!$instructor) : ?>



            <fieldset>
                <legend>Academic Info (optional)</legend>



                <label for='major'>Major</label>
                <input type='text' name='major' id='major' placeholder='Major' value='<?php echo $major; ?>' /><br>

                <label for='schoolYear'>School Year</label>
                <select name='school_year' id='schoolYear'>
                    <option value=0 selected disabled>Select School Year</option>
                    <option value=1>Freshman</option>
                    <option value=2>Sophomore</option>
                    <option value=3>Junior</option>
                    <option value=4>Senior</option>
                    <option value=5>Graduate</option>
                    <option value=6>Other</option>
                </select></br>
                <script> $("#schoolYear")[0].selectedIndex = <?php echo is_numeric($schoolYear) ? $schoolYear : 0; ?>;</script>


                <label for='gpa'>GPA</label>
                <input type='text' name='gpa' id='gpa' placeholder='GPA' value='<?php echo $gpa; ?>' />



            </fieldset>



            <fieldset>
                <legend>Personal Info (optional)</legend>



                <label for="gender">Gender:</label>
                <span class="radio"><input type='radio' name='gender' value='male' id='smale' <?php if ($sex == "male") echo "checked"; ?> />
                <label for='smale'>Male</label>

                <input type='radio' name='gender' value='female' id='sfemale' <?php if ($sex == "female") echo "checked"; ?> />
                <label for='sfemale'>Female</label>

                <input type='radio' name='gender' value='unspecified' id='snone' <?php if ($sex == "unspecified") echo "checked"; ?> />
                <label for='snone'>Don't Specify</label></span><br>

                <label for='race'>Ethnicity</label>
                <input type='text' name='race' id='race' placeholder='Ethnicity' value='<?php echo $race; ?>' /><br>

                <label for='age'>Age</label>
                <input type='text' name='age' id='age' placeholder='Age' value='<?php echo $age; ?>' /><br>

                <label for="have-disa">Do you have any disabilities?</label>
                <span class="radio"><input type='radio' name='have_disa' id='haveDisaYes' value='yes' onchange='DisaShow(this)' <?php if ($haveDisa == "yes") echo "checked"; ?> />
                <label for='haveDisaYes'>Yes</label>

                <input type='radio' name='have_disa' id='haveDisaNo' value='no' onchange='DisaShow(this)' <?php if ($haveDisa == "no") echo "checked"; ?> />
                <label for='haveDisaNo'>No</label>

                <input type='radio' name='have_disa' id='haveDisaNone' value='unspecified' onchange='DisaShow(this)' <?php if ($haveDisa == "unspecified") echo "checked"; ?> />
                <label for='haveDisaNone'>Don't Specify</label></span><br>

                <textarea <?= $haveDisa == "yes" ? "" : "disabled" ?> name='disability' placeholder='Briefly describe your disability.'><?php if ($haveDisa == "yes") echo $disability; ?></textarea>


            </fieldset>



        <?php endif ?>


        <center>
            <input type='submit' value='Submit Changes' style='width: 200px' />
            <!--<input type='button' value='Courses' onclick='window.location.href = "<?php echo Page::getRealURL("Login"); ?>"' />-->
        </center>


        <?php unset($_SESSION["formError"]); ?>

    </form>

</div>