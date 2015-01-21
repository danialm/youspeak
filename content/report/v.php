<?php 
global $reportError;
global $reportMessage;
global $report;
global $courses;
global $assessor;
?>

<div id="report">
    <h2>YouSpeak Report</h2>
<?php if($reportError){ ?>
    <h3><?= $reportMessage; ?></h3>
<?php }else if($assessor){ ?>
    <script>
        var rep = <?php echo json_encode( $report )?>;
        
        /* original data */
        var data = [];
        for(var i= 0; i<rep.students.length; i++){
            var student = rep.students[i];
            var title = [];
            var temp = [];
            for (var key in student){
                var value = student[key];
                if(i == 0){//title
                    title.push(key);
                }
                temp.push(Array.isArray(value) ? null : value);
            }
            if(i == 0){//title
                data.push(title);
            }
            data.push(temp);
        }
        var ws_name = "SheetJS";
        var wb = new Workbook(), ws = sheet_from_array_of_arrays(data);

        /* add worksheet to workbook */
        wb.SheetNames.push(ws_name);
        wb.Sheets[ws_name] = ws;
        var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});
        
        function SaveFile(){
            saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), "YouSpek Report <?= date("m-d-Y"); ?>.xlsx");
        }
        
    </script>
    <a href="#" onclick='SaveFile(); return false;'><i class="fa fa-download fa-2x orange"></i>  Download</a>
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