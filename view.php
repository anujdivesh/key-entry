<?php
  require_once("app_code/session.php");
  require_once("app_code/class.user.php");
  require_once("app_code/class.division.php");
  $auth_user = new USER();

  $user_id = $_SESSION['user_session_id'];
  $username = $auth_user->getfullname($user_id);
  $emailadd = $auth_user->getemaill($user_id);
  $index_act = 'inactive';
  $view_act = 'active';

  $station_str = $auth_user->getStationStr($user_id);
  $station_arr = explode (",", $station_str); 

  $my_list = array();
  for ($i = 0; $i < count($station_arr); $i++) {
    $e = strval($station_arr[$i]);
    $b = $auth_user->getStationAcess($e);
    $c = $e.",".$b; 
    $sname = $auth_user->getStationName($station_arr[$i]);
    $my_list[$e] = $station_arr[$i]. " - ". $sname;
  }

  $role_id = $auth_user->getRoleID($user_id);
  $all_st = '';
  $all_select = 'Select';
  if($role_id == '1'){
    $all_st = '%';
    $all_select = 'All';
  }



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php  require_once("assets/master/header_user.php"); ?>
    <link rel="shortcut icon" href="logo1.ico">
    <title>FMS | Data View</title>
    <script type="text/javascript" src="jquery.min.js"></script>   
</head>

