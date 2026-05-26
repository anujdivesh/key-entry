<?php
// Initialize the session
#session_start();

require_once "auth.php";
require 'mailclass.php';

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 5; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

$email = new HELPER();
// Define variables and initialize with empty values

// Processing form data when form is submitted
$uname = $_POST['u'];

// Validate credentials
$count_attempt = $email->email_exist(strtolower($uname));

if ($count_attempt >= 1){
    $password = randomPassword();

    if ($email->update_password(strtolower($uname), $password)){
        $username = $email->get_usernameemail(strtolower($uname));
        $subject="Password Reset";
        
        $body="Bula ! <br><br> You are receiving this email because we received a password reset request for your account. The Credentials are:<br><br>";
        $body .= "Username: ".$uname."<br>";
        $body .= "Password: ".$password."<br>";
        $body .= "Link: http://keyentry.met.gov.fj <br>";
        $body .="<br><br>Thank you<br>";
        $body .= "Fiji Meteorological Service";

        $obj = new sendmail;
		if($obj->mail(strtolower($uname), $subject, $body)){
            echo 1;
        }
        else{
            echo 2;
        }
		
    }
    else{
        echo 2;
    }

}
else{
    echo 3;
}

?>