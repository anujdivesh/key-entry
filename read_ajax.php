
<?php

require_once("app_code/session.php");
require_once("app_code/class.user.php");
require_once("app_code/class.division.php");
$auth_user = new USER();

//Fetching Values from URL
//$name2=$_POST['station'];
$data=$_POST['data'];
$station=$_POST['station'];
$date=$_POST['date'];
$remark=$_POST['remark'];
$user_id=$_POST['user_id'];

$data_arr = explode ("&", $data);  

$dt_now = date("Y-m-d");
$datetime1 = new DateTime($dt_now);
$datetime2 = new DateTime($date);
$interval = $datetime1->diff($datetime2);
$date_diff = $interval->format('%a');

if ($date_diff == 0){
    $pp = $auth_user->update_obs($station, $date, $user_id, $remark, $data_arr);
    if ($pp == 1){
        //echo "Success: Record Updated !";
        echo 1;
    }
    else{
        //echo "Error: Unexpected !";
        echo 2;
    }
}
else{
    //echo "Error: Cannot update Record !";
    echo 3;
}


?>
