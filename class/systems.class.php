<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

// use database class
require_once '../class/db.class.php';
class Systems {
	var $db = null;

	public function __construct($database = NULL)
	{
		if (isset($database))
		{
			$this->db = $database;
		}
		else
		{
			// create a new database class instace
			$this->connectDatabase ();
		}
	}
	
	/**
	 * Create a new DB connection.
	 */
	private function connectDatabase() {
		$this->db = new Database ();
	}
	
	/**
	 * Validate the system names agains the wh_systems table
	 * 
	 * @param unknown $system        	
	 *
	 * @return a status code for valid systems or a useful error code
	 *         0 - system is valid
	 *         1 - invalid system name
	 */
	public function validatename($system) {
		// check if a value is supplied
		if (! isset ( $system )) {
			return 1;
		}
		
		// make the system name uppercase; should be move to a separate method
		$system = strtoupper ( $system );
		
		// simple checks without DB access
		// check for a valid system name length
		if (! (strlen ( $system ) === 7)) {
			
			return 1;
		}
		
		// check if system name starts with 'J'
		if (! (substr ( $system, 0, 1 ) === 'J')) {
			// no, wrong system name
			return 1;
		}
		
		// check if the system exists in the database
		$result = $this->exists ( $system );
		
		return $result;
	}
	
	/**
	 * Check the database that the system exists
	 * 
	 * @param $system the
	 *        	system name to check
	 * @return 0 - the system exists in the DB
	 *         1 - the system is unknown in the DB
	 */
	private function exists($system) {
		// check if a system name is set
		if (! isset ( $system )) {
			// no, return error code
			return 1;
		}
		
		// check the DB for the system name
		$sql = "select count(1) as cnt from wh_systems where system = :system";
		// create query
		$this->db->query ( $sql );
		// and bind parameters
		$this->db->bind ( ":system", $system );
		// execute the query
		$result = $this->db->single ();
		// close the query
		$this->db->closeQuery ();
		
		return 1 - $result ['cnt'];
	}
	
	/**
	 * Check the system to be locked
	 *
	 * @param $system the
	 *        	system name to check
	 * @return the date till the system is locked
	 */
	public function locked($system) {
		// check if a system name is set
		if (! isset ( $system )) {
			// no, return error code
			return 1;
		}
		
		// check the DB for the system name
		$sql = "select DoNotSowUntil as locked from wh_systems where system = :system and DoNotSowUntil is not null and DoNotSowUntil >= CURRENT_DATE()";
		// create query
		$this->db->query ( $sql );
		// and bind parameters
		$this->db->bind ( ":system", $system );
		// execute the query
		$result = $this->db->single ();
		// close the query
		$this->db->closeQuery ();
		
		return $result ['locked'];
	}

	/**
	 * Returns the number of currently locked (on request) systems.
	 * @return mixed
	 */
	public function getLockedCount()
	{
		// check the DB for the system name
		$sql = "select count(1) as locked from wh_systems where DoNotSowUntil is not null and DoNotSowUntil > CURRENT_DATE()";
		// create query
		$this->db->query ( $sql );
		// execute the query
		$result = $this->db->single ();
		// close the query
		$this->db->closeQuery ();
		
		return $result['locked'];
	}
}

?>
