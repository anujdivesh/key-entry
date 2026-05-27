<?php
  require_once("app_code/session.php");
  require_once("app_code/class.user.php");
  require_once("app_code/class.division.php");
  $auth_user = new USER();

  $user_id = $_SESSION['user_session_id'];
  $username = $auth_user->getfullname($user_id);
  $emailadd = $auth_user->getemaill($user_id);
  $index_act = 'active';
  $view_act = 'inactive';
  $date = $station_strr = "";
  try{
    if (isset($_POST['station'])) {
        $station_strr = $_POST['station'];
    }
    else{
        header("Location: error_404.php"); 
    }
    //$station_strr = $_POST['station'];
    $ar = explode (",", $station_strr); 
    $sname = $auth_user->getStationName($ar[0]);
    $station_no = $ar[0];
    $station_det = $ar[0]. " - ". $sname;
    unset($ar[0]);
    $station_arrr = array_values($ar);
    $station_arrr = array_map('trim', $station_arrr);
    $station_arrr = array_values(array_filter($station_arrr, 'is_numeric'));
    // Preserve station sensor order as configured (do not sort).
    $my_list = array();
    for ($i = 0; $i < count($station_arrr); $i++) {
        $e = strval($station_arrr[$i]);
        $c = $e;
        $real_v = "";
        $my_list[$e] = $station_arrr[$i];
    }
    
    if (isset($_POST['datee'])) {
        $date = $_POST['datee'];
    }
    }
    catch(Exception $e) {
        throw 'Message: ' .$e->getMessage();
    }
  


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php  require_once("assets/master/header_user.php"); ?>
    <link rel="shortcut icon" href="logo1.ico">
    <title>FMS | Submission</title>
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
                <h1 class="page-title">Send Data</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html"><i class="la la-home font-20"></i></a>
                    </li>
                    <li class="breadcrumb-item">Confirm Details</li>
                </ol>
            </div>
            <div class="page-content fade-in-up">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox ibox-success">
                            <div class="ibox-head">
                                <div class="ibox-title">Submission</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                <div id="form">
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>Station</label>
                                            <input style="color:blue;" class="form-control" type="text" placeholder="Username" name ="station" id = "station" value="<?php echo $station_det; ?>" disabled="">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>Date</label>
                                            <div class="input-group date">
                                                <span class="input-group-addon bg-white"><i class="fa fa-calendar"></i></span>
                                                <input style="color:blue;" class="form-control" type="text" value="<?php echo $date; ?>" name="date" id="date" disabled="">
                                            </div>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>Time</label>
                                            <input style="color:blue;" class="form-control" type="text" placeholder="Username" name ="name" value="09:00 AM" disabled="">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>Observer</label>
                                            <input style="color:blue;" class="form-control" type="text" placeholder="Username" name ="name" value="<?php echo $username; ?>" disabled="">
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <?php
                                            $has_dry = false;
                                            $has_wet = false;
                                            $dry = $wet = 0;
                                        $dew_sensor_id = $auth_user->getDewPointSensorId();
                                            foreach($my_list as $key => $value){                                                
                                                // RH is calculated; do not show it as an entered sensor.
                                                if ((string)$value === '8') {
                                                    continue;
                                                }
                                            // Dew Point is calculated; do not show it as an entered sensor.
                                            if ($dew_sensor_id !== null && (string)$value === (string)$dew_sensor_id) {
                                                continue;
                                            }
                                                $val = $_POST['p'.$key.'p'];
                                                if (empty($_POST['p'.$key.'p']) && $_POST['p'.$key.'p'] != '0'){
                                                    $val='null';
                                                }
                                                echo "<div class='col-sm-2 form-group'>";
                                                echo "<label>". $auth_user->getSensorName($value) ."</label>";
                                                echo "<input style='color:blue;' class='form-control sensor-confirm-value' type='text' name ='".$value."' id='".$value."' value='".$val."' disabled=''>";
                                                echo "</div>";
                                                if($value == 2)
                                                {
                                                    $has_dry = true;
                                                    $dry = $val;
                                                }
                                                if($value == 3)
                                                {
                                                    $has_wet = true;
                                                    $wet = $val;
                                                }
                                            }
                                            if ($has_dry && $has_wet && is_numeric($dry) && is_numeric($wet) && (float)$dry !== 999.0 && (float)$wet !== 999.0){
                                                $rh = $auth_user->rh_calculator((float)$dry, (float)$wet);

                                                echo "<div class='col-sm-2 form-group'>";
                                                echo "<label>RH</label>";
                                                echo "<input style='color:blue;' class='form-control sensor-confirm-value' type='text' name ='8' id='8' value='".$rh."' disabled=''>";
                                                echo "</div>";

                                            if ($dew_sensor_id !== null) {
                                                $dew = $auth_user->dew_point_calculator((float)$dry, (float)$wet);
                                                if ($dew !== null) {
                                                    $dew_label = $auth_user->getSensorName($dew_sensor_id);
                                                    if (empty($dew_label)) {
                                                        $dew_label = 'Dew Point';
                                                    }
                                                    echo "<div class='col-sm-2 form-group'>";
                                                    echo "<label>".$dew_label."</label>";
                                                    echo "<input style='color:blue;' class='form-control' type='text' value='".number_format($dew,1)."' disabled=''>";
                                                    echo "</div>";
                                                }
                                            }
                                            }


                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea class="form-control" rows="1" id="remark" name="remark" ></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input id="submit" type="button" value="Confirm" class="btn btn-success">
                                        <input id="btntest" type="button" class="btn btn-danger" value="Back" onclick="window.location.href = 'index.php'" />
                                    </div>

                                </div>
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
    

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#FFCCCB;" id="popme">
                        <h5 class="modal-title" id="exampleModalCenterLabel"><div id="head"></div></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                    <div id="message"></div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="location.href = 'index.php';" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
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
    <!-- CORE SCRIPTS-->
    <script src="assets/js/app.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    <script src="assets/js/scripts/form-plugins.js" type="text/javascript"></script>

    <script type="text/javascript">
       $(document).ready(function(){
            $("#submit").click(function(){
                $("#submit").val("Please Wait...").attr("disabled", true);
                var station = "<?php echo $station_no; ?>"; 
                var user_id = "<?php echo $user_id; ?>"; 
                var date_enter = $("#date").val();
                var remark = $("#remark").val();
                var dataParts = [];
                $(".sensor-confirm-value").each(function(){
                    var sensorId = $(this).attr("id");
                    var sensorValue = $(this).val();
                    // RH (id=8) is calculated on the server; don't submit it.
                    if (sensorId === '8') {
                        return;
                    }
                    if (sensorId && sensorValue != null) {
                        dataParts.push(sensorId + '=' + sensorValue);
                    }
                });
                var dataString = dataParts.join('&');
                
                // AJAX Code To Submit Form.
                $.ajax({
                    type: "POST",
                    url: "ajaxsubmit.php",
                    data: {data : dataString, station:station, user_id:user_id, date:date_enter, remark:remark},
                    cache: false,
                    success: function(result){
                        if(result == 1){
                            $('#popme').css('background-color', '#FFCCCB');
                            $("#message").html("<span style='font-weight:bold;'>Error</span>: Record Exists !");
                            $("#head").html('Error');
                            $('#exampleModalCenter').modal('show');
                        }
                        else if(result == 2){
                            $('#popme').css('background-color', '#92aa5c');
                            $("#message").html("<span style='font-weight:bold;'>Success</span>: Record Submitted !");
                            $("#head").html('Success');
                            $('#exampleModalCenter').modal('show');
                        }
                        else{
                            $('#popme').css('background-color', '#FFCCCB');
                            $("#message").html("<span style='font-weight:bold;'>Error</span>: Unexpected !");
                            $("#head").html('Error');
                            $('#exampleModalCenter').modal('show');
                        }
                        //$('#exampleModalCenter').modal('show');
                        //window.alert(result);
                        //window.location.href='index.php';
                        
                    },
                    complete: function(){
                            $('#submit').val("Confirm").attr("disabled", false);
                        }
                });
                return false;
            
            });
        });
    
    </script>
</body>

</html>