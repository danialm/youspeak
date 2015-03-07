<?php 
global $reportError;
global $reportMessage;
global $courses;

$assessor = isset($_SESSION['isAssessor']) && $_SESSION['isAssessor'];
?>

<div id="report">
    <div id='confirmation' title='Are You Sure?'></div> 
    <h2>YouSpeak Reports</h2>
<?php if($reportError){ ?>
    <h3><?= $reportMessage; ?></h3>
<?php }else{ ?> 
    <?php if($assessor){ ?>
        <h3>All Courses: <a title='Download' href="#" onclick='SaveFile(); return false;'><i class="fa fa-download fa-lg orange"></i></a></h3>
    <?php } ?>
    <?php if($courses){?>
    <!-- List View-->
    <h3>List of Courses:</h3>
    <ul>
    <?php foreach($courses as $crs){ ?>
        <li>
            <span><?= $crs['title'] ?></span>
            <?= (isset($crs['noInstructor']) && $crs['noInstructor'] === true) ?  MakeRemoveCourseLink( $crs['id']) : ""  ?>
                <a  title="See report" href='#' onclick='FormIt({act:"get_report", courseId:<?= "\"".$crs['id']."\"" ?> }, <?= "\"".Page::getRealURL("Report")."\"" ?> ); return false;'><i class="fa fa-bar-chart"></i></a>
                <a title='Download' href="#" onclick='SaveFile(<?= $crs['id']?>); return false;'><i class="fa fa-download orange"></i></a>
        </li>
    <?php } ?>
    </ul>
<?php }else{ ?>
    <h3>No course to display!</h3>
<?php } } ?>
</div>
<script>
    $("#confirmation").dialog({
            autoOpen: false,
            modal: false,
            resizable: false,
            draggable: true,
            position: { my: "top", at: "top", of: "#report" }
    });
    $("#confirmation").dialog("option","modal",true);
</script>
