<?php

require_once("app_code/session.php");
require_once("app_code/class.user.php");
require_once("app_code/class.division.php");
$auth_user = new USER();

$station=$_POST['data'];
$date=$_POST['date'];

$data = $auth_user->search_obsdata($station, $date);
$table = "";

foreach($data as $row){
    $table .= "<tr><td>".$row['id'] ."</td><td>". $row['station_no'] ."</td><td>". $row['rainfall'] ."</td><td>". $row['dry_bulb_temperature'] ."</td><td>". $row['wet_bulb_temperature'] ."</td>";
    $table .= "<td>".$row['rh']."</td>";
    $table .= "<td>".$row['max_temperature']."</td>";
    $table .= "<td>".$row['min_temperature']."</td>";
    $table .= "<td>".$row['sunshine_hours']."</td>";
    $table .= "<td>".$row['radiation']."</td>";
    $table .= "<td>".$row['date_entry']."</td>";
    $table .="<td><a href='read_data.php?data_id=".urlencode(base64_encode($row['id']))."' class= 'btn btn-warning btn-xs m-r-5' data-toggle='tooltip' data-original-title='Edit'><i class='fa fa-pencil font-14'></i></a></td></tr>";
}    
echo $table;

?>