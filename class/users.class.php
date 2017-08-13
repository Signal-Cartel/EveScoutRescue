<?php
// use database class

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

require_once '../class/db.class.php';

/**
 * Class to check (and later manage) permissions of a user. 
 *
 * Current implementation bases on a singl table.
 * 
 * Implementation should be improved if a user permissions handling UI exists.
 */
class Users {
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
	 * Creates a new DB connection.
	 */
	private function connectDatabase() {
		$this->db = new Database ();
	}
	
	/**
	 * Check if the user is a SAR coordinator
	 * @param unknown $username
	 * @return string
	 */
	public function isSARCoordinator($username)
	{
		$result = $this->checkPermission($username, "SARCoordinator");
		
		return $result;
	}
	
	/**
	 * Check if the user is a general admin.
	 * @param unknown $username
	 * @return string
	 */
	public function isAdmin($username)
	{
		$result = $this->checkPermission($username, "Admin");
// 		echo "Admin: ".$result." / ".true."<br>"; 
		return $result;
	}
	
	/**
	 * Internal permission check function.
	 * @param unknown $username the user name of the current user
	 * @param unknown $permission the permission to check
	 * @return boolean return <code>TRUE</code> if the permission is set and <code>FALSE</code> otherwise
	 */
	public function checkPermission($username, $permission, $active = true)
	{
		$this->db->query("select count(1) as cnt from pilots where pilot = :pilot and task = :task and active = :active");
		$this->db->bind(":pilot", $username);
		$this->db->bind(":task", $permission);
		$this->db->bind(":active", ($active) ? 1 : 0);
// 		$this->db->debugDumpParams();echo "<br>";		
		// get the result line
		$data = $this->db->single();
		// close the query
		$this->db->closeQuery();
// 		echo "$username $permission $active ". (($active) ? 1 : 0)." = ".$data['cnt']."<br>";
		return ($data['cnt'] === 1) ? true : false;
	}
}