<?php
//serve up the correct config file for db connection, based on which environment we are in
if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
    if (strpos($_SERVER['HTTP_HOST'], 'dev') === false) {
	    $config = parse_ini_file('../../config/esr_dbconfig.ini');		//production
    }
    else {
	    $config = parse_ini_file('../../config/esrdev_dbconfig.ini');	//dev
    }
}
else {
    $config = parse_ini_file('../config/dbconfig_local.ini');	//local
}

//define db connection vars
define("DB_HOST", $config['hostname']);
define("DB_USER", $config['username']);
define("DB_PASS", $config['password']);
define("DB_NAME", $config['dbname']);

class Database
{
	private $host = DB_HOST;
	private $user = DB_USER;
	private $pass = DB_PASS;
	private $dbname = DB_NAME;
	
	private $dbh;
	private $error;
	private $stmt;
	
	public function __construct() 
	{
		// Set DSN
		$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
		
		// Set options
		$options = array(
			PDO::ATTR_PERSISTENT => true, 
			PDO::ATTR_EMULATE_PREPARES => false, 
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);
		// Create a new PDO instance
		try {
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
		}
		// Catch any errors
		catch (PDOException $e) {
			$this->error = $e->getMessage();
		}
	}
	
	public function query($query) 
	{
		$this->stmt = $this->dbh->prepare($query);
	}
	
	public function bind($param, $value, $type = null) 
	{
		if (is_null($type)) {
		  switch (true) {
			case is_int($value):
			  $type = PDO::PARAM_INT;
			  break;
			case is_bool($value):
			  $type = PDO::PARAM_BOOL;
			  break;
			case is_null($value):
			  $type = PDO::PARAM_NULL;
			  break;
			default:
			  $type = PDO::PARAM_STR;
		  }
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	
	public function execute() 
	{
		return $this->stmt->execute();
	}
	
	public function resultset() 
	{
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function single()
	{
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function rowCount()
	{
		return $this->stmt->rowCount();
	}
	
	public function lastInsertId()
	{
		return $this->dbh->lastInsertId();
	}
	
	public function beginTransaction()
	{
		return $this->dbh->beginTransaction();
	}
	
	public function endTransaction()
	{
		return $this->dbh->commit();
	}
	
	public function cancelTransaction()
	{
		return $this->dbh->rollBack();
	}
	
	public function debugDumpParams()
	{
		return $this->stmt->debugDumpParams();
	}
}