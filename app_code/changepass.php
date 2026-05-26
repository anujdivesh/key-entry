<?php
require_once "class.user.php";
require_once("session.php");
$email = new USER();

$user=$_POST['u'];
$password1=$_POST['p1'];
$password2=$_POST['p2'];

if ( strcmp($password1,$password2) != 0){
    echo 3;
}
else{
    if($email->update_passwordid($user, $password1)){
        echo 1;
    }
    else{
        echo 2;
    }
}




?>