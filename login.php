<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>FMS | Login</title>
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
        <div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg" style="background-image: url('img/auth/10.jpg')">
                            <!---<div class="lavalite-overlay"></div>---->
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                        <div class="authentication-form mx-auto">
                        
                            <div class="logo-centered">
                                <a href="index.php"><img src="img/logo.png" alt="" width="60px" height="60px"></a>
                            </div>
                            <center><h3>Fiji Meteorological Service</h3></center>
                            <h3>Sign In</h3>
                            <p>Happy to see you again!</p>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Email/Username/Phone" name="username" id="username">
                                    <i class="ik ik-user"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Password" name="password" id="password">
                                    <i class="ik ik-lock"></i>
                                </div>
                                <div class="row">
                                    <div class="col text-left">
                                    <a href="Online_TOD_Guide.pdf">User Guide ?</a>
                                    </div>
                                    <div class="col text-right">
                                        <a href="forgot-password.php">Forgot Password ?</a>
                                    </div>
                                </div>
                                <div class="sign-btn text-center">
                                    <input class="btn btn-theme" type="button" value="Sign In" name="but_submit" id="but_submit" />
                                </div>
                                
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#FFCCCB;">
                        <h5 class="modal-title" id="exampleModalCenterLabel">Login</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                    <div id="message"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

    <script type="text/javascript">
       $(document).ready(function(){
            $("#but_submit").click(function(){
                $("#but_submit").val("Please Wait...").attr("disabled", true);
                var username = $("#username").val().trim();
                var password = $("#password").val().trim();

                // AJAX Code To Submit Form.
                if( username != "" && password != "" ){
                $.ajax({
                    type: "POST",
                    url: "app_code/login_c.php",
                    data: {u : username, p:password},
                    cache: false,
                    success: function(result){
                        if(result == 1){
                            window.location = "index.php";
                        }
                        else if(result == 2){
                            window.location = "index.php";
                        }
                        else if(result == 3){
                            window.location = "index.php";
                        }
                        else if(result == 4){
                            $("#message").html("Your username or password are incorrect.");
                            $('#exampleModalCenter').modal('show');
                        }
                        else if(result == 6){
                            window.location = "change-password.php";
                        }
                        else{
                            $("#message").html("Too many Failed Attempts, Contact FMS.");
                            $('#exampleModalCenter').modal('show');
                        }
                    },
                    complete: function(){
                            $('#but_submit').val("Sign In").attr("disabled", false);
                        }
                });
                }
                else{
                    $("#message").html("Enter username and Password.");
                    $('#exampleModalCenter').modal('show');
                    $('#but_submit').val("Sign In").attr("disabled", false);
                }
            
            });
        });

        $("#password").keyup(function(event) {
            if (event.keyCode === 13) {
                $("#but_submit").click();
            }
        });

    
    </script>
        
    </body>
</html>
