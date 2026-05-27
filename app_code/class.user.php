<?php

require_once('dbconfig.php');
require_once("session.php");
class USER
{	

	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->openConnection();
		$this->conn = $db;
    }
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function register($fname,$lname, $user, $pass, $email, $orgid, $station, $scount, $rid)
	{
		try
		{
			$div_name = $this->getOrganizationName($orgid);
			$role_name = $this->getRoleName($rid);
			$act = 'Y';
			$cn = '0';


			
			$stmt = $this->conn->prepare("INSERT INTO user_control(first_name, last_name, USERNAME,password,email,organization_id, organization_value,station_access,count_station, counter,role_id,role_value,is_active) 
		                                               VALUES(:fname, :lname, :user, crypt(:pass, gen_salt('bf')), :email, :orgid, :orgname, :station, :scount, :countt, :rid, :rname, :active)");
												  
			$stmt->bindparam(":fname", $fname);
			$stmt->bindparam(":lname", $lname);		
			$stmt->bindparam(":user", $user);	
			$stmt->bindparam(":pass", $pass);	
			$stmt->bindparam(":email", $email);	
			$stmt->bindparam(":orgid", $orgid);	
			$stmt->bindparam(":orgname", $div_name);	
			$stmt->bindparam(":station", $station);		
			$stmt->bindparam(":scount", $scount);		
			$stmt->bindparam(":countt", $cn);			
			$stmt->bindparam(":rid", $rid);	
			$stmt->bindparam(":rname", $role_name);		
			$stmt->bindparam(":active", $act);							  
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function update_counter($username)
	{
		$sid = "1";
		try
		{
			$stmt = $this->conn->prepare("update user_control set counter = counter + 1 where lower(email) = :un");
												 
			$stmt->bindparam(":un", $username);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function update_counter_zero($username)
	{
		$sid = "0";
		try
		{
			$stmt = $this->conn->prepare("update user_control set COUNTER = :c where lower(email) = :un");
						
			$stmt->bindparam(":c", $sid);
			$stmt->bindparam(":un", $username);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function email_exist($email){
		$del = 0;
		$stmt1 = $this->runQuery("SELECT email from user_control where lower(email) = :un");
		$stmt1->bindparam(":un", $email);
        $stmt1->execute();
        $count = $stmt1->rowCount();
        return $count;
	}

	public function get_usernameemail($email){
		$stmt1 = $this->runQuery("SELECT username from user_control where lower(email) = :un");
		$stmt1->bindparam(":un", $email);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['username'];
	}

	public function get_ifrequest($user_id){
		$del = 0;
		$stmt1 = $this->runQuery("SELECT reset_req from user_control where lower(email) like :un");
		$stmt1->bindparam(":un", $user_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['reset_req'];
	}

	
	public function select_counter($username){
		$del = 0;
		$stmt1 = $this->runQuery("SELECT counter from user_control where lower(email) like :un");
		$stmt1->bindparam(":un", $username);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['counter'];
	}

	public function update_password($email, $pass){
		try{

			$stmtt = $this->conn->prepare("UPDATE user_control set password = crypt(:pass, gen_salt('bf')), counter = 0, reset_req = 1 where lower(email) = :email");
			$stmtt->bindparam(":pass", $pass);								
			$stmtt->bindparam(":email", $email);						  
				
			$stmtt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}	

	}
	public function update_passwordid($email, $pass){
		try{

			$stmtt = $this->conn->prepare("UPDATE user_control set password = crypt(:pass, gen_salt('bf')), counter = 0, reset_req = 0 where id = :email");
			$stmtt->bindparam(":pass", $pass);								
			$stmtt->bindparam(":email", $email);						  
				
			$stmtt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}	

	}
	
	public function doLogin($uname,$upass)
	{
		try
		{
			$uname = strtolower($uname);
			$stat = 'Y';
			$stmt = $this->conn->prepare("select (password = crypt(:pass, password)) AS pwd_match, username, role_id, id from USER_CONTROL where lower(email) Like :uname and IS_ACTIVE = :umail;");
			$stmt->execute(array(':pass'=>$upass,':uname'=>$uname, ':umail'=>$stat));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			if($stmt->rowCount() == 1)
			{
				if($userRow['pwd_match'] == 'True')
				{
					$_SESSION['user_session_id'] = $userRow['id'];
					$_SESSION['user_role_id'] = $userRow['role_id'];
					return true;
				}
				else
				{
					return false;
				}
			}
			else{
				return false;
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function is_loggedin()
	{
		if(isset($_SESSION['user_session_id']))
		{
			return true;
		}
	}
	
	public function redirect($url)
	{
		header("Location: $url");
	}
	
	public function doLogout()
	{
		session_destroy();
		unset($_SESSION['user_session_id']);
		return true;
	}

	public function search_users($name, $ddldivision){
		if(empty(trim($name))){
			$name = "%";
		}
		if(empty(trim($ddldivision))){
			$ddldivision = "%";
		}
	
		$sql = "select id, username, organization_value, role_value as role_name, station_access as date from user_control ";
		$sql .= "WHERE username Like :uname and organization_id Like :div";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $name);
		$stmt1->bindparam(":div", $ddldivision);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function search_obsdata($station, $date){
		if ($date == ''){ 
			$dat = '%%';
		}
		else {
			$dat = $this->date_format($date);
		}
		// Select all fields so the view can render every sensor column + any other metadata fields.
		$sql = "select obs_data.*, concat(obs_data.station_no, '-', stations.station_name) as station_label, to_char(obs_data.date_entry, 'DD-MM-YYYY HH:mi') as date_entry_fmt from obs_data ";
		$sql .= "left join stations on obs_data.station_no = stations.station_no ";
		$sql .= "WHERE obs_data.station_no Like :uname and to_char(obs_data.date_entry, 'YYYY-MM-DD HH:mi:SS') LIKE :date order by obs_data.date_entry desc";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $station);
		$stmt1->bindparam(":date", $dat);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function check_record($station, $date){
		$dat = $this->date_format($date);

		$sql = "SELECT id FROM obs_data WHERE station_no = :uname and date_entry = :datee";
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $station);
		$stmt1->bindparam(":datee", $dat);
		$stmt1->execute();
		$count = $stmt1->rowCount();
		return $count;
	}
	

	public function update_obs($station, $date, $user_id, $remark, $data_arr){
		try{
			$station_name = $this->getStationName($station);
			$dat = $date. " 09:00:00";
			$username = $this->getUsername($user_id);
			$clide_table = $this->getClideTable($station);
			$ed = 'Y';
			$edate = date('Y-m-d H:i:s');
			$pr = 'N';
			$dry = null;
			$wet = null;
			$dew_sensor_id = $this->getDewPointSensorId();
			foreach($data_arr as $item) {
				$item_arr = explode ("=", $item, 2);
				if (count($item_arr) !== 2) {
					continue;
				}
				$sensor_id = trim($item_arr[0]);
				$element_value = $item_arr[1];

				// Track Dry/Wet for RH calculation
				if ($sensor_id == '2'){
					$dry = $element_value;
				}
				if ($sensor_id == '3'){
					$wet = $element_value;
				}

				// RH + Dew Point are calculated; ignore any incoming values
				if ($sensor_id == '8' || ($dew_sensor_id !== null && $sensor_id === (string)$dew_sensor_id)) {
					continue;
				}

				$element_id = $this->getReference($sensor_id);
				$stmtt = $this->conn->prepare("UPDATE obs_data set ".$element_id."= :did, logged_id = :lid, logged_value = :lval, is_edited= :edit, edited_time = :etime, remarks = :remark, is_processed = :process, variables_flag = :variables_flag where station_no = :id and date_entry = :date");
				$stmtt->bindparam(":did", $element_value);
				$stmtt->bindparam(":lid", $user_id);
				$stmtt->bindparam(":lval", $username);
				$stmtt->bindparam(":edit", $ed);
				$stmtt->bindparam(":etime", $edate);
				$stmtt->bindparam(":remark", $remark);
				$stmtt->bindparam(":process", $pr);
				$variables_flag = "N";
				$stmtt->bindparam(":variables_flag", $variables_flag);
				$stmtt->bindparam(":id", $station);
				$stmtt->bindparam(":date", $dat);
				$stmtt->execute();
			}

			// Always update RH based on Dry/Wet if we can
			if ($dry !== null && $wet !== null && $dry !== '' && $wet !== '' && is_numeric($dry) && is_numeric($wet) && (float)$dry !== 999.0 && (float)$wet !== 999.0) {
				$rh_value = $this->rh_calculator((float)$dry, (float)$wet);
				$rh_col = $this->getReference('8');
				$stmtt = $this->conn->prepare("UPDATE obs_data set ".$rh_col."= :did, logged_id = :lid, logged_value = :lval, is_edited= :edit, edited_time = :etime, remarks = :remark, is_processed = :process, variables_flag = :variables_flag where station_no = :id and date_entry = :date");
				$stmtt->bindparam(":did", $rh_value);
				$stmtt->bindparam(":lid", $user_id);
				$stmtt->bindparam(":lval", $username);
				$stmtt->bindparam(":edit", $ed);
				$stmtt->bindparam(":etime", $edate);
				$stmtt->bindparam(":remark", $remark);
				$stmtt->bindparam(":process", $pr);
				$variables_flag = "N";
				$stmtt->bindparam(":variables_flag", $variables_flag);
				$stmtt->bindparam(":id", $station);
				$stmtt->bindparam(":date", $dat);
				$stmtt->execute();
			}

			// Always update Dew Point based on Dry/Wet if we can (when sensor exists)
			if ($dew_sensor_id !== null && $dry !== null && $wet !== null && $dry !== '' && $wet !== '' && is_numeric($dry) && is_numeric($wet) && (float)$dry !== 999.0 && (float)$wet !== 999.0) {
				$dew_value = $this->dew_point_calculator((float)$dry, (float)$wet);
				if ($dew_value !== null) {
					$dew_col = $this->getReference($dew_sensor_id);
					if (!empty($dew_col)) {
						$stmtt = $this->conn->prepare("UPDATE obs_data set ".$dew_col."= :did, logged_id = :lid, logged_value = :lval, is_edited= :edit, edited_time = :etime, remarks = :remark, is_processed = :process, variables_flag = :variables_flag where station_no = :id and date_entry = :date");
						$stmtt->bindparam(":did", $dew_value);
						$stmtt->bindparam(":lid", $user_id);
						$stmtt->bindparam(":lval", $username);
						$stmtt->bindparam(":edit", $ed);
						$stmtt->bindparam(":etime", $edate);
						$stmtt->bindparam(":remark", $remark);
						$stmtt->bindparam(":process", $pr);
						$variables_flag = "N";
						$stmtt->bindparam(":variables_flag", $variables_flag);
						$stmtt->bindparam(":id", $station);
						$stmtt->bindparam(":date", $dat);
						$stmtt->execute();
					}
				}
			}
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}	

	}

	public function add_obs($station, $date, $user_id, $remark, $data_arr){
		try{
			$station_name = $this->getStationName($station);
			$dat = $this->date_format($date);
			$username = $this->getUsername($user_id);
			$clide_table = $this->getClideTable($station);
			$ed = 'N';
			
			$stmt = $this->conn->prepare("insert into obs_data (station_no, station_value, date_entry, clide_table, logged_id, logged_value, is_edited, is_processed,variables_flag, remarks) VALUES (:a, :b, :c, :d, :e, :f, :g, :h, :j, :i)");
												  
			$stmt->bindparam(":a", $station);
			$stmt->bindparam(":b", $station_name);	
			$stmt->bindparam(":c", $dat);	
			$stmt->bindparam(":d", $clide_table);	
			$stmt->bindparam(":e", $user_id);	
			$stmt->bindparam(":f", $username);	
			$stmt->bindparam(":g", $ed);	
			$stmt->bindparam(":h", $ed);	
			$variables_flag = "N";
			$stmt->bindparam(":j", $variables_flag);   
			$stmt->bindparam(":i", $remark);	
				
			$stmt->execute();	
			


			
			$dry = null;
			$wet = null;
			$dew_sensor_id = $this->getDewPointSensorId();
			foreach($data_arr as $item) {
				$item_arr = explode ("=", $item, 2);
				if (count($item_arr) !== 2) {
					continue;
				}
				$sensor_id = trim($item_arr[0]);
				$element_value = $item_arr[1];

				if ($sensor_id == '2') {
					$dry = $element_value;
				}
				if ($sensor_id == '3') {
					$wet = $element_value;
				}

				// RH + Dew Point are calculated; ignore any incoming values
				if ($sensor_id == '8' || ($dew_sensor_id !== null && $sensor_id === (string)$dew_sensor_id)) {
					continue;
				}

				$element_id = $this->getReference($sensor_id);
				$stmtt = $this->conn->prepare("UPDATE obs_data set ".$element_id."= :did where station_no = :id and date_entry = :date and logged_id = :uid");
				$stmtt->bindparam(":did", $element_value);
				$stmtt->bindparam(":id", $station);
				$stmtt->bindparam(":date", $dat);
				$stmtt->bindparam(":uid", $user_id);
				$stmtt->execute();
			}

			// Always update RH based on Dry/Wet if we can
			if ($dry !== null && $wet !== null && $dry !== '' && $wet !== '' && is_numeric($dry) && is_numeric($wet) && (float)$dry !== 999.0 && (float)$wet !== 999.0) {
				$rh_value = $this->rh_calculator((float)$dry, (float)$wet);
				$rh_col = $this->getReference('8');
				$stmtt = $this->conn->prepare("UPDATE obs_data set ".$rh_col."= :did where station_no = :id and date_entry = :date and logged_id = :uid");
				$stmtt->bindparam(":did", $rh_value);
				$stmtt->bindparam(":id", $station);
				$stmtt->bindparam(":date", $dat);
				$stmtt->bindparam(":uid", $user_id);
				$stmtt->execute();
			}

			// Always update Dew Point based on Dry/Wet if we can (when sensor exists)
			if ($dew_sensor_id !== null && $dry !== null && $wet !== null && $dry !== '' && $wet !== '' && is_numeric($dry) && is_numeric($wet) && (float)$dry !== 999.0 && (float)$wet !== 999.0) {
				$dew_value = $this->dew_point_calculator((float)$dry, (float)$wet);
				if ($dew_value !== null) {
					$dew_col = $this->getReference($dew_sensor_id);
					if (!empty($dew_col)) {
						$stmtt = $this->conn->prepare("UPDATE obs_data set ".$dew_col."= :did where station_no = :id and date_entry = :date and logged_id = :uid");
						$stmtt->bindparam(":did", $dew_value);
						$stmtt->bindparam(":id", $station);
						$stmtt->bindparam(":date", $dat);
						$stmtt->bindparam(":uid", $user_id);
						$stmtt->execute();
					}
				}
			}
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}	

	}

	public function date_format($date){
		$day = substr($date,0,2);
		$month = substr($date,3,2);
		$year = substr($date,6,4);

		$dt = $year."-".$month."-".$day. " 09:00:00";
		return $dt;
	}

	public function search_public_holiday($name, $ddldivision){
		if(empty(trim($name))){
			$name = "%";
		}
		if(empty(trim($ddldivision))){
			$ddldivision = "%";
		}
	
		$sql = "select id, name, DATE_FORMAT(holiday_date, '%d %M  %Y') as 'holiday', year, DATE_FORMAT(created_at, '%d %M  %Y') as 'date', status_value from public_holiday ";
		$sql .= "WHERE id Like :uname and year Like :div";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $name);
		$stmt1->bindparam(":div", $ddldivision);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function is_username_valid($name){
		$sql = "SELECT ID FROM user_control WHERE username = :uname";
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $name);
		$stmt1->execute();
		$count = $stmt1->rowCount();
		return $count;
	}

	public function id_exists($name){
		$sql = "SELECT USER_ID FROM bio_data WHERE USER_ID = :uname";
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $name);
		$stmt1->execute();
		$count = $stmt1->rowCount();
		return $count;
	}

	public function getClideTable($id){
		$stmt1 = $this->runQuery("SELECT clide_table FROM stations where station_no = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['clide_table'];
	}
	public function getReference($id){
		$stmt1 = $this->runQuery("SELECT reference_col FROM sensors where id = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['reference_col'];
	}

	public function getemaill($id){
		$stmt1 = $this->runQuery("SELECT lower(email) as email FROM user_control where id = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$email = $userRow1['email'];
		if (strtolower($userRow1['email']) == 'Divesh.Anuj@met.gov.fj'){
			$email = "";
		}
        return $email;
	}

	public function getfullname($id){
		$stmt1 = $this->runQuery("SELECT concat_ws(' ', first_name, last_name) AS ab from user_Control where id = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['ab'];
	}


	public function getUsername($id){
		$stmt1 = $this->runQuery("SELECT username FROM user_control where id = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['username'];
	}

	public function rh_calculator($dry, $wet){
		$a=240.97 + $dry;
		$b = 17.502 * $dry; 
		$c = $b/$a;
		$d = pow(2.71828,$c);
		$e = 6.112 * $d;
		
		
		$a=240.97 + $wet;
		$b = 17.502 * $wet; 
		$c = $b/$a;
		$d = pow(2.71828,$c);
		$f = 6.112 * $d;
	
		$g = ($f - .66875 * (1 + .00115 * $wet) * ($dry - $wet))/ $e;
		return round($g*100,0);
	}

	public function getDewPointSensorId(){
		static $cached_id = null;
		static $loaded = false;
		if ($loaded) {
			return $cached_id;
		}
		$loaded = true;
		try {
			$stmt = $this->runQuery("SELECT id FROM sensors WHERE lower(sensor_name) LIKE '%dew%' OR lower(reference_col) LIKE '%dew%' ORDER BY id LIMIT 1");
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row && isset($row['id']) && $row['id'] !== null && $row['id'] !== '') {
				$cached_id = (string)$row['id'];
			}
		} catch (Exception $e) {
			$cached_id = null;
		}
		return $cached_id;
	}

	public function dew_point_calculator($dry, $wet){
		// Compute actual vapour pressure (hPa) from dry & wet bulb temps, then invert Magnus formula.
		$a = 240.97 + $wet;
		$b = 17.502 * $wet;
		$c = $b / $a;
		$d = pow(2.71828, $c);
		$es_wet = 6.112 * $d;
		$ea = $es_wet - .66875 * (1 + .00115 * $wet) * ($dry - $wet);
		if ($ea <= 0) {
			return null;
		}
		$ln = log($ea / 6.112);
		$td = (240.97 * $ln) / (17.502 - $ln);
		return round($td, 1);
	}

	public function getSensorName($id){
		$stmt1 = $this->runQuery("SELECT sensor_name FROM sensors where id = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['sensor_name'];
	}

	public function getStationName($id){
		$stmt1 = $this->runQuery("SELECT station_name FROM stations where station_no = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['station_name'];
	}

	public function getStationStr($id){
		$stmt1 = $this->runQuery("SELECT station_access FROM user_control where id = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['station_access'];
	}

	public function getStationAcess($id){
		$stmt1 = $this->runQuery("SELECT sensors FROM stations where station_no = :id");
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['sensors'];
	}



	public function getOrganizationName($division){
		$stmt1 = $this->runQuery("SELECT org_name FROM organizations where id = :id");
		$stmt1->bindparam(":id", $division);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['org_name'];
	}
	
	public function getRoleName($division){
		$stmt1 = $this->runQuery("SELECT role_name FROM role where role_id = :id");
		$stmt1->bindparam(":id", $division);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['role_name'];
	}

	public function getRoleID($division){
		$stmt1 = $this->runQuery("SELECT role_id FROM user_control where id = :id");
		$stmt1->bindparam(":id", $division);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['role_id'];
	}
	
	public function isnull_all($name){
		if(empty(trim($name)))
		{
			$name = "%";
		}
		else{
			$name = $name;
		}
		return $name;
	}

	public function update_user($userid, $division, $role, $status, $email, $fname, $lname, $access, $scount, $cnt){
		try
		{
			$div_name = $this->getOrganizationName($division);
			$role_name = $this->getRoleName($role);
						
			$stmt = $this->conn->prepare("UPDATE user_control set organization_id = :did, organization_value = :dname, role_id = :rid, role_value = :rname, is_active = :stat, email = :email, first_name = :fname, last_name = :lname, station_access = :access, count_station = :scount, counter = :cnt where id = :id");
												  
			$stmt->bindparam(":did", $division);
			$stmt->bindparam(":dname", $div_name);		
			$stmt->bindparam(":rid", $role);	
			$stmt->bindparam(":rname", $role_name);	
			$stmt->bindparam(":stat", $status);	
			$stmt->bindparam(":email", $email);	
			$stmt->bindparam(":fname", $fname);	
			$stmt->bindparam(":lname", $lname);	
			$stmt->bindparam(":access", $access);	
			$stmt->bindparam(":scount", $scount);	
			$stmt->bindparam(":cnt", $cnt);	
			$stmt->bindparam(":id", $userid);							  
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}			
	}

	public function add_bio_data($id, $fname, $mname, $lname, $contact, $address, $by)
	{
		try
		{
			$date = date('Y-m-d H:i:s');
			$stmt = $this->conn->prepare("INSERT INTO bio_data(user_id, first_name, middle_name, last_name, contact, address, modified_by, modified_date) 
		                                               VALUES(:uname, :upass, :did, :dname, :rid, :rname, :by, :date)");
												  
			$stmt->bindparam(":uname", $id);
			$stmt->bindparam(":upass", $fname);		
			$stmt->bindparam(":did", $mname);	
			$stmt->bindparam(":dname", $lname);	
			$stmt->bindparam(":rid", $contact);	
			$stmt->bindparam(":rname", $address);		
			$stmt->bindparam(":by", $by);	
			$stmt->bindparam(":date", $date);						  
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function update_bio_data($id, $fname, $mname, $lname, $contact, $address, $by){
		try
		{
			$date = date('Y-m-d H:i:s');
			$stmt = $this->conn->prepare("UPDATE bio_data set first_name = :did, middle_name = :dname, last_name = :rid, contact = :rname, address = :stat, modified_by = :by, modified_date = :date where user_id = :id");
												  
			$stmt->bindparam(":did", $fname);
			$stmt->bindparam(":dname", $mname);		
			$stmt->bindparam(":rid", $lname);	
			$stmt->bindparam(":rname", $contact);	
			$stmt->bindparam(":stat", $address);	
			$stmt->bindparam(":id", $id);	
			$stmt->bindparam(":by", $by);	
			$stmt->bindparam(":date", $date);							  
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}			
	}

	public function update_public_holiday($id, $status, $user){
		try
		{
			$sid = "";
			$sval = "";
			if($status == "Y"){
				$sid = "Y";
				$sval = "Active";
			}
			else{
				$sid = "N";
				$sval = "In-Active";
			}
			$date = date('Y-m-d H:i:s');
			$stmt = $this->conn->prepare("UPDATE public_holiday set status_id = :a, status_value = :b, modified_by = :c, modified_date = :d where id = :e");
												  
			$stmt->bindparam(":a", $sid);
			$stmt->bindparam(":b", $sval);		
			$stmt->bindparam(":c", $user);	
			$stmt->bindparam(":d", $date);	
			$stmt->bindparam(":e", $id);						  
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}			
	}

	public function add_contract($id, $edp, $position, $years, $start, $end, $annual, $sick, $breave, $is_act, $added_by, $pay, $availsick, $availbreavement, $availann, $initsickw, $availsickw)
	{
		try
		{
			$stat_val = "";
			if ($is_act == "Y"){
				$stat_val = "Valid";
			}
			else{
				$stat_val = "Invalid";
			}
			$strt_year = substr($start, 6, 4);
			$strt_day = substr($start, 3, 2);
			$strt_month = substr($start, 0, 2);
			$start_year_final = $strt_year. "-". $strt_day. "-".$strt_month;

			$cutoff_year = $strt_year + 1;
			$cutoff_date = $cutoff_year. "-". $strt_day. "-".$strt_month;

			$date1 = date('Y-m-d');
			if($cutoff_date < $date1){
				$cutoff_date=date('Y-m-d', strtotime('+1 year', strtotime($cutoff_date)) );
			}
			if($cutoff_date < $date1){
				$cutoff_date=date('Y-m-d', strtotime('+1 year', strtotime($cutoff_date)) );
			}
			if($cutoff_date < $date1){
				$cutoff_date=date('Y-m-d', strtotime('+1 year', strtotime($cutoff_date)) );
			}


			$end_year = substr($end, 6, 4);
			$end_day = substr($end, 3, 2);
			$end_month = substr($end, 0, 2);
			$end_year_final = $end_year. "-". $end_day. "-".$strt_month;

			$date = date('Y-m-d H:i:s');
			$stmt = $this->conn->prepare("INSERT INTO CONTRACTS(user_id, edp_num, position, num_of_years, contract_start_date, contract_end_date, annual_leave_entitled, sick_leave_entitled, breavement_leave_entitled, is_active, is_active_value, added_by, added_date, num_pays, SICK_LEAVE_AVAIL, BREAVEMENT_LEAVE_AVAIL, annual_leave_avail, SICK_LEAVE_OUT_ENTITLED, SICK_LEAVE_OUT_AVAIL, CONTRACT_CUTOFF_DATE) 
		                                               VALUES(:user, :edp, :position, :years, :start, :end, :annual, :sick, :breave, :is_act, :is_act_val, :addedby, :addeddate, :pay, :asick, :ab, :ann, :sickw, :sickww, :cutoff)");
												  
			$stmt->bindparam(":user", $id);
			$stmt->bindparam(":edp", $edp);		
			$stmt->bindparam(":position", $position);	
			$stmt->bindparam(":years", $years);	
			$stmt->bindparam(":start", $start_year_final);	
			$stmt->bindparam(":end", $end_year_final);		
			$stmt->bindparam(":annual", $annual);	
			$stmt->bindparam(":sick", $sick);		
			$stmt->bindparam(":breave", $breave);
			$stmt->bindparam(":is_act", $is_act);
			$stmt->bindparam(":is_act_val", $stat_val);			
			$stmt->bindparam(":addedby", $added_by);		
			$stmt->bindparam(":addeddate", $date);			  
			$stmt->bindparam(":pay", $pay);	
			$stmt->bindparam(":asick", $availsick);			  
			$stmt->bindparam(":ab", $availbreavement);	
			$stmt->bindparam(":ann", $availann);	
			$stmt->bindparam(":sickw", $initsickw);	
			$stmt->bindparam(":sickww", $availsickw);	
			$stmt->bindparam(":cutoff", $cutoff_date);	
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function search_contracts($userid){
	
		$sql = "select c_id, position, DATE_FORMAT(CONTRACT_START_DATE, '%d %M  %Y') as 'start', DATE_FORMAT(CONTRACT_END_DATE, '%d %M  %Y') as 'end', IS_ACTIVE from CONTRACTS ";
		$sql .= "WHERE user_id Like :uname";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $userid);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}
	public function search_contracts_due(){
	
		$sql = "select c_id, Concat(bio_data.first_name, ' ', bio_data.last_name) as 'full_name', position, DATE_FORMAT(CONTRACT_START_DATE, '%d %M  %Y') as 'start', DATE_FORMAT(CONTRACT_END_DATE, '%d %M  %Y') as 'end', IS_ACTIVE, contract_cutoff_date, CONTRACT_START_DATE from CONTRACTS ";
		$sql .= "left join bio_data on bio_data.user_id = contracts.user_id where is_active = 'Y' and Year(contract_cutoff_date) = Year(NOW()) and Year(CONTRACT_END_DATE) != Year(NOW())";
		  
		$stmt1 = $this->runQuery($sql);
		//$stmt1->bindparam(":uname", $userid);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}
	public function search_contracts_due1(){
	
		$sql = "select c_id, Concat(bio_data.first_name, ' ', bio_data.last_name) as 'full_name', position, DATE_FORMAT(CONTRACT_START_DATE, '%d %M  %Y') as 'start', DATE_FORMAT(CONTRACT_END_DATE, '%d %M  %Y') as 'end', IS_ACTIVE, contract_cutoff_date, CONTRACT_START_DATE, CONTRACT_END_DATE from CONTRACTS ";
		$sql .= "left join bio_data on bio_data.user_id = contracts.user_id where is_active = 'Y' and (contract_end_date < curdate() or Year(CONTRACT_END_DATE) = Year(NOW()))";
		  
		$stmt1 = $this->runQuery($sql);
		//$stmt1->bindparam(":uname", $userid);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}
	public function search_approved_leave_requests(){
	
		$sql = "select id, Concat(bio_data.first_name, ' ', bio_data.last_name) as 'full_name', leave_type_value, DATE_FORMAT(start_date, '%d %M  %Y') as 'start', DATE_FORMAT(end_date, '%d %M  %Y') as 'end',DATE_FORMAT(resume_date, '%d %M  %Y') as 'resume',num_of_days,status_value ";
		$sql .= "from leave_request left join bio_data on bio_data.user_id = leave_request.loggedby_id where status_id = '4' order by id desc";
		  
		$stmt1 = $this->runQuery($sql);
		//$stmt1->bindparam(":uname", $userid);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function getsensors(){
		// Force numeric ordering even if sensors.id is stored as text.
		$stmt1 = $this->runQuery("SELECT sensor_name, id FROM sensors ORDER BY id::int ASC");
        $stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;
    }

	public function search_reject_leave_requests(){
	
		$sql = "select id, Concat(bio_data.first_name, ' ', bio_data.last_name) as 'full_name', leave_type_value, DATE_FORMAT(start_date, '%d %M  %Y') as 'start', DATE_FORMAT(end_date, '%d %M  %Y') as 'end',DATE_FORMAT(resume_date, '%d %M  %Y') as 'resume',num_of_days,status_value, rejected_by_user ";
		$sql .= "from leave_request left join bio_data on bio_data.user_id = leave_request.loggedby_id where status_id = '5' order by id desc";
		  
		$stmt1 = $this->runQuery($sql);
		//$stmt1->bindparam(":uname", $userid);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}
	public function getuserid_contract($c_id){
		$stmt1 = $this->runQuery("SELECT user_id FROM contracts where C_ID = :id");
		$stmt1->bindparam(":id", $c_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['user_id'];
	}

	public function change_contract_status($c_id, $is_act, $moduser, $annual_avail, $sick_avail, $sickw_avail, $breave_avail)
	{
		try
		{
			$stat_val = "";
			if ($is_act == "Y"){
				$stat_val = "Valid";
			}
			else{
				$stat_val = "Invalid";
			}

			$date = date('Y-m-d H:i:s');
			$stmt = $this->conn->prepare("update CONTRACTS set is_active = :a, is_active_value = :b, modified_by = :c, modified_date = :d, ANNUAL_LEAVE_AVAIL = :e, SICK_LEAVE_AVAIL = :f, BREAVEMENT_LEAVE_AVAIL = :g, SICK_LEAVE_OUT_AVAIL = :h where c_id = :id");
												  
			$stmt->bindparam(":a", $is_act);
			$stmt->bindparam(":b", $stat_val);		
			$stmt->bindparam(":c", $moduser);	
			$stmt->bindparam(":d", $date);	
			$stmt->bindparam(":e", $annual_avail);	
			$stmt->bindparam(":f", $sick_avail);	
			$stmt->bindparam(":g", $breave_avail);	
			$stmt->bindparam(":h", $sickw_avail);	
			$stmt->bindparam(":id", $c_id);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function update_cutoff_date($c_id, $cutoff_date, $test){
		try
		{
			$date1 = date('Y-m-d');
			$futureDate=date('Y-m-d', strtotime('+1 year', strtotime($cutoff_date)) );
			if($futureDate < $date1){
				$futureDate=date('Y-m-d', strtotime('+1 year', strtotime($cutoff_date)) );
			}
			if($futureDate < $date1){
				$futureDate=date('Y-m-d', strtotime('+1 year', strtotime($cutoff_date)) );
			}
			if($futureDate < $date1){
				$futureDate=date('Y-m-d', strtotime('+1 year', strtotime($cutoff_date)) );
			}

			if ($test == "y"){
				$date = date('Y-m-d');
				if($date >= $cutoff_date)
				{
					$stmt = $this->conn->prepare("update CONTRACTS set CONTRACT_CUTOFF_DATE = :a where c_id = :id");
													
					$stmt->bindparam(":a", $futureDate);
					$stmt->bindparam(":id", $c_id);	
					
					$stmt->execute();	
				}
			}

			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}			

	}

	public function searchvalid_contracts(){
		$flag = 'Y';
		$sql = "select c_id, user_id, num_pays, annual_leave_entitled, annual_leave_avail from CONTRACTS ";
		$sql .= "WHERE is_active = :uname";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $flag);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function add_annual_leave($c_id, $rate)
	{
		try
		{
			$stmt = $this->conn->prepare("update contracts set ANNUAL_LEAVE_AVAIL = case when (ANNUAL_LEAVE_AVAIL is not null) then ANNUAL_LEAVE_AVAIL + :an else :an end where c_id = :id");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function add_pay_run($end, $seq, $cid, $uid, $val, $exby)
	{
		try
		{
			$end_year = substr($end, 6, 4);
			$end_day = substr($end, 3, 2);
			$end_month = substr($end, 0, 2);
			$end_year_final = $end_year. "-". $end_day. "-".$end_month;

			$date = date('Y-m-d H:i:s');

			$stmt = $this->conn->prepare("insert into pay_runs (pay_date, pay_seq, c_id, user_id, added_val, executed_by, executed_date) VALUES (:a, :b, :c, :d, :e, :f, :g)");
												  
			$stmt->bindparam(":a", $end_year_final);
			$stmt->bindparam(":b", $seq);	
			$stmt->bindparam(":c", $cid);	
			$stmt->bindparam(":d", $uid);	
			$stmt->bindparam(":e", $val);	
			$stmt->bindparam(":f", $exby);	
			$stmt->bindparam(":g", $date);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function add_public_holiday($holiday_date, $name, $year, $user)
	{
		try
		{
			$sid = "Y";
			$sval = "Active";
			$end_year = substr($holiday_date, 6, 4);
			$end_day = substr($holiday_date, 3, 2);
			$end_month = substr($holiday_date, 0, 2);
			$end_year_final = $end_year. "-". $end_day. "-".$end_month;

			$stmt = $this->conn->prepare("insert into public_holiday (name, holiday_date, year, created_user, status_id, status_value) VALUES (:a, :b, :c, :d, :e, :f)");
												  
			$stmt->bindparam(":a", $name);
			$stmt->bindparam(":b", $end_year_final);	
			$stmt->bindparam(":c", $year);	
			$stmt->bindparam(":d", $user);	
			$stmt->bindparam(":e", $sid);	
			$stmt->bindparam(":f", $sval);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function search_contract($date, $ddldivision){
		
		if(empty(trim($date))){
			$end_year_final = "%";
		}
		else{
			$end_year = substr($date, 6, 4);
			$end_day = substr($date, 3, 2);
			$end_month = substr($date, 0, 2);
			$end_year_final = $end_year. "-". $end_day. "-".$end_month;
		}

		

		if(empty(trim($ddldivision))){
			$ddldivision = "%";
		}
	
		$sql = "select p_id, pay_date,PAY_SEQ, concat(bio_data.first_name, ' ' ,bio_data.LAST_NAME) as 'Name' , added_val, EXECUTED_BY, DATE_FORMAT(EXECUTED_DATE, '%d %M %Y %H:%i %p') as 'EXECUTED_DATE' from PAY_RUNS left join BIO_DATA on BIO_DATA.USER_ID = PAY_RUNS.USER_ID ";
		$sql .= "WHERE pay_seq Like :uname and pay_date Like :div";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $ddldivision);
		$stmt1->bindparam(":div", $end_year_final);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function add_upload($uid, $name, $size, $type, $location)
	{
		try
		{
			$stmt = $this->conn->prepare("insert into upload (user_id, name, size, type, location) VALUES (:a, :b, :c, :d, :e)");
												  
			$stmt->bindparam(":a", $uid);
			$stmt->bindparam(":b", $name);	
			$stmt->bindparam(":c", $size);	
			$stmt->bindparam(":d", $type);	
			$stmt->bindparam(":e", $location);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function date_formater($date){
		$str = "";

		$end_year = substr($date, 6, 4);
		$end_day = substr($date, 3, 2);
		$end_month = substr($date, 0, 2);
		$str = $end_year. "-". $end_day. "-".$end_month;

		return $str;
	}

	public function get_leave_value($c_id){
		$stmt1 = $this->runQuery("SELECT L_NAME FROM LEAVE_TYPE where L_ID = :id");
		$stmt1->bindparam(":id", $c_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['L_NAME'];
	}

	public function get_latest_file($user_id){
		$stmt1 = $this->runQuery("SELECT id FROM upload where user_id = :id order by id desc");
		$stmt1->bindparam(":id", $user_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['id'];
	}

	public function get_username($user_id){
		$stmt1 = $this->runQuery("SELECT USERNAME FROM user_control where id = :id");
		$stmt1->bindparam(":id", $user_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['USERNAME'];
	}

	public function get_user_id($user_id){
		$stmt1 = $this->runQuery("SELECT loggedby_id FROM leave_request where id = :id");
		$stmt1->bindparam(":id", $user_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['loggedby_id'];
	}
	public function get_file_location($user_id){
		$stmt1 = $this->runQuery("SELECT location FROM upload where id = :id");
		$stmt1->bindparam(":id", $user_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['location'];
	}
	public function get_user_role($user_id){
		$stmt1 = $this->runQuery("SELECT role_id FROM user_control where id = :id");
		$stmt1->bindparam(":id", $user_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['role_id'];
	}
	public function get_leave_status($l_id){
		$stmt1 = $this->runQuery("SELECT status_id FROM leave_request where id = :id");
		$stmt1->bindparam(":id", $l_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['status_id'];
	}
	public function get_leave_id(){
		$stmt1 = $this->runQuery("SELECT id FROM leave_request order by id desc");
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$id = $userRow1['id'];
		if($id == ""){
			$id = "L00001";
		}
		else{
			$id++;
		}
        return $id;
	}

	public function request_leave($leave_id, $start_date, $end_date, $half_day, $resume_date, $num_days, $desc, $file_attach, $File_id, $logged_by, $assign_id, $user_id)
	{
		try
		{
			$get_leave_id = $this->get_leave_id();

			$leavename = $this->get_leave_value($leave_id);
			$username = $this->get_username($assign_id);
			$assign_role = $this->get_user_role($assign_id);
			$statval = "";
			$statid = "";
			$hodrole = "";
			$dirrole = "";
			
			if($assign_role == "3"){
				$hodrole = "";
				$statid = "1";
				$statval = "Assigned to Supervisor";
			}
			if($assign_role == "4"){
				$assign_role = "";
				$assign_id = "";
				$username = "";
				$hodrole = "4";
				$statid = "2";
				$statval = "Assigned to HOD";
			}
			if($assign_role == "5"){
				$assign_role = "";
				$assign_id = "";
				$username = "";
				$hodrole = "";
				$statid = "3";
				$statval = "Assigned to DMET";
				$dirrole = "5";
			}

			$date = date('Y-m-d H:i:s');
			//$stmt = $this->conn->prepare("INSERT INTO LEAVE_REQUEST(leave_type_id, leave_type_value, start_date, end_date, half_day, resume_date, num_of_days, description, file_attached, file_id, logged_by, logged_date, assigned_sup_id, assigned_sup_value, assigned_sup_role_id, loggedby_id, status_id, status_value) 
		                                               //VALUES(:user, :edp, :position, :years, :start, :end, :annual, :sick, :breave, :is_act, :is_act_val, :addedby, :addeddate, :pay, :asick, :lbid, :sid, :sval)");
			
			$stmt = $this->conn->prepare("INSERT INTO LEAVE_REQUEST(id, leave_type_id, leave_type_value, start_date, end_date, half_day, resume_date, num_of_days, description, file_attached, file_id, logged_by, logged_date, assigned_sup_id, assigned_sup_value, assigned_sup_role_id, loggedby_id, status_id, status_value, ASSIGNED_HOD_ROLE_ID, ASSIGNED_DIR_ROLE_ID) 
		                                               VALUES(:idd, :user, :edp, :position, :years, :start, :end, :annual, :sick, :breave, :is_act, :is_act_val, :addedby, :addeddate, :pay, :asick, :lbid, :sid, :sval, :hodrole, :dirrole)");
			
			$stmt->bindparam(":idd", $get_leave_id);
			$stmt->bindparam(":user", $leave_id);
			$stmt->bindparam(":edp", $leavename);		
			$stmt->bindparam(":position", $start_date);	
			$stmt->bindparam(":years", $end_date);	
			$stmt->bindparam(":start", $half_day);	
			$stmt->bindparam(":end", $resume_date);		
			$stmt->bindparam(":annual", $num_days);	
			$stmt->bindparam(":sick", $desc);		
			$stmt->bindparam(":breave", $file_attach);
			$stmt->bindparam(":is_act", $File_id);
			$stmt->bindparam(":is_act_val", $logged_by);			
			$stmt->bindparam(":addedby", $date);		
			$stmt->bindparam(":addeddate", $assign_id);			  
			$stmt->bindparam(":pay", $username);	
			$stmt->bindparam(":asick", $assign_role);	
			$stmt->bindparam(":lbid", $user_id);	
			$stmt->bindparam(":sid", $statid);	
			$stmt->bindparam(":sval", $statval);	
			$stmt->bindparam(":hodrole", $hodrole);	
			$stmt->bindparam(":dirrole", $dirrole);	
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function file_upload($name, $size, $type, $tmp_name, $user_id){

		$location = "../Upload/";
		$maxsize = 100000000000;
		if(isset($name) &!empty($name)){
			if($size <= $maxsize){
				//if($type == "image/jpeg" && $size <= $maxsize){
				if(move_uploaded_file($tmp_name, $location.$name)){
					
					if($this->add_upload($user_id, $name, $size, $type, $location.$name))
					{
						return true;
					}
		
				}else{
					return false;
				}
			}else{
				//echo "File should be jpeg image & only 100 kb in size";
			}
		}
		return true;

	}

	public function add_applied_leave_annual($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set ANNUAL_LEAVE_APPLIED = case when (ANNUAL_LEAVE_APPLIED is not null) then ANNUAL_LEAVE_APPLIED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_applied_leave_annual($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set ANNUAL_LEAVE_APPLIED = case when (ANNUAL_LEAVE_APPLIED is not null) then ANNUAL_LEAVE_APPLIED - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_avail_leave_annual($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set ANNUAL_LEAVE_AVAIL = case when (ANNUAL_LEAVE_AVAIL is not null) then ANNUAL_LEAVE_AVAIL - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_used_leave_annual($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set ANNUAL_LEAVE_USED = case when (ANNUAL_LEAVE_USED is not null) then ANNUAL_LEAVE_USED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_used_leave_sick($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_USED = case when (SICK_LEAVE_USED is not null) then SICK_LEAVE_USED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_applied_leave_sick($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_APPLIED = case when (SICK_LEAVE_APPLIED is not null) then SICK_LEAVE_APPLIED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_applied_leave_sick($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_APPLIED = case when (SICK_LEAVE_APPLIED is not null) then SICK_LEAVE_APPLIED - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_avail_leave_sick($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_AVAIL = case when (SICK_LEAVE_AVAIL is not null) then SICK_LEAVE_AVAIL - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_applied_leave_sick_sheet($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_OUT_APPLIED = case when (SICK_LEAVE_OUT_APPLIED is not null) then SICK_LEAVE_OUT_APPLIED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_used_leave_sickout($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_OUT_USED = case when (SICK_LEAVE_OUT_USED is not null) then SICK_LEAVE_OUT_USED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_applied_leave_sickout($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_OUT_APPLIED = case when (SICK_LEAVE_OUT_APPLIED is not null) then SICK_LEAVE_OUT_APPLIED - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_avail_leave_sickout($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_OUT_AVAIL = case when (SICK_LEAVE_OUT_AVAIL is not null) then SICK_LEAVE_OUT_AVAIL - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_applied_leave_breave($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set BREAVEMENT_LEAVE_APPLIED = case when (BREAVEMENT_LEAVE_APPLIED is not null) then BREAVEMENT_LEAVE_APPLIED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_used_leave_breave($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set BREAVEMENT_LEAVE_USED = case when (BREAVEMENT_LEAVE_USED is not null) then BREAVEMENT_LEAVE_USED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_applied_leave_breave($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set BREAVEMENT_LEAVE_APPLIED = case when (BREAVEMENT_LEAVE_APPLIED is not null) then BREAVEMENT_LEAVE_APPLIED - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function sub_avail_leave_breave($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set BREAVEMENT_LEAVE_AVAIL = case when (BREAVEMENT_LEAVE_AVAIL is not null) then BREAVEMENT_LEAVE_AVAIL - :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_rejected_leave_annual($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set ANNUAL_LEAVE_REJECTED = case when (ANNUAL_LEAVE_REJECTED is not null) then ANNUAL_LEAVE_REJECTED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_rejected_leave_sick($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_REJECTED = case when (SICK_LEAVE_REJECTED is not null) then SICK_LEAVE_REJECTED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_rejected_leave_breave($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set BREAVEMENT_LEAVE_REJECTED = case when (BREAVEMENT_LEAVE_REJECTED is not null) then BREAVEMENT_LEAVE_REJECTED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function add_rejected_leave_sickout($c_id, $rate)
	{
		$stat = "Y";
		try
		{
			$stmt = $this->conn->prepare("update contracts set SICK_LEAVE_OUT_REJECTED = case when (SICK_LEAVE_OUT_REJECTED is not null) then SICK_LEAVE_OUT_REJECTED + :an else :an end where user_id = :id and IS_ACTIVE = :act");
												  
			$stmt->bindparam(":an", $rate);
			$stmt->bindparam(":id", $c_id);	
			$stmt->bindparam(":act", $stat);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}


	public function add_applied($leavetype, $user, $rate){
		try
		{
			if($leavetype == "1"){
				$this->add_applied_leave_annual($user, $rate);
				return true;	
			}
			elseif($leavetype == "2"){
				$this->add_applied_leave_sick($user, $rate);
				return true;	
			}
			elseif($leavetype == "3"){
				$this->add_applied_leave_breave($user, $rate);
				return true;	
			}
			elseif($leavetype == "4"){
				$this->add_applied_leave_sick_sheet($user, $rate);
				return true;	
			}
			else{
				return false;	
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}		
	}

	public function delete_applied($leavetype, $user, $rate){
		try{
			if($leavetype == "1"){
				$this->sub_applied_leave_annual($user, $rate);
				return true;	
			}
			elseif($leavetype == "2"){
				$this->sub_applied_leave_sick($user, $rate);
				return true;	
			}
			elseif($leavetype == "3"){
				$this->sub_applied_leave_breave($user, $rate);
				return true;	
			}
			elseif($leavetype == "4"){
				$this->sub_applied_leave_sickout($user, $rate);
				return true;	
			}
			else{
				return false;	
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}
	}

	public function rejected_leave($leavetype, $user, $rate){
		try
		{
			if($leavetype == "1"){
				$this->sub_applied_leave_annual($user, $rate);
				$this->add_rejected_leave_annual($user, $rate);
				return true;	
			}
			elseif($leavetype == "2"){
				$this->sub_applied_leave_sick($user, $rate);
				$this->add_rejected_leave_sick($user, $rate);
				return true;	
			}
			elseif($leavetype == "3"){
				$this->sub_applied_leave_breave($user, $rate);
				$this->add_rejected_leave_breave($user, $rate);
				return true;	
			}
			elseif($leavetype == "4"){
				$this->sub_applied_leave_sickout($user, $rate);
				$this->add_rejected_leave_sickout($user, $rate);
				return true;	
			}
			else{
				return false;
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}		

	}

	public function dmet_approval($leavetype, $user, $rate){
		try{
			if($leavetype == "1"){
				$this->add_used_leave_annual($user, $rate);
				$this->sub_applied_leave_annual($user, $rate);
				$this->sub_avail_leave_annual($user, $rate);
				return true;
			}
			elseif($leavetype == "2"){
				$this->add_used_leave_sick($user, $rate);
				$this->sub_applied_leave_sick($user, $rate);
				$this->sub_avail_leave_sick($user, $rate);
				return true;	
			}
			elseif($leavetype == "3"){
				$this->add_used_leave_breave($user, $rate);
				$this->sub_applied_leave_breave($user, $rate);
				$this->sub_avail_leave_breave($user, $rate);
				return true;	
			}
			elseif($leavetype == "4"){
				$this->add_used_leave_sickout($user, $rate);
				$this->sub_applied_leave_sickout($user, $rate);
				$this->sub_avail_leave_sickout($user, $rate);
				return true;	
			}
			else{
				return false;	
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}	
		
	}

	public function search_leave($userid){
	
		$sql = "select id, leave_type_value, num_of_days, DATE_FORMAT(START_DATE, '%d/%m/%Y') as 'start',DATE_FORMAT(END_DATE, '%d/%m/%Y') as 'start1', DATE_FORMAT(resume_date, '%d %M  %Y') as 'end', status_value from LEAVE_REQUEST ";
		$sql .= "WHERE loggedby_id = :uname and deleted_flag = '0' order by id desc";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $userid);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function search_leave_sup($userid, $role){
		$stat = "1";
		$d = 0;
		$sql = "select id, leave_type_value, num_of_days, DATE_FORMAT(START_DATE, '%d/%m/%Y') as 'start',DATE_FORMAT(END_DATE, '%d/%m/%Y') as 'start1', DATE_FORMAT(resume_date, '%d %M  %Y') as 'end', status_value from LEAVE_REQUEST ";
		$sql .= "WHERE ASSIGNED_SUP_ID like :uname and ASSIGNED_SUP_ROLE_ID = :role and status_id = :sid and DELETED_FLAG = :d";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":uname", $userid);
		$stmt1->bindparam(":role", $role);
		$stmt1->bindparam(":sid", $stat);
		$stmt1->bindparam(":d", $d);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}
	public function search_leave_hod($role){
		$stat = "2";
		$d = 0;
		$sql = "select id, leave_type_value, num_of_days, DATE_FORMAT(START_DATE, '%d/%m/%Y') as 'start',DATE_FORMAT(END_DATE, '%d/%m/%Y') as 'start1', DATE_FORMAT(resume_date, '%d %M  %Y') as 'end', status_value from LEAVE_REQUEST ";
		$sql .= "WHERE ASSIGNED_HOD_ROLE_ID = :role and status_id = :sid and DELETED_FLAG = :d";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":role", $role);
		$stmt1->bindparam(":sid", $stat);
		$stmt1->bindparam(":d", $d);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function search_leave_dir($role){
		$stat = "3";
		$d = 0;
		$sql = "select id, leave_type_value, num_of_days, DATE_FORMAT(START_DATE, '%d/%m/%Y') as 'start',DATE_FORMAT(END_DATE, '%d/%m/%Y') as 'start1', DATE_FORMAT(resume_date, '%d %M  %Y') as 'end', status_value from LEAVE_REQUEST ";
		$sql .= "WHERE ASSIGNED_DIR_ROLE_ID = :role and status_id = :sid and DELETED_FLAG = :d";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":role", $role);
		$stmt1->bindparam(":sid", $stat);
		$stmt1->bindparam(":d", $d);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function supervisor_approval($comment, $name, $id)
	{
		$role = "4";
		$date = date('Y-m-d H:i:s');
		$sid = "2";
		$sval = "Assigned to HOD";
		try
		{
			$stmt = $this->conn->prepare("update leave_request set SUP_COMMENT = :comm, SUP_LOGGED_DATE = :date, SUP_LOGGED_BY = :name, ASSIGNED_HOD_ROLE_ID = :role, status_id = :sid, status_value = :sval where id = :id");
												  
			$stmt->bindparam(":comm", $comment);
			$stmt->bindparam(":date", $date);	
			$stmt->bindparam(":name", $name);	
			$stmt->bindparam(":role", $role);	
			$stmt->bindparam(":id", $id);	
			$stmt->bindparam(":sid", $sid);	
			$stmt->bindparam(":sval", $sval);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function hod_approval($comment, $name,$userid, $id)
	{
		$role = "5";
		$date = date('Y-m-d H:i:s');
		$sid = "3";
		$sval = "Assigned to DMET";
		try
		{
			$stmt = $this->conn->prepare("update leave_request set HOD_COMMENT = :comm, HOD_LOGGED_DATE = :date, HOD_LOGGED_BY = :name, APPROVED_HOD_ID = :hid, ASSIGNED_DIR_ROLE_ID = :role, status_id = :sid, status_value = :sval where id = :id");
												  
			$stmt->bindparam(":comm", $comment);
			$stmt->bindparam(":date", $date);	
			$stmt->bindparam(":name", $name);	
			$stmt->bindparam(":hid", $userid);	
			$stmt->bindparam(":role", $role);	
			$stmt->bindparam(":id", $id);	
			$stmt->bindparam(":sid", $sid);	
			$stmt->bindparam(":sval", $sval);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function dir_approval($comment, $name, $userid, $id)
	{
		$sid = "4";
		$sval = "Approved by DMET";
		$date = date('Y-m-d H:i:s');

		try
		{
			$stmt = $this->conn->prepare("update leave_request set DIR_COMMENT = :comm, DIR_LOGGED_DATE = :date, DIR_LOGGED_BY = :name, APPROVED_DIR_ID = :did, status_id = :sid, status_value = :sval where id = :id");
												  
			$stmt->bindparam(":comm", $comment);
			$stmt->bindparam(":date", $date);	
			$stmt->bindparam(":name", $name);	
			$stmt->bindparam(":did", $userid);	
			$stmt->bindparam(":id", $id);
			$stmt->bindparam(":sid", $sid);	
			$stmt->bindparam(":sval", $sval);	
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function update_fileid($id, $l_id)
	{
		$sid = "y";
		try
		{
			$stmt = $this->conn->prepare("update leave_request set FILE_ATTACHED = :comm, file_id = :date where id = :id");
												  
			$stmt->bindparam(":comm", $sid);
			$stmt->bindparam(":date", $id);
			$stmt->bindparam(":id", $l_id);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function delete_leave($l_id)
	{
		$sid = "1";
		try
		{
			$stmt = $this->conn->prepare("update leave_request set DELETED_FLAG = :comm where id = :id");
												  
			$stmt->bindparam(":comm", $sid);
			$stmt->bindparam(":id", $l_id);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function update_assigned_user($assign_id, $l_id)
	{
			$username = $this->get_username($assign_id);
			$assign_role = $this->get_user_role($assign_id);
			$statval = "";
			$statid = "";
			$hodrole = "";
			
			if($assign_role == "3"){
				$hodrole = "";
				$statid = "1";
				$statval = "Assigned to Supervisor";
			}
			if($assign_role == "4"){
				$assign_role = "";
				$assign_id = "";
				$username = "";
				$hodrole = "4";
				$statid = "2";
				$statval = "Assigned to HOD";
			}

		try
		{
			$stmt = $this->conn->prepare("update leave_request set ASSIGNED_SUP_ID = :a, ASSIGNED_SUP_VALUE = :b, ASSIGNED_SUP_ROLE_ID = :c, ASSIGNED_HOD_ROLE_ID = :d, status_id = :e, status_value = :f where id = :id");
												  
			$stmt->bindparam(":a", $assign_id);
			$stmt->bindparam(":b", $username);
			$stmt->bindparam(":c", $assign_role);
			$stmt->bindparam(":d", $hodrole);
			$stmt->bindparam(":e", $statid);
			$stmt->bindparam(":f", $statval);
			$stmt->bindparam(":id", $l_id);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function get_public_holidays(){
		$flag = "Y";
		$year =  date("Y");

		$sql = "select holiday_date from public_holiday where status_id = :a and Year = :b";
		  
		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":a", $flag);
		$stmt1->bindparam(":b", $year);
		$stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;

	}

	public function reject_leave($reject_id, $reject_val, $l_id, $comment)
	{
		$sid = "5";
		$sval = "Rejected";
		$date = date('Y-m-d H:i:s');
		try
		{
			$stmt = $this->conn->prepare("update leave_request set rejected_by_id = :a, rejected_by_user = :b, rejected_date = :c, status_id = :d, status_value = :e, SUP_COMMENT = :f, SUP_LOGGED_DATE = :g, SUP_LOGGED_BY = :h where id = :id");
												  
			$stmt->bindparam(":a", $reject_id);
			$stmt->bindparam(":b", $reject_val);
			$stmt->bindparam(":c", $date);
			$stmt->bindparam(":d", $sid);
			$stmt->bindparam(":e", $sval);
			$stmt->bindparam(":f", $comment);
			$stmt->bindparam(":g", $date);
			$stmt->bindparam(":h", $reject_val);
			$stmt->bindparam(":id", $l_id);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}

	public function reject_leave_hod($reject_id, $reject_val, $l_id, $comment)
	{
		$sid = "5";
		$sval = "Rejected";
		$date = date('Y-m-d H:i:s');
		try
		{
			$stmt = $this->conn->prepare("update leave_request set rejected_by_id = :a, rejected_by_user = :b, rejected_date = :c, status_id = :d, status_value = :e, HOD_COMMENT = :f, HOD_LOGGED_DATE = :g, HOD_LOGGED_BY = :h where id = :id");
												  
			$stmt->bindparam(":a", $reject_id);
			$stmt->bindparam(":b", $reject_val);
			$stmt->bindparam(":c", $date);
			$stmt->bindparam(":d", $sid);
			$stmt->bindparam(":e", $sval);
			$stmt->bindparam(":f", $comment);
			$stmt->bindparam(":g", $date);
			$stmt->bindparam(":h", $reject_val);
			$stmt->bindparam(":id", $l_id);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function reject_leave_dir($reject_id, $reject_val, $l_id, $comment)
	{
		$sid = "5";
		$sval = "Rejected";
		$date = date('Y-m-d H:i:s');
		try
		{
			$stmt = $this->conn->prepare("update leave_request set rejected_by_id = :a, rejected_by_user = :b, rejected_date = :c, status_id = :d, status_value = :e, DIR_COMMENT = :f, DIR_LOGGED_DATE = :g, DIR_LOGGED_BY = :h where id = :id");
												  
			$stmt->bindparam(":a", $reject_id);
			$stmt->bindparam(":b", $reject_val);
			$stmt->bindparam(":c", $date);
			$stmt->bindparam(":d", $sid);
			$stmt->bindparam(":e", $sval);
			$stmt->bindparam(":f", $comment);
			$stmt->bindparam(":g", $date);
			$stmt->bindparam(":h", $reject_val);
			$stmt->bindparam(":id", $l_id);
				
			$stmt->execute();	
			
			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}				
	}
	public function chart_data(){
		$sql = "select (select count(id) from leave_request where MONTH(logged_date) = '01' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Jan',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '02' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Feb',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '03' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Mar',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '04' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Apr',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '05' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'May',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '06' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Jun',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '07' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Jul',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '08' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Aug',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '09' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Sep',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '10' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Oct',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '11' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Nov',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '12' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Dec' ";
		$sql .= "from leave_request LIMIT 1";

		$stmt1 = $this->runQuery($sql);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['Jan'].",".$userRow1['Feb'].",".$userRow1['Mar'].",".$userRow1['Apr'].",".$userRow1['May'].",".$userRow1['Jun'].",".$userRow1['Jul'].",".$userRow1['Aug'].",".$userRow1['Sep'].",".$userRow1['Oct'].",".$userRow1['Nov'].",".$userRow1['Dec']; 
        return $s;
	}

	public function chart_data_user($id){
		$sql = "select (select count(id) from leave_request where MONTH(logged_date) = '01' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Jan',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '02' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Feb',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '03' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Mar',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '04' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Apr',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '05' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'May',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '06' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Jun',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '07' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Jul',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '08' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Aug',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '09' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Sep',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '10' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Oct',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '11' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Nov',";
		$sql .= "(select count(id) from leave_request where MONTH(logged_date) = '12' and deleted_flag = 0 and  Year(logged_date) = Year(NOW())) as 'Dec' ";
		$sql .= "from leave_request where loggedby_id = :id LIMIT 1";

		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['Jan'].",".$userRow1['Feb'].",".$userRow1['Mar'].",".$userRow1['Apr'].",".$userRow1['May'].",".$userRow1['Jun'].",".$userRow1['Jul'].",".$userRow1['Aug'].",".$userRow1['Sep'].",".$userRow1['Oct'].",".$userRow1['Nov'].",".$userRow1['Dec']; 
        return $s;
	}

	public function widget1(){
		$sql = "select count(c_ID) as 'num' from contracts where is_active = 'Y'";

		$stmt1 = $this->runQuery($sql);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['num'];
        return $s;
	}
	public function widget2(){
		$sql = "select count(ID) as 'num' from user_control where active_status = 'Y'";

		$stmt1 = $this->runQuery($sql);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['num'];
        return $s;
	}
	public function widget3(){
		$sql = "select count(C_ID) as 'num' from contracts where contract_end_date < curdate() and is_active = 'Y'";

		$stmt1 = $this->runQuery($sql);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['num'];
        return $s;
	}
	public function widget4(){
		$sql = "select leave_type_value as 'name', count(leave_type_id) as 'id' from leave_request group by leave_type_id order by id desc limit 1";

		$stmt1 = $this->runQuery($sql);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['name'];
        return $s;
	}
	public function widget5($id){
		$sql = "select count(C_ID) as 'num' from contracts where contract_end_date > curdate() and is_active = 'Y' and user_id = :id";

		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['num'];
		$stat = "";
		if($s == "1"){
			$stat = "Valid";
		}
		else{
			$stat = "Invalid";
		}
        return $stat;
	}

	public function widget6($id){
		$sql = "select count(ID) as 'num' from leave_request where status_id != '4' and loggedby_id = :id";

		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['num'];
        return $s;
	}
	public function widget7($id){
		$sql = "select count(ID) as 'num' from leave_request where loggedby_id = :id";

		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['num'];
        return $s;
	}
	public function widget8($id){
		$sql = "select round(annual_leave_avail,2) as 'num' from contracts where is_active = 'Y' and user_id = :id";

		$stmt1 = $this->runQuery($sql);
		$stmt1->bindparam(":id", $id);
        $stmt1->execute();
		$userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
		$s = $userRow1['num'];
		if($s == ""){
			$s = 0;
		}
        return $s;
	}

	public function reset_password($c_id, $password){
		try
		{

			$stmt = $this->conn->prepare("update user_control set password = crypt(:a, gen_salt('bf')) where id = :id");
											
			$stmt->bindparam(":a", $password);
			$stmt->bindparam(":id", $c_id);	
			
			$stmt->execute();	

			return true;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			return false;
		}			

	}
}
?>