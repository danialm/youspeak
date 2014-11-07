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
<script>

var joinCourseList = <?php echo json_encode($joinCourseList); ?>;
var savedContent = null;

function SaveAndClearContent ()
{
    savedContent = $("#drawers").html();
    $("#drawers").html("");
}

function RestoreContent ()
{
    $("#drawers").html(savedContent);
    $("#button").html("<a id='button' data-role='button' data-theme='a' href='#' onclick='DrawJoinCourseList(); return false;'>Join a Course</a>");
    $("#page1").trigger("create");
}

function DrawJoinCourseList ()
{
    var html = "";
    
    if (joinCourseList.length==0)
        html = "No Courses";
    
    else
        for (i=0; i<joinCourseList.length; i++)
        {
            var name = joinCourseList[i][0];
            var c = name.replace(/ /g,"");
            c = c.replace(/,/g,"");
            html += "<div data-theme='a' data-role='collapsible' data-collapsed='true' data-inset='false'>";
            html += "<h3>" + name + "</h3>";
            html += "<ul data-role='listview' data-divider-theme='a' data-inset='false' data-collapsed='true'>";
            
            for (j=1; j<joinCourseList[i].length; j++)
            {
                var title = joinCourseList[i][j]['title'];
                var id    = joinCourseList[i][j]['id'];
                html += "<li data-theme='c'>";
                html += "<a href='#' onclick='mobileJoinCourse("+id+",\""+title+"\"); return false;' data-ajax='false'>"+title+"</a>";
                html += "</li>";
            }
            
            html += "</ul>";
            html += "</div>";
        }
    
    $("#drawers").html(html);
    $("#button").html("<a id='button' data-role='button' data-theme='b' href='#' onclick='RestoreContent(); return false;'>Back</a>");
    $("#page1").trigger("create");
}

function mobileJoinCourse (id, title)
{
    FormIt({act:"join_course",c:id,u:<?php echo $userId; ?>},"<?php echo Page::getRealURL(); ?>");
}

</script>
<div data-role="page" id="page1">
    <div data-theme="b" data-role="header" data-position="fixed">
        <h2>
            YouSpeak
        </h2>
        <a data-theme="b" data-role='button' class="ui-btn-right" href='<?php echo Page::getRealURL("Login"); ?>'>
            Log Out
        </a>
    </div>
    <div data-role="content">
        <div id='drawers' data-role="collapsible-set" data-theme="b" data-content-theme="b">
            <?php if (!$enrollment)
                echo "No Courses Available";
                
            else foreach ($enrollment as $e):
                $cid = $e['course_id'];
        
                if ( !isset($allCourses[$cid]) )
                    continue;
            
                $ctitle = $allCourses[$cid]["title"];
                $sfc = SessionsForCourse($cid);
            ?>
            <div data-role="collapsible" data-collapsed="true" data-inset='false'>
                <h3>
                    <?php echo $ctitle; ?>
                </h3>
                <ul data-role="listview" data-divider-theme="b" data-inset="false" data-collapsed='true'>
                    <?php if (!$sfc)
                        echo "No Sessions Available";
                        
                    else foreach($sfc as $i=>$s):
                        if ($i >= 3) break;
                        $sid = $s['id'];
                        $sdate = $sessionDate = date("M jS, Y", $s["date"]);
                    ?>
                    <li data-theme="c">
                        <form name="sf<?php echo $sid; ?>" action="<?php echo Page::getRealURL("Classroom"); ?>" method="POST">
                        <input type="hidden" name="sessionId" value=<?php echo $sid; ?> /></form>
                        <a href="#" onclick="sf<?php echo $sid; ?>.submit();" data-ajax=false>
                            <?php echo $sdate; ?>
                        </a>
                    </li>
                    <?php endforeach // sfc(sessions) ?>
                </ul>
            </div>
            <?php endforeach // enrollment(courses) ?>
        </div>
        <script> SaveAndClearContent(); $("#drawers").html(savedContent); </script>
        <br /><br />
        <div id='button'>
            <a id='button' data-role='button' data-theme='a' href='#' onclick='DrawJoinCourseList(); return false;'>Join a Course</a>
        </div>
    </div>
</div>
