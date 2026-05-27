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
  $p6p ="";

  if (!isset($_GET['data_id'])) {
    header("Location: error_404.php"); 
  }

  if(isset($_GET['data_id'])) {
        $dataid =  base64_decode(urldecode($_GET['data_id']));
        $query = "select obs_data.*, to_char( date_entry, 'DD-MON-YYYY HH:mi') as date_entry, TO_CHAR(created_at at time zone 'utc' at time zone 'Pacific/Fiji', 'DD-MON-YYYY hh12:mi:ss AM') as created_at, to_char( date_entry, 'YYYY-MM-DD') as dt_ent from obs_data where id = :di";
    
    $userstmt = $auth_user->runQuery($query);
    $userstmt->execute(array(":di"=>$dataid));
    $userinfo=$userstmt->fetch(PDO::FETCH_ASSOC);

    $station_no = $userinfo['station_no'];
    $station_value = $userinfo['station_value'];
    $logged_value = $userinfo['logged_value'];
    $is_edited = $userinfo['is_edited'];
    $is_processed = $userinfo['is_processed'];
    $date_entry = $userinfo['date_entry'];
    $created_at = $userinfo['created_at'];
    $remark = $userinfo['remarks'];
    $dt_ent = $userinfo['dt_ent'];

    if ($is_edited == 'N'){$is_edited = 'No';}else{$is_edited = 'Yes';}
    if ($is_processed == 'N'){$is_processed = 'No';}else{$is_processed = 'Yes';}

    $sname = $auth_user->getStationName($station_no);
    $ar = $auth_user->getStationAcess($station_no);
    $station_arrr = explode (",", $ar); 
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


    $prod_qty = '0';
    $button_cls = "warning";

    $dt_now = date("Y-m-d");
    $datetime1 = new DateTime($dt_now);
    $datetime2 = new DateTime($dt_ent);
    $interval = $datetime1->diff($datetime2);
    $date_diff = $interval->format('%a');

    $element_active = 'disabled';
    if ($date_diff == 0){
        $element_active = '';
    }


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
                <h1 class="page-title">Data Results</h1>
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
                        <div class="ibox ibox-warning">
                            <div class="ibox-head">
                                <div class="ibox-title">Search Results</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                </div>
                            </div>
                            <div class="ibox-body">

                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>Station No</label>
                                    <input class="form-control" type="text" placeholder="" name ="id" value="<?php echo $station_no; ?>" disabled="">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>Station Name</label>
                                    <input class="form-control" type="text" placeholder="" name ="name" value="<?php echo $station_value; ?>" disabled="">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>Entry Date </label>
                                    <input class="form-control" type="text" placeholder="" name ="name" value="<?php echo $date_entry; ?>" disabled="">
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                            <?php
                                $has_dry = false;
                                $has_wet = false;
									$has_dewpoint = false;
                                $dry = $wet = 0;
									$dew_sensor_id = $auth_user->getDewPointSensorId();

                                foreach($my_list as $key => $value){
                                    // RH is calculated; do not show it as an entered/editable sensor.
                                    if ((string)$key === '8') {
                                        continue;
                                    }
										// Dew Point is calculated; do not show it as an entered/editable sensor.
										if ($dew_sensor_id !== null && (string)$key === (string)$dew_sensor_id) {
											$has_dewpoint = true;
											continue;
										}
                                    $reference_col = $auth_user->getReference($value);
                                    $val = 'nan';
                                    if ($reference_col && array_key_exists($reference_col, $userinfo) && $userinfo[$reference_col] !== null && $userinfo[$reference_col] !== '') {
                                        $val = $userinfo[$reference_col];
                                    }
                                    echo "<div class='col-sm-2 form-group'>";
                                    echo "<label>". $auth_user->getSensorName($value) ."</label>";
                                    echo "<input style='color:blue;' class='form-control sensor-edit-value' type='text' name ='".$value."' id='".$key."' value='".$val."' ".$element_active.">";
                                    echo "</div>";
                                    if($key == 2)
                                    {
                                        $has_dry = true;
                                        $dry = $val;
                                    }
                                    if($key == 3)
                                    {
                                        $has_wet = true;
                                        $wet = $val;
                                    }
                                }
                                if ($has_dry && $has_wet && is_numeric($dry) && is_numeric($wet) && (float)$dry !== 999.0 && (float)$wet !== 999.0){
                                    $rhh = $auth_user->rh_calculator((float)$dry, (float)$wet);
                                    echo "<div class='col-sm-2 form-group'>";
                                    echo "<label>RH</label>";
                                    echo "<input style='color:blue;' class='form-control sensor-edit-value' type='text' name ='8' id='8' value='".number_format($rhh,1)."' disabled=''>";
                                    echo "</div>";

                                        if ($has_dewpoint && $dew_sensor_id !== null) {
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
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                        <label>Remarks</label>
                                        <textarea class="form-control" style="color:blue;" rows="2" id="remark" name="remark" <?php if ($element_active == 'disabled'){ ?> disabled <?php   } ?>><?php echo $remark; ?></textarea>
                                    </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>Entered By</label>
                                    <input class="form-control" type="text" placeholder="" name ="id" value="<?php echo $logged_value; ?>" disabled="">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>Edited</label>
                                    <input class="form-control" type="text" placeholder="" name ="name" value="<?php echo $is_edited; ?>" disabled="">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>Processed</label>
                                    <input class="form-control" type="text" placeholder="" name ="name" value="<?php echo $is_processed; ?>" disabled="">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>Created at</label>
                                    <input class="form-control" type="text" placeholder="" name ="name" value="<?php echo $created_at; ?>" disabled="">
                                </div>
                            </div>
                                <div class="form-group">
                                    <input id="submit" type="submit" value="Update" class="btn btn-warning" id="submit" name="submit" >
                                    <input id="btntest" type="button" class="btn btn-danger" value="Back" onclick="window.location.href = 'view.php'" />
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
                    <input class="btn btn-secondary" type="button" value="Close" name="but_close" id="but_close" />
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
    <script src="./assets/vendors/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="assets/js/app.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    <script src="assets/js/scripts/form-plugins.js" type="text/javascript"></script>


    
    <script type="text/javascript">
       var number = 2;
       $(document).ready(function(){
            $("#submit").click(function(){
                $("#submit").val("Please Wait...").attr("disabled", true);
                var station = "<?php echo $station_no; ?>"; 
                var date_enter = "<?php echo $dt_ent; ?>"; 
                var user_id = "<?php echo $user_id; ?>"; 
                var remark = $("#remark").val();
                var dataParts = [];
                $(".sensor-edit-value").each(function(){
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
                    url: "read_ajax.php",
                    data: {data : dataString, station:station, date:date_enter, remark:remark, user_id:user_id},
                    cache: false,
                    success: function(result){
                        if(result == 1){
                            $('#popme').css('background-color', '#92aa5c');
                            $("#message").html("<span style='font-weight:bold;'>Success</span>: Record Updated !");
                            $("#head").html('Success');
                            $('#exampleModalCenter').modal('show');
                            number = 1;
                        }
                        else if(result == 2){
                            $('#popme').css('background-color', '#FFCCCB');
                            $("#message").html("<span style='font-weight:bold;'>Error</span>: Unexpected !");
                            $("#head").html('Error');
                            $('#exampleModalCenter').modal('show');
                        }
                        else{
                            $('#popme').css('background-color', '#FFCCCB');
                            $("#message").html("<span style='font-weight:bold;'>Error</span>: Cannot update Record !");
                            $("#head").html('Error');
                            $('#exampleModalCenter').modal('show');
                        }
                        //window.alert(result);
                        //window.location.href='view.php';
                    },
                    complete: function(){
                            $('#submit').val("Update").attr("disabled", false);
                        }
                });
                //END AJAX
                
                return false;
            
            });

            $("#but_close").click(function(){
                if (number == 1){
                    window.location='view.php';
                }
                else{
                    $('#exampleModalCenter').modal('hide');
                }
                
            });
        });

    
    </script>

</body>

</html>