<body class="fixed-navbar">
    <div class="page-wrapper">
        <?php  require_once("assets/master/userheader1.php"); ?>
        <!-- START SIDEBAR-->
        <?php  require_once("assets/master/nav2.php"); ?>
        <!-- END SIDEBAR-->
        <div class="content-wrapper">
            <!-- START PAGE CONTENT-->
            <div class="page-heading">
                <h1 class="page-title">Data View</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html"><i class="la la-home font-20"></i></a>
                    </li>
                    <li class="breadcrumb-item">Search</li>
                </ol>
            </div>
            <div class="page-content fade-in-up">
                <div class="row">
                
                    <div class="col-md-12">
                        <div class="ibox ibox-info">
                            <div class="ibox-head">
                                <div class="ibox-title">Search</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>Station</label>
                                            <select class="form-control" name="station" id="station">
                                            <option value="<?php echo $all_st;?>" selected="selected">--- <?php echo $all_select; ?> Station ---</option>
                                                <?php
                                                    foreach($my_list as $key => $value){
                                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group" id="date_1">
                                            <label>Date</label>
                                            <div class="input-group date">
                                                <span class="input-group-addon bg-white"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control" type="text" value="" name="datee" id = "datee" placeholder="All Days">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Search" class="btn btn-success" name="submit" id="submit" >
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">Details</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                </div>
                            </div>
                                                        <div class="ibox-body">
                                                        <!-- Top scrollbar -->
                                                        <div id="top-scrollbar" style="overflow-x:auto; width:100%; height:18px;">
                                                            <div style="height:1px; min-width:1200px;"></div>
                                                        </div>
                                                        <div class="table-responsive" id="table-scrollbar" style="overflow-x:auto; width:100%;">
                                                                <table class="table table-bordered" id="records_table" style="white-space:nowrap;">
                                                                                <tbody>
                                                                                </tbody>
                                                                        </table>
                                                        </div>
                                    <script type="text/javascript">
                                    // Sync top scrollbar with table scrollbar
                                    $(function(){
                                        var $top = $('#top-scrollbar');
                                        var $table = $('#table-scrollbar');
                                        $top.scroll(function(){ $table.scrollLeft($top.scrollLeft()); });
                                        $table.scroll(function(){ $top.scrollLeft($table.scrollLeft()); });
                                        // Resize top scrollbar inner div to match table width
                                        function syncWidth() {
                                            var table = document.getElementById('records_table');
                                            if (table) {
                                                var w = table.scrollWidth;
                                                $('#top-scrollbar > div').width(w);
                                            }
                                        }
                                        setInterval(syncWidth, 500);
                                    });
                                    </script>
                            <table>
                            <tbody>
                            <tr>
                            <td>
                            <button onclick="exportTableToCSV('dailydata.csv')" style="display:none;" id="csv" name="csv" class="btn btn-primary btn-sm">Export Daily</button>
                            </td>
                            <td>
                            <button onclick="exportTableToCSVsub('subdata.csv')" style="display:none;" id="csv2" name="csv2" class="btn btn-info btn-sm">Export Sub Daily</button>
                            </td>
                            </tr>
                            </tbody>

                            </table>
                            
                            
                            </div>
                        </div>
                    </div>
                </div>                   

            </div>
            <!-- END PAGE CONTENT-->
            <?php  require_once("assets/master/footer.php"); ?>
        </div>
    </div>
    <!-- END THEME CONFIG PANEL-->
    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!-- CORE PLUGINS-->
    <script src="assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/vendors/metisMenu/dist/metisMenu.min.js" type="text/javascript"></script>
    <script src="assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
    <script src="assets/vendors/select2/dist/js/select2.full.min.js" type="text/javascript"></script>
    <script src="assets/vendors/jquery-knob/dist/jquery.knob.min.js" type="text/javascript"></script>
    <script src="assets/vendors/moment/min/moment.min.js" type="text/javascript"></script>
    <script src="assets/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="assets/vendors/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="assets/vendors/jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
    <script src="./assets/vendors/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="assets/js/app.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    <script src="assets/js/scripts/form-plugins.js" type="text/javascript"></script>



    <script type="text/javascript">
       $(document).ready(function(){
            $("#submit").click(function(){
                $("#submit").val("Please Wait...").attr("disabled", true);
                var Table = document.getElementById("records_table");
                Table.innerHTML = "";
                var dataString = $("#station").val();
                var datee = $("#datee").val();

                // AJAX Code To Submit Form.
                $.ajax({
                    type: "POST",
                    url: "view_post.php",
                    data: {data : dataString, date:datee},
                    cache: false,
                    success: function(result){
                        if ($.trim(result) === '') {
                            $('#records_table').html("<tbody><tr><td>No Records Found.</td></tr></tbody>");
                            document.getElementById("csv").style.display='none';
                            document.getElementById("csv2").style.display='none';
                            return;
                        }

                        // view_post.php returns <thead> + <tbody> for all fields.
                        $('#records_table').html(result);
                        document.getElementById("csv").style.display='block';
                        document.getElementById("csv2").style.display='block';
                    },
                    complete: function(){
                            $('#submit').val("Search").attr("disabled", false);
                        }
                });
                return false;
            
            });
        });

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;

            // CSV file
            csvFile = new Blob([csv], {type: "text/csv"});

            // Download link
            downloadLink = document.createElement("a");

            // File name
            downloadLink.download = filename;

            // Create a link to the file
            downloadLink.href = window.URL.createObjectURL(csvFile);

            // Hide download link
            downloadLink.style.display = "none";

            // Add the link to DOM
            document.body.appendChild(downloadLink);

            // Click download link
            downloadLink.click();
        }

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) 
                month = '0' + month;
            if (day.length < 2) 
                day = '0' + day;

            return [day, month, year].join('-');
        }

        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("table tr");
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (var j = 0; j < cols.length-1; j++) 
                    if(j != 3){
                        if(j != 4){
                            if(j != 5){
                                if(j != 7){
                                    if(j == 10){
                                        if ( i == 0){
                                            row.push('Date Entry');
                                        }
                                        else{
                                            var from = cols[j].innerText.split("-");
                                            var test = from[1]+'/'+from[0]+'/'+from[2];
                                            //alert(test);
                                            var f = new Date(test);
                                            f.setDate(f.getDate()-1);
                                            row.push(formatDate(f)+" 09:00:00");
                                        }
                                    }
                                    else{
                                    if (cols[j].innerText != 'Export Daily'){
                                        row.push(cols[j].innerText);
                                    }
                                    }
                                }
                            }
                        }
                    }
                    //row.push(cols[j].innerText);
                
                csv.push(row.join(","));        
            }

            // Download CSV file
            downloadCSV(csv.join("\n"), filename);
        }

        function exportTableToCSVsub(filename) {
            var csv = [];
            var rows = document.querySelectorAll("table tr");
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (var j = 0; j < cols.length-1; j++) 
                    if(j != 2){
                        if(j != 6){
                            if(j != 8){
                                if(j != 9){
                                    if (cols[j].innerText != 'Export Daily'){
                                        row.push(cols[j].innerText);
                                    }
                                }
                            }
                        }
                    }
                    //row.push(cols[j].innerText);
                
                csv.push(row.join(","));        
            }

            // Download CSV file
            downloadCSV(csv.join("\n"), filename);
        }
    
    </script>
</body>

</html>