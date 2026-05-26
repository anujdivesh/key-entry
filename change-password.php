<?php
  require_once("app_code/session.php");
  require_once("app_code/class.user.php");
  require_once("app_code/class.division.php");
  $auth_user = new USER();

  $user_id = $_SESSION['user_session_id'];
  $username = $auth_user->getemaill($user_id);

  $req_counter = $auth_user->get_ifrequest(strtolower($username)); 

  if ($req_counter == 0){
    header("Location: index.php"); 
    } 


?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
    <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>FMS | Password</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="shortcut icon" href="img/logo1.ico">

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
        
        <link rel="stylesheet" href="plugins/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="plugins/ionicons/dist/css/ionicons.min.css">
        <link rel="stylesheet" href="plugins/icon-kit/dist/css/iconkit.min.css">
        <link rel="stylesheet" href="plugins/perfect-scrollbar/css/perfect-scrollbar.css">
        <link rel="stylesheet" href="dist/css/theme.min.css">
        <script src="src/js/vendor/modernizr-2.8.3.min.js"></script>
    </head>

    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg" style="background-image: url('img/auth/register-bg.jpg')">
                            <!---<div class="lavalite-overlay"></div>---->
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                        <div class="authentication-form mx-auto">
                            <div class="logo-centered">
                                <a href="index.php"><img src="img/logo.png" alt="" width="60px" height="60px"></a>
                            </div>
                            <center><h3>Fiji Meteorological Service</h3></center>
                            <h3>Change Password!</h3>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Email" disabled="" value="<?php echo $username; ?>">
                                    <i class="ik ik-user"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Password" required="" name="password1" id="password1">
                                    <i class="ik ik-lock"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Confirm Password" required="" name="password2" id="password2">
                                    <i class="ik ik-eye-off"></i>
                                </div>
                                <div class="sign-btn text-center">
                                    <input class="btn btn-theme" type="button" value="Submit" name="but_submit" id="but_submit" />
                                </div>
                        </div>
                    </div>
                </div>
            </div>
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
        
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script>window.jQuery || document.write('<script src="src/js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="plugins/popper.js/dist/umd/popper.min.js"></script>
        <script src="plugins/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="plugins/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
        <script src="plugins/screenfull/dist/screenfull.js"></script>
        <script src="dist/js/theme.js"></script>
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        
    <script type="text/javascript">
        var number = 2;
       $(document).ready(function(){
            $("#but_submit").click(function(){
                $("#but_submit").val("Please Wait...").attr("disabled", true);
                var email = "<?php echo $user_id; ?>";
                var pass1 = $("#password1").val().trim();
                var pass2 = $("#password2").val().trim();
                
                // AJAX Code To Submit Form.
                if( email != "" && pass1 != "" && pass2 != ""){
                $.ajax({
                    type: "POST",
                    url: "app_code/changepass.php",
                    data: {u : email, p1 : pass1, p2 : pass2},
                    cache: false,
                    success: function(result){
                        if(result == 1){
                            $('#popme').css('background-color', '#92aa5c');
                            $("#message").html("Your Password has been successfully changed !");
                            $("#head").html('Success');
                            $('#exampleModalCenter').modal('show');
                            number = 1;
                        }
                        else if(result == 3){
                            $('#popme').css('background-color', '#FFCCCB');
                            $("#message").html("We are Sorry ! Your Passwords do not match !");
                            $("#head").html('Error');
                            $('#exampleModalCenter').modal('show');
                        }
                        else{
                            $('#popme').css('background-color', '#FFCCCB');
                            $("#message").html("Unexpected Error Occured, Please contact Fiji Met Service");
                            $("#head").html('Error');
                            $('#exampleModalCenter').modal('show');
                        }
                    },
                    complete: function(){
                            $('#but_submit').val("Submit").attr("disabled", false);
                        }
                });
                }
                else{
                    $("#message").html("All Fields Are Required.");
                    $("#head").html('Error');
                    $('#exampleModalCenter').modal('show');
                    $('#but_submit').val("Submit").attr("disabled", false);
                }
            
            });

            $("#but_close").click(function(){
                if (number == 1){
                    window.location='app_code/logout.php?logout=true';
                }
                else{
                    $('#exampleModalCenter').modal('hide');
                }
                
            });

        });

        $("#password2").keyup(function(event) {
            if (event.keyCode === 13) {
                $("#but_submit").click();
            }
        });

    
    </script>
    </body>
</html>
