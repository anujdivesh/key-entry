<?php
class Database
{   
    private $server = "mysql:host=localhost;dbname=manualDB";
    private $user = "root";
    private $pass = "";
    private $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );

    protected $con;

    /* Function for opening connection */
    public function openConnection()
    
    {
        try 
        {
            
            $this->con = new PDO("pgsql:host=192.168.7.18;port=5432;dbname=manualDB;user=anuj;password=Simple10");
            
            return $this->con;
        } 
        catch (PDOException $e) 
        {
            
            echo "There is some problem in connection: " . $e->getMessage();
        }
    }

    /* Function for closing connection */
    public function closeConnection()
    {
        $this->con = null;
    }
}
?>