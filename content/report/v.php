<?php 
global $reportError;
global $reportMessage;
global $report;
global $courses;
?>

<div id="report">
    <h2>YouSpeak Report</h2>
<?php if($reportError){ ?>
    <h3><?= $reportMessage; ?></h3>
<?php }else if($report){ ?>
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
        <li><span><?= $crs['title'] ?></span> <a href='#' onclick='FormIt({act:"report", reportCourseId:<?= "\"".$crs['id']."\"" ?> }, <?= "\"".Page::getRealURL("Report")."\"" ?> ); return false;'><i class="fa fa-bar-chart" title="See report"></i></a></li>
    <?php } ?>
    </ul>
<?php }else{ ?>
    <h3>No course to display!</h3>
<?php } ?>
</div>