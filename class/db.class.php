<?php
if (strpos($_SERVER['HTTP_HOST'],'dev') === FALSE) {
	$config = parse_ini_file('../../config/esr_dbconfig.ini');
}
else {
	$configpath = preg_replace('/\/htdocs\/.*$/', '', $_SERVER['REDIRECT_DOCUMENT_ROOT']) . '/htdocs/config/esr_dbconfig_dev.ini';
	$config = parse_ini_file($configpath);
}

// check if a config is found
if ($config === FALSE)
{
	echo "<p><b>No DB config found!</b></p>";
	// add error logging here
	exit(1);
}

//define db connection vars
define("DB_HOST", $config['hostname']);
define("DB_USER", $config['username']);
define("DB_PASS", $config['password']);
define("DB_NAME", $config['dbname']);

// check for enabled maintenance mode in DB
define("MAINTENANCE", $config['maintenance']);

/**
 * Database connection handling wrapper. It's possible to run one query at a time only.
 */
class Database
{
	private $host = DB_HOST;
	private $user = DB_USER;
	private $pass = DB_PASS;
	private $dbname = DB_NAME;
	
	// the database connection handle
	private $dbh;
	// the last error
	private $error;
	// the current statement to execute and retrieve results
	private $stmt;
	// flag indication if the statement is already executed
	private $executed = FALSE;
	
	/**
	 * Create a new database connection instance.
	 * 
	 * Note: The database can set to maintenance mode. This mode is used for migration tasks only.
	 * Only connections with the 'connectMaintenance' flag set can use this mode.
	 * 
	 * @param string $connectMaintenance (default: <code>FALSE</code>) connect only in maintenance mode if <code>TRUE</code>.  
	 */
	public function __construct($connectMaintenance = FALSE)
	{
		if ($connectMaintenance == FALSE && MAINTENANCE === '1')
		{
			?>
			<div class="white"><b>System is in maintenance mode. Retry later!</b></div>
			<?php
			exit(1);
		}
		else if ($connectMaintenance == TRUE && MAINTENANCE != '0')
		{
			?>
			<div class="white"><b>Maintenance connection are allowed in 'maintenance' mode only!!!</b></div>
			<?php
			exit(1);
		}
		
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
			echo "PDO creation failed: ". htmlspecialchars($this->error);
			exit(1);
		}
	}
	
	public function query($query) 
	{
		$this->executed = FALSE;
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
		// set the executed flag to TRUE
		$this->executed = TRUE;
		// before execute the current query
		return $this->stmt->execute();
	}
	
	public function resultset() 
	{
		// check if the statement is already executed
		if (!$this->executed)
		{
			// no, execute the statement
			$this->execute();
			// before retrieve result set
		}
		// return result set of query
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/*
	 * Close the cursor to the database of the last statement.
	 *
	 * IMPORTANT:
	 * Do not access the result set or the result size after closing the statement!
	 */
	public function closeQuery()
	{
		// check if the statement is executed
		if ($this->executed)
		{
			// yes, close the result cursor
			$this->stmt->closeCursor();
		}
		$this->stmt = NULL;
	}
	
	public function single()
	{
		// check if the statement is already executed
		if (!$this->executed)
		{
			// no, execute the statement
			$this->execute();
			// before retrieve result set
		}
		// return result set of query
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