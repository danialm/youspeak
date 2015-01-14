<?php

global $userId;
global $userInfo;
global $courses;
global $joinCourseList;
global $expand;

?>

<div id="chooser">
    <div id='confirmation' title='Are You Sure?'></div>
    <script>
        $("#confirmation").dialog({autoOpen:false});
        $("#confirmation").dialog("option","resizable",false);
        $("#confirmation").dialog("option","modal",true);
        var joinCourseList = <?php echo json_encode($joinCourseList); ?>;
        var userId = <?php echo $userId; ?>;
        var URL = '<?php echo Page::getRealURL(); ?>';

    </script>
    
    <h2> <?= ucfirst(strtolower($userInfo['firstname']))?> <?= ucfirst(strtolower($userInfo['lastname'])) ?>'s Courses </h2>

    <a href='#' id='iplus' onclick='openJoinACourse(); return false;'><i class="fa fa-plus fa-lg green"></i><b>Join a Course</b></a>
    
    <div id="join"></div>
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


//        $("#joinCourse .header").button().click(function () { 
//            $("#joinCourse #courses").slideToggle();
//        });
//        var html = "<dl>";
//        for (i=0; i<joinCourseList.length; i++){
//            var name = joinCourseList[i][0];
//            var c = name.replace(/ /g,"");
//            c = c.replace(/,/g,"");
//            var link = "<a href='#' onclick='$(\"#joinCourse #courses ."+c+"\").slideToggle(); return false;'>"+name+"</a>";
//            html += "<dt>"+link+"</dt>";
//            for (j=1; j<joinCourseList[i].length; j++)
//            {
//                var title = joinCourseList[i][j]['title'];
//                var id    = joinCourseList[i][j]['id'];
//                link = "<a href='#' onclick='joinDialog("+id+",\""+title+"\"); return false;'>"+title+"</a>";
//                html += "<dd class='"+c+"' style='display:none'>"+link+"</dd>";
//            }
//        }
//        html += "</dl>";
//
//        $("#joinCourse #courses").html(html).hide();
    </script>
    
    <?php
        if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
            echo "<br><p class='red'>".$_SESSION['error']."</p>";
            unset($_SESSION['error']);
        }
//        echo "<dl>\n";
        
        if ($userInfo["role_code"] == "in"){
            echo "<dl>";
            echo "<dt id='addCourse'>";
            echo "<a ";//style='padding-left: 16px; width: auto;'
            echo " id='iplus' href='#'";//class='icons'
            echo " onclick='ChooserSwitchToAddCourse(); return false;'>";
            echo '<i class="fa fa-plus fa-lg green"></i>';
            echo "<b>Add a Course</b></a></dt>";
            echo "</dl>";
        }
        
        if ( count($courses) > 0 ){
            foreach ($courses as $course){
                
                $courseTitle = trim($course["title"]) . " (" . $course['term_name'] . " " . trim($course['year']) . ")";
                $removeLink = $reportLink = "";
                $editCourse = "";
                if ($course['role_in_course'] == 'in'){
                    $removeLink = MakeRemoveCourseLink($course['id']);
                    $reportLink = "<a href='#' onclick='FormIt({act:\"report\", reportCourseId:" . $course['id'] . "}, \"" . Page::getRealURL("Report") . "\"); return false;'><i class='fa fa-bar-chart fa-lg' title='See report'></i></a>";
                    $editCourse = "<a href='#' onclick='toggleEditCourseForm(" . $course['id'] . "); return false;'><i class='fa fa-edit fa-lg orange' title='Edit Course'></i></a>";
                }else{
                    $leaveCourse = MakeLeaveCourseLink($userId, $course['id']);
                }
                
                echo "<dl><dt><span class='course$course[id]'>$leaveCourse$removeLink$editCourse<b>$courseTitle </b>$reportLink</span>";
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
//                        if (($expand != $course['id']) && ($i >= 3)){
//                            MakeExpandLink($course['id']);
//                            break;
//                        }
                        $sessionID = $s["id"];
                        $sessionDate = date("M jS, Y", $s["date"]);
                        $htmlLink = MakeSessionLink($sessionID, $sessionDate, ($course['role_in_course'] == 'in'));
                        if (($course['role_in_course'] == 'in'))
                            $removeLink = MakeRemoveSessionLink($sessionID);
                        echo "<dd>$removeLink$htmlLink</dd>";
                    }
//                    if ($expand == $course['id'])
//                        MakeExpandLink($course['id']);
                }
                else if($course['role_in_course'] != 'in') echo "<dd>No sessions available</dd>";
                echo "</dl>";
            }
        }
        else
            echo "<dl><dt>No courses available</dt></dl>";
        
        
//        echo "</dl>\n";
    ?>
    
</div><!-- chooser -->
