<?php 
global $reportError;
global $reportMessage;
global $report;
global $courses;
global $asReport;
global $inReport;
?>

<div id="report">
    <div id='confirmation' title='Are You Sure?'></div>    
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
    <h2>YouSpeak Report</h2>
<?php if($reportError){ ?>
    <h3><?= $reportMessage; ?></h3>
<?php }else{ ?> 
    <script>
        var report = <?= json_encode( $asReport ? $asReport : $inReport )?>, date = '<?= date("m-d-y") ?>' ;
        console.log(report);
    </script>
    <?php if($asReport){ ?>
        <h3>All Courses: <a title='Download' href="#" onclick='SaveFile(report, date); return false;'><i class="fa fa-download fa-lg orange"></i></a></h3>
    <?php } ?>
    <?php if($report){ ?>
    <h3><?= $report['title']?></h3>
    <?php foreach($report['reports'] as $name => $rep){?>
        <div class="reportContainer">
            <h4><?= ucfirst(strtolower($name)) ?></h4>
            <?php foreach( $rep as $n => $d){ ?>
            <li><?= ucwords(str_replace("_", " ", $n)).": ".$d?></li>
            <?php } ?>
        </div>
    <?php } ?>
    <br><br>
    <a href='#' onclick='FormIt({act:"clear"}, <?= "\"".Page::getRealURL("Report")."\"" ?> ); return false;'><i class="fa fa-list" title="Report"></i>Reports</a>
<?php }else if($courses){?>
    <h3>List of Courses:</h3>
    <ul>
    <?php foreach($courses as $crs){ ?>
        <li>
            <span><?= $crs['title'] ?></span>
            <?= (isset($crs['noInstructor']) && $crs['noInstructor'] === true) ?  MakeRemoveCourseLink( $crs['id']) : ""  ?>
                <a  title="See report" href='#' onclick='FormIt({act:"report", reportCourseId:<?= "\"".$crs['id']."\"" ?> }, <?= "\"".Page::getRealURL("Report")."\"" ?> ); return false;'><i class="fa fa-bar-chart"></i></a>
                <a title='Download' href="#" onclick='SaveFile(report, date, <?= $crs['id']?>); return false;'><i class="fa fa-download orange"></i></a>
        </li>
    <?php } ?>
    </ul>
<?php }else{ ?>
    <h3>No course to display!</h3>
<?php } } ?>
</div>