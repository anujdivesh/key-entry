<?php
header('Content-Type: application/json');

require_once("../app_code/class.user.php");

$auth_user = new USER();
require_once "../app_code/config.php";

//$sqlQuery = "SELECT student_id,student_name,marks FROM tbl_marks ORDER BY student_id";

$sql = "select (select count(id) from leave_request where MONTH(logged_date) = '01' and deleted_flag = 0) as 'Jan',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '02' and deleted_flag = 0) as 'Feb',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '03' and deleted_flag = 0) as 'Mar',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '04' and deleted_flag = 0) as 'Apr',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '05' and deleted_flag = 0) as 'May',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '06' and deleted_flag = 0) as 'Jun',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '07' and deleted_flag = 0) as 'Jul',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '08' and deleted_flag = 0) as 'Aug',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '09' and deleted_flag = 0) as 'Sep',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '10' and deleted_flag = 0) as 'Oct',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '11' and deleted_flag = 0) as 'Nov',";
$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '12' and deleted_flag = 0) as 'Dec'";
$sql .= "from leave_request where Year(logged_date) = Year(NOW()) LIMIT 1";

$result = mysqli_query($auth_user->runQuery($sql));

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}


echo json_encode($data);
?>