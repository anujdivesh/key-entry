<?php
  require_once("../app_code/session.php");
  require_once("../app_code/class.user.php");
  require_once("../app_code/class.division.php");
  $auth_user = new USER();

  $user_id = $_SESSION['user_session_id'];
	
  $username = $auth_user->getUsername($user_id);

  
  $table= $name ="";
    
    if (isset($_POST['Enter']))
    {
        $name = trim($_POST["name"]);
        $ddldivision = trim($_POST["ddldivision"]);

        $table = "<div class='ibox'>";
        $table .="<div class='ibox-body'>";
        $table .="<div class='table-responsive'>";
        $table .="<table class='table'>";
        $table .="<thead>";
        $table .="<tr>";
        $table .= "<th>ID</th>";
        $table .= "<th>Username</th>";
        $table .= "<th>Organization</th>";
        $table .= "<th>Role</th>";
        $table .= "<th>Station Access</th>";
        $table .= "<th>Actions</th>";
        $table .= "</tr>";
        $table .= "</thead>";
        $table .=  "<tbody>";

        $userlist = $auth_user->search_users($name, $ddldivision);

        foreach($userlist as $row){
            $role = $row['role_name'];
            $table .= "<tr><td>".$row['id'] ."</td><td>". $row['username'] ."</td><td>". $row['organization_value'] ."</td><td>". $role ."</td><td>". $row['date'] ."</td>";
            $table .="<td><a href='edituser.php?edit_id=".urlencode(base64_encode($row['id']))."' class= 'btn btn-default btn-xs m-r-5' data-toggle='tooltip' data-original-title='Edit'><i class='fa fa-pencil font-14'></i></a></td></tr>";
        }    

        $table .=  "</tbody>";
        $table .=  "</table>";
        $table .=  "</div>";
        $table .=  "</div>";
        $table .=  "</div>";

    }

    if (isset($_POST['Clear']))
    {
        $table = "";
    }

    if (isset($_POST['Save']))
    {
        header ('Location: adduser.php');
    }
    if (isset($_POST['Reset']))
    {
        header ('Location: resetpassword.php');
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
                </ol>
            </div>
            <div class="page-content fade-in-up">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ibox">
                            <div class="ibox-head">
                                <div class="ibox-title">Search</div>
                                <div class="ibox-tools">
                                    <a class="ibox-collapse"><i class="fa fa-minus"></i></a>
                                    
                                </div>
                            </div>
                            <div class="ibox-body">
                                <form action="useraccess.php" method="post">
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>Username</label>
                                            <input class="form-control" type="text" placeholder="Username" name ="name" value="<?PHP print $name; ?>">
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label>Organization</label>
                                            <select class="form-control" name="ddldivision">
                                                <?php
                                                $obj = new DIV();
                                                $userRow1 = $obj->getDivision();
                                                echo "<option value=''>-- Please Select Organization -- </option>";
                                                foreach($userRow1 as $row){
                                                    echo "<option value=". $row['id'] .">" . $row['org_name'] . "</option>";
                                                }    
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" value="Search" class="btn btn-primary" name="Enter">
                                        <input type="submit" value="Clear" class="btn btn-danger" name="Clear">
                                        <input type="submit" value="Add User" class="btn btn-default" name="Save">
                                        <input type="submit" value="Reset Password" class="btn btn-warning" name="Reset">
                                    </div>
                                    <?php echo $table; ?>

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