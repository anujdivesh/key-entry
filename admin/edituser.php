<?php
  require_once("../app_code/session.php");
  require_once("../app_code/class.user.php");
  require_once("../app_code/class.division.php");
  $auth_user = new USER();

  $user_id = $_SESSION['user_session_id'];
  $username = $auth_user->getUsername($user_id);

  if(isset($_GET['edit_id'])) {
    $userid =  base64_decode(urldecode($_GET['edit_id']));
    $query = "select id, first_name, last_name, username, organization_id, organization_value, role_id, role_value, is_active, email, counter, count_station, station_access ";
	$query .="from user_control ";
    $query .="where id = :di";
    
    $userstmt = $auth_user->runQuery($query);
    $userstmt->execute(array(":di"=>$userid));
    $userinfo=$userstmt->fetch(PDO::FETCH_ASSOC);

    $TK = $userinfo['id'];
    $uname = $userinfo['username'];
    $fname = $userinfo['first_name'];
    $lname = $userinfo['last_name'];
    $division = $userinfo['organization_value'];
    $role = $userinfo['role_value'];
    $status = $userinfo['is_active'];
    if($status == 'Y'){ $status = 'Yes';} else {$status = 'No';}
    $email = $userinfo['email'];
    $divid = $userinfo['organization_id'];
    $roleid = $userinfo['role_id'];
    $access = $userinfo['station_access'];
    $scount = $userinfo['count_station'];
    $counter = $userinfo['counter'];

  }

  if($_SERVER["REQUEST_METHOD"] == "POST"){

    if (isset($_POST['Update']))
    {
        $ddldivision = trim($_POST["ddldivision"]);
        $ddlrole = trim($_POST["ddlrole"]);
        $ddlstatus = trim($_POST["ddlstatus"]);
        $email = trim($_POST["email"]);
        $fname = trim($_POST["fname"]);
        $lname = trim($_POST["lname"]);
        $access = trim($_POST["access"]);
        $str_arr = explode (",", $access);  
        $str_len = count($str_arr);
        $counter = trim($_POST["counter"]);
        $userid =  base64_decode(urldecode($_GET['edit_id']));

        if($auth_user->update_user($userid, $ddldivision, $ddlrole, $ddlstatus, $email, $fname, $lname, $access, $str_len, $counter)){
            echo '<script language="javascript">';
            echo 'alert("Sucessfully Modified a user !");';
            echo 'window.location.href = "useraccess.php";';
            echo '</script>';
        }
        else{
            echo '<script language="javascript">';
            echo 'alert("Unsuccessful, Please try Again !");';
            echo 'window.location.href = "useraccess.php";';
            echo '</script>';
        }

    }

    if (isset($_POST['Save']))
    {
        header ('Location: useraccess.php');
    }
  }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php  require_once("../assets/master/header.php"); ?>
</head>

<body class="fixed-navbar">
    <div class="page-wrapper">
        <?php  require_once("../assets/master/userheader.php"); ?>
        <!-- START SIDEBAR-->
        <?php  require_once("../assets/master/nav.php"); ?>
        <!-- END SIDEBAR-->
        <div class="content-wrapper">
            <!-- START PAGE CONTENT-->
            <div class="page-heading">
                <h1 class="page-title">Manage Access</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="index.html"><i class="la la-home font-20"></i></a>
                    </li>
                    <li class="breadcrumb-item">Search</li>
                    <li class="breadcrumb-item">Edit User</li>
                </ol>
            </div>
            <div class="page-content fade-in-up">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox">
                            <div class="ibox-head">
                                <div class="ibox-title">Edit</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                <form action="<?php $_SERVER['PHP_SELF'];?>" method="post">
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>User ID</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="id" value="<?php echo $TK ?>" disabled="">
                                        </div>
                                        <div class="col-sm-9 form-group">
                                            <label>Username</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="name" value="<?php echo $uname ?>" disabled="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>Email</label>
                                            <input class="form-control" type="text" placeholder="test@met.gov.fj" name ="email" value="<?php echo $email ?>">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                        <label>First Name</label>
                                            <input class="form-control" type="text" placeholder="First Name" name ="fname" value="<?php echo $fname ?>">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                        <label>Last Name</label>
                                            <input class="form-control" type="text" placeholder="Last Name" name ="lname" value="<?php echo $lname ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Current Organization</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="division" id = "division" value="<?php echo $division ?>" disabled="">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                        <label>Change Organization to</label>
                                            <select class="form-control" name="ddldivision">
                                                <?php
                                                $obj = new DIV();
                                                $userRow1 = $obj->getDivision();
                                                echo "<option value='".$divid."'>-- Please Select Organization -- </option>";
                                                foreach($userRow1 as $row){
                                                    echo "<option value=". $row['id'] .">" . $row['org_name'] . "</option>";
                                                }    
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Current Role</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="role" value="<?php echo $role ?>" disabled="">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                        <label>Change Role to</label>
                                            <select class="form-control" name="ddlrole">
                                                <?php
                                                $obj = new DIV();
                                                $userRow1 = $obj->getrole();
                                                echo "<option value='".$roleid."'>-- Please Select Role -- </option>";
                                                foreach($userRow1 as $row){
                                                    echo "<option value=". $row['role_id'] .">" . $row['role_name'] . "</option>";
                                                }    
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Current Status</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="status" value="<?php echo $status ?>" disabled="">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                        <label>Change Status to</label>
                                            <select class="form-control" name="ddlstatus">
                                            <?php 
                                                echo "<option value='".$status."'>-- Please Select Status -- </option>";
                                                echo "<option value='Y'>Yes</option>";
                                                echo "<option value='N'>No</option>";
                                            ?>    
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>Station Access (,)</label>
                                            <input class="form-control" type="text" placeholder="Stations" name ="access" value="<?php echo $access ?>">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                        <label>Station Count</label>
                                            <input class="form-control" type="text" placeholder="Station Count" name ="scount" value="<?php echo $scount ?>" disabled="">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                        <label>Counter</label>
                                            <input class="form-control" type="text" placeholder="Counter" name ="counter" value="<?php echo $counter ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Save Changes" class="btn btn-primary" name="Update">
                                        <input type="submit" value="Go Back" class="btn btn-danger" name="Save">
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
            <?php  require_once("../assets/master/footer.php"); ?>
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
    <script src="../assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="../assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="../assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../assets/vendors/metisMenu/dist/metisMenu.min.js" type="text/javascript"></script>
    <script src="../assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
    <!-- CORE SCRIPTS-->
    <script src="../assets/js/app.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->
</body>

</html>