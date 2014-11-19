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



<div id="register">

    <h2> Registration </h2>

    <form class='bordered' name='register' method='POST' onsubmit='return ValidateRegistration();'>

        <input type='hidden' name='act' value='register' />

        <div id='formError'>
            <?php echo $errorMsg; ?>
        </div>

        <table>
            <tr>
                <td colspan=2>
                    <fieldset>
                        <legend>User Info (required)</legend>

                        <table style='width:100%'>
                            <tr>
                                <td>

                                    <span id='label_firstName'><label for='firstName'>First Name</label></span>
                                    <input type='text' name='firstName' id='firstName' placeholder='First Name' value='<?php echo $firstname; ?>' /><br />

                                    <span id='label_lastName'><label for='lastName'>Last Name</label></span>
                                    <input type='text' name='lastName' id='lastName' placeholder='Last Name' value='<?php echo $lastname; ?>' /><br />

                                    <span id='label_email'><label for='email'>E-mail</label></span>
                                    <input type='text' name='email' id='email' placeholder='E-mail' value='<?php echo $email; ?>' /><br />                              

                                    <span id='label_institute'><label for='inst'>Institution</label></span>
                                    <select name='institute' id='selInst' style='width:154px'>
                                        <option disabled selected>Select Institution</option>

                                        <?php foreach ($institutions as $i): ?>
                                            <option value='<?php echo $i['id']; ?>'><?php echo $i['name']; ?></option>
                                        <?php endforeach; ?>

                                    </select><br/>
                                    <script>$("#selInst").val(<?php echo is_numeric($inst) ? $inst : 0; ?>);</script>

                                </td>
                            </tr>
                        </table>

                    </fieldset>
                </td></tr><tr><td>
                    <fieldset id='academicfields'>
                        <legend>Academic Info (optional)</legend>

                        <table><tr>
                                <td><label for='major'>Major</label></td>
                                <td><input type='text' name='major' id='major' placeholder='Major' value='<?php echo $major; ?>' /></td>
                            </tr><tr>
                                <td><label for='schoolYear'>School Year</label></td>
                                <td class='rightColumn'><select name='schoolYear' id='schoolYear'>
                                        <option value=0 selected disabled>Select School Year</option>
                                        <option value=1>Freshman</option>
                                        <option value=2>Sophomore</option>
                                        <option value=3>Junior</option>
                                        <option value=4>Senior</option>
                                        <option value=5>Graduate</option>
                                        <option value=6>Other</option>
                                    </select>
                                    <script>$("#schoolYear").val(<?php echo $schoolYear ? $schoolYear : "0"; ?>);</script>
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
                                    <input type='radio' name='sex' value='male' id='smale' <?php if ($sex == "male") echo "checked"; ?> />
                                    <label for='smale'>Male</label>

                                    <input type='radio' name='sex' value='female' id='sfemale' <?php if ($sex == "female") echo "checked"; ?> />
                                    <label for='sfemale'>Female</label>

                                    <br />
                                    <input type='radio' name='sex' value='unspecified' id='snone' <?php if ($sex == "unspecified") echo "checked"; ?> />
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
                                    <input type='radio' name='haveDisa' id='haveDisaYes' value='yes' onchange='DisaShow(this)' <?php if ($haveDisa == "yes") echo "checked"; ?> />
                                    <label for='haveDisaYes'>Yes</label>
                                    <input type='radio' name='haveDisa' id='haveDisaNo' value='no' onchange='DisaShow(this)' <?php if ($haveDisa == "no") echo "checked"; ?> />
                                    <label for='haveDisaNo'>No</label>
                                    <br />
                                    <input type='radio' name='haveDisa' id='haveDisaNone' value='unspecified' onchange='DisaShow(this)' <?php if ($haveDisa == "unspecified") echo "checked"; ?> />
                                    <label for='haveDisaNone'>Don't Specify</label>
                                </td>
                            </tr><tr>
                                <td colspan=2><textarea <?php if ($haveDisa == "yes")
                                            echo "";
                                        else
                                            echo "disabled";
                                        ?> style='display: inline-block' name='disability' width='100%' placeholder='Briefly describe your disability.'><?php if ($haveDisa == "yes") echo $disability; ?></textarea></td>
                            </tr></table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan=2>

            <center><input type='submit' value='Create Account' style='width: 200px' /></center>

            </td>
            </tr>
        </table>

<?php unset($_SESSION["formError"]); ?>

    </form>

</div>
<script>
    $("#academicfields").height($("#academicfields").parent().height() - 18);
    $("#academicfields table").css("margin-top", ($("#academicfields").height() / 2 - 55) + "px");

</script>