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
		$sql = "SELECT count(1) as cnt from wh_systems where system = :system";
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
	 * @param $system the system name to check
	 * @return the date till the system is locked
	 */
	public function locked($system) {
		// check if a system name is set
		if (! isset ( $system )) {
			// no, return error code
			return 1;
		}
		
		// check the DB for the system name
		$sql = "SELECT DoNotSowUntil as locked from wh_systems 
			where system = :system and DoNotSowUntil is not null and DoNotSowUntil >= CURRENT_DATE()";
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
		$sql = "SELECT count(1) as locked from wh_systems where DoNotSowUntil is not null and DoNotSowUntil > CURRENT_DATE()";
		// create query
		$this->db->query ( $sql );
		// execute the query
		$result = $this->db->single ();
		// close the query
		$this->db->closeQuery ();
		
		return $result['locked'];
	}
	
	/**
	 * Returns the class and notes for a given wormhole system.
	 * @param $system the system name to check
	 * @return mixed
	 */
	public function getWHInfo($system)
	{
		// check if system name is set
		if (! isset ( $system )) {
			// no, return error code
			return;
		}
		
		// check the DB for the wormhole name
		$sql = "SELECT sys.*, GROUP_CONCAT(
						CONCAT(typ.Name, '/', typ.Destination, '/', typ.Size)
						ORDER BY typ.Destination
					) AS StaticWhInfo
				FROM wh_systems sys, wh_types typ, wh_systemstatics sta
				WHERE sys.System = :system
				AND sys.System = sta.System
				AND sta.StaticType = typ.Name";

		// create query
		$this->db->query ( $sql );
		// and bind parameters
		$this->db->bind ( ":system", $system );
		// execute the query
		$result = $this->db->single();
		// close the query
		$this->db->closeQuery ();
		
		return $result;
	}

	/**
	 * Get all activities of a system
	 * @param unknown $system
	 * @return string
	 */
	public function getSystemActivities($system)
	{
		$this->db->query("SELECT * FROM activity WHERE System = :system ORDER By ActivityDate DESC");
		$this->db->bind(':system', $system);
		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}

	/**
	 * Add system note
	 * @param unknown $system
	 * @param unknown $charname
	 * @param unknown $note
	 */
	public function addSystemNote($system, $charname, $note)
	{
		$this->db->beginTransaction();
		$this->db->query("INSERT INTO systemnote (systemname, noteby, note, notedate) 
			VALUES (:systemname, :username, :note, :notedate)");
		$this->db->bind(':systemname', $system);
		$this->db->bind(':username', $charname);
		$this->db->bind(':note', $note);
		$this->db->bind(':notedate', gmdate("Y-m-d H:i:s", time()));
		$this->db->execute();
		//end db transaction
		$this->db->endTransaction();
	}

	/**
	 * Update existing system note
	 * @param unknown $id
	 * @param unknown $note
	 * @param unknown $editdate
	 */
	public function editSystemNote($id, $note, $editdate)
	{
		$this->db->beginTransaction();
		$this->db->query("UPDATE systemnote SET note = :note, LastUpdated = :nowdt WHERE id = :id");
		$this->db->bind(':note', $note);
		$this->db->bind(':nowdt', $editdate);
		$this->db->bind(':id', $id);
		$this->db->execute();
		//end db transaction
		$this->db->endTransaction();
	}

	/**
	 * Get all system notes for a given system
	 * @param unknown $system
	 * @return string
	 */
	public function getSystemNotes($system)
	{
		$this->db->query("SELECT * FROM systemnote WHERE systemname = :system ORDER By notedate DESC");
		$this->db->bind(':system', $system);
		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}

	/**
	 * Get a single system note, by ID
	 * @param unknown $id
	 * @return string
	 */
	public function getSystemNote($id)
	{
		$this->db->query("SELECT * FROM systemnote WHERE id = :id");
		$this->db->bind(':id', $id);
		$result = $this->db->single();
		$this->db->closeQuery();
		
		return $result;
	}

	/**
	 * Delete system note
	 * @param unknown $id
	 */
	public function deleteSystemNote($id)
	{
		$this->db->beginTransaction();
		$this->db->query("DELETE FROM systemnote WHERE id = :id");
		$this->db->bind(':id', $id);
		$this->db->execute();
		//end db transaction
		$this->db->endTransaction();
	}

	/**
	 * Get valid sow locations for system
	 * @param int $planetCount
	 * @return string array
	 */
	public function getSowLocations($planetCount)
	{
		$sowLocs = array('See Notes','Star',
			'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII','XIII','XIV','XV','XVI','XVII','XVIII','XIX','XX');

		$nonPlanetOptions = 2;
		$sowLocs = array_slice($sowLocs, 0, $nonPlanetOptions + $planetCount);

		return $sowLocs;
	}

	/**
	 * Returns ship size limit by wormhole size
	 * @param int $size the max size if the ship
	 * @return string 'F/D', 'BC', 'BS', 'CAP' or 'SCAP' allowed ship class
	 */
	static function getShipSizeLimit($size) {
	
		$size = intval($size);
	
		if ($size <= 5000000) {
			$massDesc = "F/D";
		}
		else if ($size <= 20000000) {
			$massDesc = "BC";
		}
		else if ($size <= 300000000) {
			$massDesc = "BS";
		}
		else if ($size <= 1350000000) {
			$massDesc = "CAP";
		}
		else  {
			$massDesc = "SCAP";
		}
	
		return '(' . $massDesc . ')';

	}
	
}
?>