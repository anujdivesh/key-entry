<?php

require_once('dbconfig.php');
require_once("session.php");

class DIV{
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
    
    public function getDivision(){
        $stmt1 = $this->runQuery("SELECT id, org_name FROM organizations");
        $stmt1->execute();
      //  $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;
    }

    public function getrole(){
        $stmt1 = $this->runQuery("SELECT role_id, role_name FROM role");
        $stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;
    }

    public function getuserid(){
        $stmt1 = $this->runQuery("SELECT id, username FROM user_control where is_active = 'Y'");
        $stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;
    }

    public function getleave(){
        $stmt1 = $this->runQuery("SELECT L_ID, L_NAME FROM LEAVE_TYPE");
        $stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;
    }

    public function getdelegat($division, $role){
        $query = "";
        if($role == "3"){
            $query = "SELECT ID, USERNAME FROM user_control where divison_id = :div and role_id = '4'";
        }
        elseif ($role == "4")
        {
            $query = "SELECT ID, USERNAME FROM user_control where role_id = '5'";
        }
        else{
            $query = "SELECT ID, USERNAME FROM user_control where divison_id = :div and (role_id = '3' or role_id = '4')";
        }
        $stmt1 = $this->runQuery($query);
        $stmt1->bindparam(":div", $division);
        $stmt1->execute();
        $userRow1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        return $userRow1;
    }

}
?>