<?php
  require_once("../app_code/session.php");
  require_once("../app_code/class.user.php");
  require_once("../app_code/class.division.php");

  $username_err = $confirm_password_err = $password_err = $ddldivision_err = $ddlrole_err = $email_err = "";
  $auth_user = new USER();

  $user_id = $_SESSION['user_session_id'];
  $username = $auth_user->getUsername($user_id);

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST['Insert']))
    {
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $uname = trim($_POST["name"]);
    $password = trim($_POST["password"]);
    $con_password = trim($_POST["confirmpassword"]);
    $division_id =  trim($_POST["ddldivision"]);
    $role_id =  trim($_POST["ddlrole"]);
    $email =  trim($_POST["email"]);
    $station =  trim($_POST["station"]);

    if(empty(trim($uname))){
        $username_err = "Please enter a username.";
    }
    else if($auth_user->is_username_valid($uname) > 0){
        $username_err = "This username is already taken.";
    }

    if(empty(trim($password)))
    {
        $password_err = "Please enter a password.";     
    } 
    elseif(strlen(trim($password)) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } 

    if(empty(trim($con_password))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($con_password);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    if(empty(trim($_POST["ddldivision"]))){
        $ddldivision_err = "Please Select Organization.";     
    }
    if(empty(trim($_POST["station"]))){
        $station_err = "Please Enter Stations.";     
    }
    if(empty(trim($_POST["ddlrole"]))){
        $ddlrole_err = "Please Select Role.";     
    }
    if(empty(trim($_POST["email"]))){
        $email_err = "Please Enter Email.";     
    }
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)  && empty($ddldivision_err) && empty($ddlrole_err) && empty($email_err) && empty($station_err)){
        $str_arr = explode (",", $station);  
        $str_len = count($str_arr);
        if($auth_user->register($fname, $lname, $uname, $password, $email, $division_id,$station,$str_len, $role_id)){
            echo '<script language="javascript">';
            echo 'alert("Sucessfully Added a user !");';
            echo 'window.location.href = "adduser.php";';
            echo '</script>';
        }
        else{
            echo '<script language="javascript">';
            echo 'alert("Unsuccessful, Please try Again !");';
            echo 'window.location.href = "adduser.php";';
            echo '</script>';
        }
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
                    <li class="breadcrumb-item">Add User</li>
                </ol>
            </div>
            <div class="page-content fade-in-up">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox">
                            <div class="ibox-head">
                                <div class="ibox-title">Add</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post" id="myForm">
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Username</label>
                                            <input class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" type="text" placeholder="Username" name ="name" value="<?php echo isset($_POST["name"]) ? $_POST["name"] : ''; ?>">
                                            <div class="invalid-feedback"><?php echo $username_err; ?></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>First Name</label>
                                            <input class="form-control" type="text" placeholder="First Name" name ="fname" value="<?php echo isset($_POST["fname"]) ? $_POST["fname"] : ''; ?>">
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Last Name</label>
                                            <input class="form-control" type="text" placeholder="Last Name" name ="lname" value="<?php echo isset($_POST["lname"]) ? $_POST["lname"] : ''; ?>">
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Password</label>
                                            <input class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" type="password" placeholder="Password" name ="password">
                                            <div class="invalid-feedback"><?php echo $password_err; ?></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Confirm Password</label>
                                            <input class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" type="password" placeholder="Confirm Password" name ="confirmpassword">
                                            <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                        <label>Select Division</label>
                                            <select class="form-control <?php echo (!empty($ddldivision_err)) ? 'is-invalid' : ''; ?>" name="ddldivision">
                                                <?php
                                                $obj = new DIV();
                                                $userRow1 = $obj->getDivision();
                                                echo "<option value=''>-- Please Select Organization -- </option>";
                                                foreach($userRow1 as $row){
                                                    echo "<option value=". $row['id'] .">" . $row['org_name'] . "</option>";
                                                }    
                                                ?>
                                            </select>
                                            <span class="invalid-feedback"><?php echo $ddldivision_err; ?></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                        <label>Select Role</label>
                                            <select class="form-control <?php echo (!empty($ddlrole_err)) ? 'is-invalid' : ''; ?>" name="ddlrole">
                                                <?php
                                                $obj = new DIV();
                                                $userRow1 = $obj->getrole();
                                                echo "<option value=''>-- Please Select Role -- </option>";
                                                foreach($userRow1 as $row){
                                                    echo "<option value=". $row['role_id'] .">" . $row['role_name'] . "</option>";
                                                }    
                                                ?>
                                            </select>
                                            <span class="invalid-feedback"><?php echo $ddlrole_err; ?></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                        <label>Email</label>
                                            <input class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" type="text" placeholder="test@met.gov.fj" name ="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ''; ?>">
                                            <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                        <label>Station Access (,)</label>
                                            <input class="form-control <?php echo (!empty($station_err)) ? 'is-invalid' : ''; ?>" type="text" placeholder="V77542,V85622" name ="station" value="<?php echo isset($_POST["station"]) ? $_POST["station"] : ''; ?>">
                                            <div class="invalid-feedback"><?php echo $station_err; ?></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Save Changes" class="btn btn-primary" name="Insert">
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