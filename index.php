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

  $station_str = $auth_user->getStationStr($user_id);
  $station_arr = explode (",", $station_str); 

  $my_list = array();
  for ($i = 0; $i < count($station_arr); $i++) {
    $e = strval($station_arr[$i]);
    $b = $auth_user->getStationAcess($e);
    $c = $e.",".$b; 
    $sname = $auth_user->getStationName($station_arr[$i]);
    $my_list[$c] = $station_arr[$i]. " - ". $sname;
  }
  $len = count($my_list);
  if($len == 1){
    unset($my_list);
  }

  $role_id = $auth_user->getRoleID($user_id);
  if($role_id == '1'){
    unset($my_list);
  }


  $sensor_array = $auth_user->getsensors();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php  require_once("assets/master/header_user.php"); ?>
    <link rel="shortcut icon" href="logo1.ico">
    <title>FMS | Data Entry</title>
    <script type="text/javascript" src="jquery.min.js"></script>   
    <script type="text/javascript">   


$(document).ready(function() {   
    $('#station').change(function(){   
    var p = $('#station').val();
    var nameArr = p.split(',');
    document.getElementById( "error_para" ).innerHTML = "";
    
    var len = <?php echo count($sensor_array); ?>

    for (i = 0; i < len+1; i++) {
        if (i == 0){
            $('#Submit').hide();
        }
      $('#'+i).hide();
    }  

    for (i = 1; i < nameArr.length; i++) {
        if (i == 1){
            $('#Submit').show();
        }
      $('#'+nameArr[i]).show();
    } 
/*
    var people = ["false","false","false", "false", "false", "false", "false"];

    for (i = 1; i < nameArr.length; i++) {
        people[nameArr[i]-1] = 'true';
    } 
    

    var isp1p = (people[0] === 'true');
    var isp2p = (people[1] === 'true');
    var isp3p = (people[2] === 'true');
    var isp4p = (people[3] === 'true');
    var isp5p = (people[4] === 'true');
    var isp6p = (people[5] === 'true');
    var isp7p = (people[6] === 'true');

    

    

    $('#login-form').validate({
                errorClass: "help-block",
                rules: {
                    p1p:{
                        required:isp1p
                    },
                    p2p: {
                        required: isp2p
                    },
                    p3p: {
                        required: isp3p
                    },
                    p4p: {
                        required: isp4p
                    },
                    p5p: {
                        required: isp5p
                    },
                    p6p: {
                        required: isp6p
                    },
                    p7p: {
                        required: isp7p
                    }
                },
                highlight: function(e) {
                    $(e).closest(".form-group").addClass("has-error")
                },
                unhighlight: function(e) {
                    $(e).closest(".form-group").removeClass("has-error")
                },
            });
*/
    });   
    });   

    function validateForm() {
        var error = "*Please Fill All Required Fields.";
        if(document.getElementById("1").style.display != "none") {
            if(document.getElementById("p1p").value == "") {
                document.getElementById( "error_para" ).innerHTML = error;
                return false;
            } 
        }
        if(document.getElementById("2").style.display != "none") {
            if(document.getElementById("p2p").value == "") {
                document.getElementById( "error_para" ).innerHTML = error;
                return false;
            } 
        }
        if(document.getElementById("3").style.display != "none") {
            if(document.getElementById("p3p").value == "") {
                document.getElementById( "error_para" ).innerHTML = error;
                return false;
            } 
        }
        if(document.getElementById("4").style.display != "none") {
            if(document.getElementById("p4p").value == "") {
                document.getElementById( "error_para" ).innerHTML = error;
                return false;
            } 
        }
        if(document.getElementById("5").style.display != "none") {
            if(document.getElementById("p5p").value == "") {
                document.getElementById( "error_para" ).innerHTML = error;
                return false;
            } 
        }
        if(document.getElementById("6").style.display != "none") {
            if(document.getElementById("p6p").value == "") {
                document.getElementById( "error_para" ).innerHTML = error;
                return false;
            } 
        }
        if(document.getElementById("7").style.display != "none") {
            if(document.getElementById("p7p").value == "") {
                document.getElementById( "error_para" ).innerHTML = error;
                return false;
            } 
        }
        //check time
        var dateString = document.getElementById('datee').value;
        var dateParts = dateString.split("/");
        var dateObject = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]); 


        var dateFirst = new Date("06/10/2020");
        var dateSecond = new Date();
        var now = dateSecond.getMonth()+1 + '/' + dateSecond.getDate() + '/' +  dateSecond.getFullYear();
        var first = new Date(now);
        var timeDiff = Math.abs(first.getTime() - dateObject.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
        
        if (dateObject.getTime() >= first.getTime()){
            if (diffDays >= 1){
                document.getElementById( "error_para" ).innerHTML = "*Invalid Date";
                return false;
            }
        }
	else{
            if (diffDays >= 5){
                document.getElementById( "error_para" ).innerHTML = "*Invalid Date";
                return false;
            }
        }
        //check time end
    } 

        function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }




    </script>
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
                <h1 class="page-title">Data Entry</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html"><i class="la la-home font-20"></i></a>
                    </li>
                    <li class="breadcrumb-item">Online Key Enter</li>
                </ol>
            </div>
            <div class="page-content fade-in-up">
            <form id="login-form" action="send_data.php" method="post" onsubmit="return validateForm()" name="myForm" > 
                <div class="row">
                
                    <div class="col-md-12">
                        <div class="ibox ibox-info">
                            <div class="ibox-head">
                                <div class="ibox-title">Details</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>Station</label>
                                            <select class="form-control" name="station" id="station">
                                            <option value="" selected="selected">--- Select Station ---</option>
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
                                                <input class="form-control" type="text" value="<?php echo date('d/m/Y') ?>" name="datee" id = "datee">
                                            </div>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>Time</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="name" value="09:00 AM" disabled="">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>Observer</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="name" value="<?php echo $username; ?>" disabled="">
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox ibox-primary">
                            <div class="ibox-head">
                                <div class="ibox-title">Input</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                    <div class="row">
                                        <?php
                                            foreach($sensor_array as $row){
                                                echo "<div class='col-sm-2 form-group' id='".$row['id']."' style='display: none;'>";
                                                echo "<label>". $row['sensor_name'] ." <span style='color:red;'>*</span></label>";
                                                echo "<input class='form-control' type='text' name ='p".$row['id']."p' id ='p".$row['id']."p' onkeypress='return isNumberKey(event)'>";
                                                echo "</div>";
                                            }


                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Submit" class="btn btn-primary" name="Submit" id="Submit" style="display:none;">
                                        <p style="color:red;" id="error_para" ></p>
                                        <p style="color:darkgreen;" id="msg" ><u>Note</u>: Enter <b>999</b> for Missing Values</p>
                                    </div>

                                
                            </div>
                        </div>
                    </div>
                </div>
                </form>                            

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

</body>

</html>
