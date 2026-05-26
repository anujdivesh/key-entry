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
    $uname = trim($_POST["ddlusername"]);
    $password = trim($_POST["password"]);
    $con_password = trim($_POST["confirmpassword"]);

    if(empty(trim($uname))){
        $username_err = "Please enter a username.";
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

    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        if($auth_user->reset_password($uname, $password)){
            echo '<script language="javascript">';
            echo 'alert("Sucessfully Changed user Password !");';
            echo 'window.location.href = "resetpassword.php";';
            echo '</script>';
        }
        else{
            echo '<script language="javascript">';
            echo 'alert("Unsuccessful, Please try Again !");';
            echo 'window.location.href = "resetpassword.php";';
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
                    <li class="breadcrumb-item">Reset Password</li>
                </ol>
            </div>
            <div class="page-content fade-in-up">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox">
                            <div class="ibox-head">
                                <div class="ibox-title">Reset Password</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                <form action="resetpassword.php" method="post" id="myForm">
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Username</label>
                                            <select class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" name="ddlusername">
                                                <?php
                                                $obj = new DIV();
                                                $userRow1 = $obj->getuserid();
                                                echo "<option value=''>-- Please Select Username -- </option>";
                                                foreach($userRow1 as $row){
                                                    echo "<option value=". $row['id'] .">" . $row['username'] . "</option>";
                                                }    
                                                ?>
                                            </select>
                                            <div class="invalid-feedback"><?php echo $username_err; ?></div>
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