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
		$result = $this->checkPermission($username, "ESR Coordinator");
		
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
	 * @param unknown $permission the role to check
	 * @return boolean return <code>TRUE</code> if the permission is set and <code>FALSE</code> otherwise
	 */
	public function checkPermission($username, $permission, $active = true)
	{
		$this->db->query("select count(1) as cnt from user_roles where username = :pilot and rolename = :task and active = :active");
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
	
	/**
	 * Check if the user is a relevant start/locate agent on the SAR request.
	 * @param unknown $username - the user name of the current user
	 * @param unknown $rescueid - the id of the SAR request to check
	 * @return boolean - return <code>TRUE</code> if the permission is set or <code>FALSE</code> otherwise
	 */
	public function isSARAgent($username, $rescueid)
	{
		$this->db->query("SELECT count(1) as cnt FROM rescuerequest WHERE id = :id 
							AND (startagent = :pilot OR locateagent = :pilot2)");
		$this->db->bind(":id", $rescueid);
		$this->db->bind(":pilot", $username);
		$this->db->bind(":pilot2", $username);
		$data = $this->db->single();
		$this->db->closeQuery();
		return ($data['cnt'] === 1) ? true : false;
	}
	
	/**
	 * Check if the user is a relevant rescue agent on the SAR request.
	 * @param unknown $username - the user name of the current user
	 * @param unknown $rescueid - the id of the SAR request to check
	 * @return boolean - return <code>TRUE</code> if the permission is set or <code>FALSE</code> otherwise
	 */
	public function isRescueAgent($username, $rescueid)
	{
		$this->db->query("SELECT COUNT(1) as cnt FROM rescueagents WHERE reqid = :id
							AND pilot = :pilot");
		$this->db->bind(":id", $rescueid);
		$this->db->bind(":pilot", $username);
		$data = $this->db->single();
		$this->db->closeQuery();
		return ($data['cnt'] === 1) ? true : false;
	}
	
	/**
	 * Get list of all users by role, can filter for specific role
	 * @param string $role the specific role we want a list of users for, use '%%' for not specified
	 * @param boolean $active active filter, default to "true"
	 * @return array return list of users
	 */
	public function getUsersByRole($role, $active = true)
	{
		$this->db->query("SELECT * FROM user_roles ur, user u WHERE ur.userid = u.id AND ur.roleid LIKE :role 
			AND ur.active = :active ORDER BY ur.username");
		$this->db->bind(":role", $role);
		$this->db->bind(":active", ($active) ? 1 : 0);
		// get the resultset
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		return $data;
	}
	
	/**
	 * Check if the user of the current session is part of the alliance
	 * @return boolean
	 */
	public static function isAllianceUserSession()
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		return $_SESSION['auth_characteralliance'] == 99005130;
	}
}