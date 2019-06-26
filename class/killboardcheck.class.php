<?php
// use database class

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script directly!";
	exit ( 1 );
}

require_once '../class/db.class.php';

/**
 * Class to check killboard details. 
 *
 * CREATE TABLE `esrcuser`.`killboard_check` ( `lastUpdated` DATETIME NULL ) ENGINE = InnoDB;
 *
 */
class KillboardActivity {
	
	var $db = null;
	
	public function __construct($database = NULL)
	{
		if (isset($database))
		{
			$this->db = $database;
		}
		else
		{
			// create a new database class instance
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
	 * Get last updated timestamp to see when we checked the killboard
	 * @return timestamp
	 */
	public function getLastKillboardCheck()
	{
		$selectStmt = "SELECT lastUpdated FROM killboard_check;";
		
		$this->db->query($selectStmt);		
		$data = $this->db->resultset();
		$this->db->closeQuery();

		if (count($data) == 0) {
			$this->db->query("INSERT INTO `killboard_check` (`lastUpdated`) VALUES (UTC_TIMESTAMP());");
			$this->db->execute();	

			$this->db->query($selectStmt);
			$data = $this->db->resultset();
			$this->db->closeQuery();
		}
		
		return $data[0]['lastUpdated'];
	}
	
	/**
	 * Update last checked timestamp to show we've checked the killboard
	 */
	public function updateLastKillboardCheck()
	{
		$this->db->query("UPDATE `killboard_check` SET `lastUpdated` = (UTC_TIMESTAMP()) WHERE 1;");
		$this->db->execute();
	}
}