<?php

global $userId;
global $userInfo;
global $courses;
global $joinCourseList;
global $expand;
global $institutions;

$instructor = isset($_SESSION["isInstructor"]) && $_SESSION['isInstructor'];
$admin = isset($_SESSION["isAdmin"]) && $_SESSION['isAdmin'];
?>

<div id="chooser">
    <div id='confirmation' title='Are You Sure?'></div>
    <script>
        $("#confirmation").dialog({
                autoOpen: false,
                modal: false,
                resizable: false,
                draggable: true,
                position: { my: "top", at: "top", of: "#chooser" }
        });
        $("#confirmation").dialog("option","modal",true);
        var joinCourseList = <?php echo json_encode($joinCourseList); ?>;
        var userId = <?php echo $userId; ?>;
        var URL = '<?php echo Page::getRealURL(); ?>';
    </script>
    
    <h2> <?= ucfirst(strtolower($userInfo['firstname']))?> <?= ucfirst(strtolower($userInfo['lastname'])) ?>'s Courses </h2>
    <?php if($admin){?>
    <dl><a href='#' id='iplus' onclick='openAddUser(<?= json_encode($institutions) ?> ); return false;'><i class="fa fa-plus fa-lg green"></i><b>Add User</b></a></dl>

        <div id="addIns" data-intro="Add instructor" data-position="right"></div>
        <script>
            $("#addIns").hide()
                .dialog({
                    autoOpen: false,
                    dialogClass: "no-close-button",
                    hide: { effect: "slide", duration: 200, direction: "down" },
                    show: { effect: "slide", duration: 200, direction: "down" },
                    modal: false,
                    resizable: false,
                    draggable: true,
                    position: { my: "top", at: "top", of: "#chooser" },
                    width: 300,
                    height: 300
            });
        </script>    
    <?php } ?>
    <dl><a href='#' id='iplus' onclick='openJoinACourse(); return false;'><i class="fa fa-plus fa-lg green"></i><b>Join a Course</b></a></dl>
    
    <div id="join" data-intro="Select prof./course" data-position="right"></div>
    <script>
        $("#join").hide()
            .dialog({
                autoOpen: false,
                dialogClass: "no-close-button",
                hide: { effect: "slide", duration: 200, direction: "down" },
                show: { effect: "slide", duration: 200, direction: "down" },
                modal: false,
                resizable: false,
                draggable: true,
                position: { my: "top", at: "top", of: "#chooser" },
                width: 300,
                height: 300
        });
        
        $(document).ready(function(){
            var dls = $("#chooser dl");
            dls.each(function(){
                shrink(this, 0);
            });
        });
        function shrink(t, time){
                var dds = $(t).find("dd:not('.addSession')");
                if(dds.length > 3){
                    dds.each(function(i){
                        if(i>2)
                            $(this).slideUp(time);
                    });
                    $(t).find("dd.expand").remove();
                    $(t).append("<dd class='expand'><a href='#' onclick='expand($(this)); return false;' ><i class='fa fa-caret-down'></i> Show More</a></dd>");
                }

        }
        function expand(t){
            t.closest("dd").siblings().slideDown();
            t.closest("dd").html("<a href='#' onclick='shrink($(this).closest(\"dl\"), 500); return false;' ><i class='fa fa-caret-up'></i> Show Less</a>");
        }
    </script>
    
    <?php
        if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
            echo "<br><p class='red'>".$_SESSION['error']."</p>";
            unset($_SESSION['error']);
        }
        
        if ($instructor){
            echo "<dl>";
            echo "<dt id='addCourse'>";
            echo "<a ";
            echo " id='iplus' href='#'";
            echo " onclick='ChooserSwitchToAddCourse(); return false;'>";
            echo '<i class="fa fa-plus fa-lg green"></i>';
            echo "<b>Add a Course</b></a></dt>";
            echo "</dl>";
        }
        
        if ( count($courses) > 0 ){
            $courseCounter = $ownCourseCounter = $sessionCounter = 0;
            foreach ($courses as $course){
                
                $course['role_in_course'] == 'in' ? $ownCourseCounter++ : "";
                $courseCounter++;
                
                $courseTitle = trim($course["title"]) . " (" . $course['term_name'] . " " . trim($course['year']) . ")";
                $removeLink = $reportLink = "";
                $editCourse = "";
                if ($course['role_in_course'] == 'in'){
                    
                    $reportLink = "<a href='#' onclick='FormIt({act:\"report\", courseId:" . $course['id'] . "}, \"" . Page::getRealURL("Report") . "\"); return false;'";
                    $reportLink.= $ownCourseCounter === 1 ? "data-intro='See report' data-position='right'" : "";
                    $reportLink.= " ><i class='fa fa-bar-chart fa-lg' title='See report'></i></a>";
                    
                    $editCourse = "<a href='#' onclick='toggleEditCourseForm(" . $course['id'] . "); return false;'";
                    $editCourse.= $ownCourseCounter === 1 ? " data-intro='Edit course' data-position='bottom'" : "";
                    $editCourse.= " ><i class='fa fa-edit fa-lg orange' title='Edit Course'></i></a>";
                }
                
                $leaveCourse = MakeLeaveCourseLink($userId, $course['id']);
                
                echo "<dl><dt><span class='course$course[id]'><span";
                echo $courseCounter === 1 ? " data-intro=' Drop course' data-position='top'" : "";
                echo " >$leaveCourse</span><span>$editCourse</span><b>$courseTitle </b>$reportLink</span>";
                if($course['role_in_course'] == 'in'){
                    echo "<form class='hide course$course[id]' action method='post' >";
                    echo "<input type='hidden' name='act' value='edit_course' />";
                    echo "<input type='hidden' name='courseId' value='$course[id]' />";
                    echo "<input type='text' name='courseName' value='$course[title]' />";
                    echo "<select name='term'>\n";
                    echo "<option ". ($course['term_code']=="sp"?"selected":"") ." value='sp'>Spring</option>";
                    echo "<option ". ($course['term_code']=="su"?"selected":"") ." value='su'>Summer</option>";
                    echo "<option ". ($course['term_code']=="fa"?"selected":"") ." value='fa'>Fall</option>";
                    echo "<option ". ($course['term_code']=='wi'?"selected":"") ." value='wi'>Winter</option>";
                    echo "</select>";
                    echo "<input type='text' name='year' value='$course[year]' style='width:4em' />";
                    echo "<input type='submit' value='Submit' />";
                    echo "<input type='button' value='Cancel' onclick='toggleEditCourseForm(" . $course['id'] . "); return false;' />";
                    echo "</form>";
                }
                echo "</dt>";
                
                if ($course['role_in_course'] == 'in'){
                    $htmlLink = MakeAddSessionLink($course['id']);
                    echo "$htmlLink";
                }
                
                if (isset($course['sessions'])){
                    foreach ($course['sessions'] as $i=>$s){
                        $sessionCounter++;
                        $sessionID = $s["id"];
                        $sessionDate = date("M jS, Y", $s["date"]);
                        $htmlLink = MakeSessionLink($sessionID, $sessionDate, ($course['role_in_course'] == 'in'));
                        if (($course['role_in_course'] == 'in'))
                            $removeLink = MakeRemoveSessionLink($sessionID, $sessionCounter === 1);
                        echo "<dd>$removeLink$htmlLink</dd>";
                    }
                }
                else if($course['role_in_course'] != 'in') echo "<dd>No sessions available</dd>";
                echo "</dl>";
            }
        }
       
    ?>
    
</div><!-- chooser -->
