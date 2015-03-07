<?php 
global $reportError;
global $reportMessage;
global $report;
global $COMMENTS_REPORT_TITLE;
global $QUIZ_REPORT_TITLE;
?>

<div id="report">
    <h2><?= $report['title']?> Report</h2>
<?php if($reportError){ ?>
    <h3><?= $reportMessage; ?></h3>
<?php }else{ ?> 
    <?php foreach($report['reports'] as $name => $rep){ 
        if($name === 'sessions')            continue;?>
        <div class="reportContainer">
            <h3><?= ucfirst(strtolower($name)) ?></h3>
            <?php if($name == $COMMENTS_REPORT_TITLE){?>
            <div class="charts">
                <div id="comments_line" class="chart line">
                    <canvas width="600" height="400"></canvas>
                    <div class="caption">Number of comments per session </div>
                </div>
                <div id="comments_doughnut" class="chart pie">
                    <canvas width="200" height="200"></canvas>
                    <div class="caption">Number of comments</div>
                </div>
                <div class="chart">
                    <table>
                        <?php foreach( $rep as $n => $d){ ?>
                        <tr>
                            <td><?= ucwords(str_replace("_", " ", $n))?></td><td><?= $d?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <div class="caption">&nbsp;</div>
                </div>
            </div>
            <?php }?>
            <?php if($name == $QUIZ_REPORT_TITLE){?>
            <div class="charts">
                <div id="quizzes_line" class="chart line">
                    <canvas width="600" height="400"></canvas>
                    <div class="caption">Average questionnaire data per session </div>
                </div>
                <div id="quizzes_doughnut" class="chart pie">
                    <canvas width="200" height="200"></canvas>
                    <div class="caption">Number of questionnaire</div>
                </div>
                <div class="chart">
                    <table>
                        <?php foreach( $rep as $n => $d){ ?>
                        <tr>
                            <td><?= ucwords(str_replace("_", " ", $n))?></td><td><?= $d?></td>
                        </tr>
                        <?php } ?>
                    </table>
                    <div class="caption">&nbsp;</div>
                </div>
            </div>
            <?php }?>
            <?php if($name == "students"){?>
            <div>
                <span>IDs: </span><span><?= implode(", ", $rep)?></span>
            </div>
            <?php }?>
        </div>
    <?php } ?>
    <br><br>
    <a href='<?= Page::getRealURL("Reports") ?>'><i class="fa fa-list" title="Report"></i>Reports</a>
    <script src="scripts/Chart.js/Chart.min.js"></script>
    <script>
    var comRep = <?= json_encode($report['reports'][$COMMENTS_REPORT_TITLE])?>;
    var quzRep = <?= json_encode($report['reports'][$QUIZ_REPORT_TITLE])?>;
    var sesRep = <?= json_encode($report['reports']['sessions'])?>;
    Chart.defaults.global.animationSteps = "30";
    Chart.defaults.global.animationEasing = "easeOutBounce";
    Chart.defaults.global.responsive= true;
    Chart.defaults.global.multiTooltipTemplate= "<%= value %>";
    $(document).ready(function(){
        showCommentChatrs();
        showQuizChatrs();
    });
    </script>
<?php } ?>
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
