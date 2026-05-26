
<?php

require_once("app_code/session.php");
require_once("app_code/class.user.php");
require_once("app_code/class.division.php");
$auth_user = new USER();

//Fetching Values from URL
//$name2=$_POST['station'];
$data=$_POST['data'];
$station=$_POST['station'];
$user_id=$_POST['user_id'];
$date=$_POST['date'];
$remark=$_POST['remark'];

$data_arr = explode ("&", $data);  

//check 

if ($auth_user->check_record($station, $date) >= 1){
    #echo "Error: Record Exists !";
    echo 1;
}
else{
    $pp = $auth_user->add_obs($station, $date, $user_id, $remark, $data_arr);
    if ($pp == 1){
        #echo "Success: Record Submitted !";
        echo 2;
    }
    else{
        #echo "Error: Unexpected !";
        echo 3;
    }
}




?>
