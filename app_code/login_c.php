<?php
// Initialize the session
session_start();

require_once "auth.php";
$login = new HELPER();
// Define variables and initialize with empty values

$username_err = $password_err = "";

// Processing form data when form is submitted
$uname = $_POST['u'];
$upass = $_POST['p'];

// Validate credentials
$count_attempt = $login->select_counter(strtolower($uname));
if($count_attempt <= 3){
    if($login->doLogin(strtolower($uname),$upass) || $login->doLoginPhone(strtolower($uname),$upass) || $login->doLoginUsername(strtolower($uname),$upass))
    {
        $ret_val = 1;
        $login->update_counter_zero(strtolower($uname));
        $role = trim($_SESSION['user_role_id']);
        if($role == '1'){
            $ret_val = 1;
        }
        else if($role == '2'){
            $ret_val = 2;
        }    
        else if($role == '3'){
            $ret_val = 3;
        }
        $req_counter = $login->get_ifrequest(strtolower($uname)); 
        if ($req_counter >= 1){
            $ret_val = 6;
        }   
        echo $ret_val;   
    }
    else
    {
        if($login->update_counter(strtolower($uname))){
            echo 4;
        }
    }
}
else{
    echo 5;
}

?>