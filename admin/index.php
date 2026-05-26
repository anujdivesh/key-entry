<?php
  require_once("../app_code/session.php");
  require_once("../app_code/class.user.php");
  $auth_user = new USER();

  $user_id = $_SESSION['user_session_id'];
  $username = $auth_user->getUsername($user_id);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php  require_once("../assets/master/header.php"); ?>
</head>

<body class="fixed-navbar">
    <div class="page-wrapper">
        <!-- START HEADER-->
        <?php  require_once("../assets/master/userheader.php"); ?>
        <!-- END HEADER-->
        <!-- START SIDEBAR-->
        <?php  require_once("../assets/master/nav.php"); ?>
        <!-- END SIDEBAR-->
        <div class="content-wrapper">
            <!-- START PAGE CONTENT-->
            <div class="page-content fade-in-up">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="ibox bg-success color-white widget-stat">
                            <div class="ibox-body">
                                <h2 class="m-b-5 font-strong"><?php echo $auth_user->widget1(); ?></h2>
                                <div class="m-b-5">VALID CONTRACTS</div><i class="ti-stats-up widget-stat-icon"></i>
                                <div><i class="fa fa-level-up m-r-5"></i><small></small></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="ibox bg-info color-white widget-stat">
                            <div class="ibox-body">
                                <h2 class="m-b-5 font-strong"><?php echo $auth_user->widget2(); ?></h2>
                                <div class="m-b-5">TOTAL USERS</div><i class="ti-user widget-stat-icon"></i>
                                <div><i class="fa fa-level-up m-r-5"></i><small></small></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="ibox bg-warning color-white widget-stat">
                            <div class="ibox-body">
                                <h2 class="m-b-5 font-strong"><?php echo $auth_user->widget4(); ?></h2>
                                <div class="m-b-5">TOP LEAVE TYPE USED</div><i class="ti-bar-chart widget-stat-icon"></i>
                                <div><i class="fa fa-level-up m-r-5"></i><small></small></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="ibox bg-danger color-white widget-stat">
                            <div class="ibox-body">
                                <h2 class="m-b-5 font-strong"><?php echo $auth_user->widget3(); ?></h2>
                                <div class="m-b-5">EXPIRED CONTRACTS</div><i class="ti-stats-down widget-stat-icon"></i>
                                <div><i class="fa fa-level-down m-r-5"></i><small></small></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="ibox">
                            <div class="ibox-body">
                                <div class="flexbox mb-4">
                                    <div>
                                        <h3 class="m-0">Statistics</h3>
                                        <div>Leave Requests for the current year</div>
                                    </div>
                                </div>
                                <div>
                                    <canvas id="bar_chart" style="height:450px;"></canvas>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="ibox">
                        <div class="ibox-head">
                                <div class="ibox-title">HR Module</div>
                            </div>
                            <div class="ibox-body">
                                <ul class="list-group list-group-divider list-group-full tasks-list">
                                    <li class="list-group-item task-item">
                                        <div>
                                            <label class="ui-checkbox ui-checkbox-gray ui-checkbox-success">
                                                <input type="checkbox">
                                                <span class="input-span"></span>
                                                <span class="task-title">Contract Management</span>
                                            </label>
                                        </div>
                                        <div class="task-data"><small class="text-muted">Module</small></div>
                                        <div class="task-actions">
                                            <a href="javascript:;"><i class="fa fa-edit text-muted m-r-10"></i></a>
                                            <a href="javascript:;"><i class="fa fa-trash text-muted"></i></a>
                                        </div>
                                    </li>
                                    <li class="list-group-item task-item">
                                        <div>
                                            <label class="ui-checkbox ui-checkbox-gray ui-checkbox-success">
                                                <input type="checkbox">
                                                <span class="input-span"></span>
                                                <span class="task-title">Request Leaves</span>
                                            </label>
                                        </div>
                                        <div class="task-data"><small class="text-muted">Module</small></div>
                                        <div class="task-actions">
                                            <a href="javascript:;"><i class="fa fa-edit text-muted m-r-10"></i></a>
                                            <a href="javascript:;"><i class="fa fa-trash text-muted"></i></a>
                                        </div>
                                    </li>
                                    <li class="list-group-item task-item">
                                        <div>
                                            <label class="ui-checkbox ui-checkbox-gray ui-checkbox-success">
                                                <input type="checkbox">
                                                <span class="input-span"></span>
                                                <span class="task-title">Circulate leave Request</span>
                                            </label>
                                        </div>
                                        <div class="task-data"><small class="text-muted">Module</small></div>
                                        <div class="task-actions">
                                            <a href="javascript:;"><i class="fa fa-edit text-muted m-r-10"></i></a>
                                            <a href="javascript:;"><i class="fa fa-trash text-muted"></i></a>
                                        </div>
                                    </li>
                                    <li class="list-group-item task-item">
                                        <div>
                                            <label class="ui-checkbox ui-checkbox-gray ui-checkbox-success">
                                                <input type="checkbox">
                                                <span class="input-span"></span>
                                                <span class="task-title">Leave Request Approvals</span>
                                            </label>
                                        </div>
                                        <div class="task-data"><small class="text-muted">Module</small></div>
                                        <div class="task-actions">
                                            <a href="javascript:;"><i class="fa fa-edit text-muted m-r-10"></i></a>
                                            <a href="javascript:;"><i class="fa fa-trash text-muted"></i></a>
                                        </div>
                                    </li>
                                    <li class="list-group-item task-item">
                                        <div>
                                            <label class="ui-checkbox ui-checkbox-gray ui-checkbox-success">
                                                <input type="checkbox">
                                                <span class="input-span"></span>
                                                <span class="task-title">Notification on Contract Expiry</span>
                                            </label>
                                        </div>
                                        <div class="task-data"><small class="text-muted">Module</small></div>
                                        <div class="task-actions">
                                            <a href="javascript:;"><i class="fa fa-edit text-muted m-r-10"></i></a>
                                            <a href="javascript:;"><i class="fa fa-trash text-muted"></i></a>
                                        </div>
                                    </li>
                                    <li class="list-group-item task-item">
                                        <div>
                                            <label class="ui-checkbox ui-checkbox-gray ui-checkbox-success">
                                                <input type="checkbox">
                                                <span class="input-span"></span>
                                                <span class="task-title">Monitoring Leave Utilization</span>
                                            </label>
                                        </div>
                                        <div class="task-data"><small class="text-muted">Module</small></div>
                                        <div class="task-actions">
                                            <a href="javascript:;"><i class="fa fa-edit text-muted m-r-10"></i></a>
                                            <a href="javascript:;"><i class="fa fa-trash text-muted"></i></a>
                                        </div>
                                    </li>
                                    <li class="list-group-item task-item">
                                        <div>
                                            <label class="ui-checkbox ui-checkbox-gray ui-checkbox-success">
                                                <input type="checkbox">
                                                <span class="input-span"></span>
                                                <span class="task-title">General Enquiry</span>
                                            </label>
                                        </div>
                                        <div class="task-data"><small class="text-muted">Module</small></div>
                                        <div class="task-actions">
                                            <a href="javascript:;"><i class="fa fa-edit text-muted m-r-10"></i></a>
                                            <a href="javascript:;"><i class="fa fa-trash text-muted"></i></a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- END PAGE CONTENT-->
            <?php  require_once("../assets/master/userheader.php"); ?>
        </div>
    </div>
    <!-- BEGIN THEME CONFIG PANEL-->
    
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
    <script src="../assets/vendors/chart.js/dist/Chart.min.js" type="text/javascript"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap-2.0.3.min.js" type="text/javascript"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
    <script src="../assets/vendors/jvectormap/jquery-jvectormap-us-aea-en.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="../assets/js/app.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    <script>

        $(function() {
            var myData=[<?php 
                    echo $auth_user->chart_data();
                ?>];

                    var a = {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"],
                    datasets: [{
                        label: "Leave Request",
                        borderColor: 'rgba(52,152,219,1)',
                        backgroundColor: 'rgba(52,152,219,1)',
                        pointBackgroundColor: 'rgba(52,152,219,1)',
                        data: myData
                    }]
                },
                t = {
                    responsive: !0,
                    maintainAspectRatio: !1
                },
                e = document.getElementById("bar_chart").getContext("2d");
            new Chart(e, {
                type: "line",
                data: a,
                options: t
            });

        });
        
    </script>
</body>

</html>