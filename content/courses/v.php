<?php

global $userId;
global $userInfo;
global $sessions;
global $enrollment;
global $allCourses;
global $roleRef;
global $joinCourseList;
global $warnNewPassword;

global $expand;
?>

<div id="chooser">
    <div id='confirmation' title='Are You Sure?'></div>
    <script>
        $("#confirmation").dialog({autoOpen:false});
        $("#confirmation").dialog("option","resizable",false);
        $("#confirmation").dialog("option","modal",true);
        var joinCourseList = <?php echo json_encode($joinCourseList); ?>;
        var u = <?php echo $userId; ?>;
        function joinDialog (c,title)
        {
            $("#joinCourse").append("<div id='confirmDialog' title='Confirm'>");
            $("#joinCourse #confirmDialog").html("<p></p>");
            $("#joinCourse #confirmDialog p").html("Join "+title+"?");
            $("#joinCourse #confirmDialog").dialog({
                modal: true,
                buttons: {
                    Yes: function () {FormIt({act:"join_course",u:u,c:c},"<?php echo Page::getRealURL(); ?>");},
                    No:  function () {$(this).dialog("close");}
                }
            });
            $("#joinCourse #confirmDialog").dialog("open");
        }
    </script>
    <h2> <?php echo "$userInfo[firstname] $userInfo[lastname]"; ?>'s Courses </h2>
    
    <div id='joinCourse'>
        <div class='header'>Join a Course</div>
        <div id='courses'></div>
    </div>
    <script>
        $("#joinCourse .header").button().click(function () { $("#joinCourse #courses").toggle("slide",{direction:"up"})});
        var html = "<dl>";
        for (i=0; i<joinCourseList.length; i++)
        {
            var name = joinCourseList[i][0];
            var c = name.replace(/ /g,"");
            c = c.replace(/,/g,"");
            var link = "<a href='#' onclick='$(\"#joinCourse #courses ."+c+"\").toggle(); return false;'>"+name+"</a>";
            html += "<dt>"+link+"</dt>";
            for (j=1; j<joinCourseList[i].length; j++)
            {
                var title = joinCourseList[i][j]['title'];
                var id    = joinCourseList[i][j]['id'];
                link = "<a href='#' onclick='joinDialog("+id+",\""+title+"\"); return false;'>"+title+"</a>";
                html += "<dd class='"+c+"' style='display:none'>"+link+"</dd>";
            }
        }
        html += "</dl>";

        $("#joinCourse #courses").html(html).hide();
    </script>
    
    <?php
        
        echo "<dl>\n";
        if ( count($enrollment) > 0 )
        {
            foreach ($enrollment as $e)
            {
                $courseID = $e["course_id"];
                
                if ( !isset($allCourses[$courseID]) )
                    continue;
                    
                $courseTitle = $allCourses[$courseID]["title"];
                $role = $roleRef[ $e["role_code"] ];
                $sfc = SessionsForCourse($courseID);
                $instruct = ($role == $roleRef["in"]);
                $removeLink = "";
                if ($instruct)
                    $removeLink = MakeRemoveCourseLink($courseID);
                echo "<dt><b>$courseTitle</b> $removeLink</dt>\n";
                if (isset($sfc))
                {
                    foreach ($sfc as $i=>$s)
                    {
                        if (($expand != $courseID) && ($i >= 3))
                        {
                            MakeExpandLink($courseID);
                            break;
                        }
                        $sessionID = $s["id"];
                        $sessionDate = date("M jS, Y", $s["date"]);
                        $htmlLink = MakeSessionLink($sessionID, $sessionDate, $instruct);
                        if ($instruct)
                            $removeLink = MakeRemoveSessionLink($sessionID);
                        echo "<dd>$htmlLink $removeLink</dd>\n";
                    }
                    if ($expand == $courseID)
                        MakeExpandLink($courseID);
                }
                else echo "<dd>No sessions available</dd>\n";
                echo "</dt>";
                if ($instruct)
                {
                    $htmlLink = MakeAddSessionLink($courseID);
                    echo "$htmlLink";
                }
            }
        }
        else
            echo "<dt>No courses available</dt>\n";
        
        if ($userInfo["role_code"] == "in")
        {
            echo "<dt id='addCourse'>";
            echo "<a style='padding-left: 16px; width: auto;'";
            echo " class='icons' id='iplus' href='#'";
            echo " onclick='ChooserSwitchToAddCourse(); return false;'>";
            echo "Add a Course</a></dt>";
        }
        
        echo "</dl>\n";
    ?>
    
</div><!-- chooser -->

<?php if ($warnNewPassword): ?>
<script>
    $("<div></div>").dialog(
    {
        title: "Update Profile",
        dialogClass: "no-close-button",
        width: 350,
        modal: true,
        resizable: false,
        draggable: false,
        buttons: {Close: function () { $(this).dialog("close"); }},
        close: function () { $(this).dialog("destroy"); },
        show: "scale",
        hide: "scale",
    }).html("Remember to update your password by clicking the 'My Profile' link in the navigation bar above.");
</script>
<?php endif; ?>