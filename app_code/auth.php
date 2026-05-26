<?php

require_once('dbconfig.php');

class HELPER
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
    
    public function select_counter($username){
		$del = 0;
		$stmt1 = $this->runQuery("SELECT counter from user_control where lower(email) like :un");
		$stmt1->bindparam(":un", $username);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['counter'];
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

	public function doLoginUsername($uname,$upass)
	{
		try
		{
			$uname = strtolower($uname);
			$stat = 'Y';
			$stmt = $this->conn->prepare("select (password = crypt(:pass, password)) AS pwd_match, username, role_id, id from USER_CONTROL where lower(username) Like :uname and IS_ACTIVE = :umail;");
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
	
	public function doLoginPhone($uname,$upass)
	{
		try
		{
			$uname = strtolower($uname);
			$stat = 'Y';
			$stmt = $this->conn->prepare("select (password = crypt(:pass, password)) AS pwd_match, username, role_id, id from USER_CONTROL where phone = :uname and IS_ACTIVE = :umail;");
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
    
    public function get_ifrequest($user_id){
		$del = 0;
		$stmt1 = $this->runQuery("SELECT reset_req from user_control where lower(email) like :un");
		$stmt1->bindparam(":un", $user_id);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['reset_req'];
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
	
	public function email_exist($email){
		$del = 0;
		$stmt1 = $this->runQuery("SELECT email from user_control where lower(email) = :un");
		$stmt1->bindparam(":un", $email);
        $stmt1->execute();
        $count = $stmt1->rowCount();
        return $count;
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

	public function get_usernameemail($email){
		$stmt1 = $this->runQuery("SELECT username from user_control where lower(email) = :un");
		$stmt1->bindparam(":un", $email);
        $stmt1->execute();
        $userRow1=$stmt1->fetch(PDO::FETCH_ASSOC);
        return $userRow1['username'];
	}
    

}