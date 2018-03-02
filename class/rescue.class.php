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
 * Class to manage and search SAR requests. 
 *
 */
class Rescue {
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
	 * Create a new rescue request.
	 * 
	 * @param unknown $system
	 * @param unknown $pilot
	 * @param unknown $canrefit
	 * @param unknown $launcher
	 * @param unknown $agentname
	 * @return unknown
	 */
	public function createRequest($system, $pilot, $canrefit, $launcher, $agentname)
	{
		$this->db->query("insert into rescuerequest(system, pilot, canrefit, launcher, startagent, requestdate, lastcontact) values(:system, :pilot, :canrefit, :launcher, :agent, now(), now())");
		$this->db->bind(":system", $system);
		$this->db->bind(":pilot", $pilot);
		$this->db->bind(":canrefit", $canrefit);
		$this->db->bind(":launcher", $launcher);
		$this->db->bind(":agent", $agentname);
		
		// execute insert query
		$this->db->execute();
		// get new rescue ID
		$rescueID = $this->db->lastInsertId();
	
		// return the new created rescue ID
		return $rescueID;
	}
	
	/**
	 * Create a new ESRC rescue request.
	 *
	 * @param unknown $system
	 * @param unknown $pilot
	 * @param unknown $agentname
	 * @param unknown $status
	 * @return unknown
	 */
	public function createESRCRequest($system, $pilot, $agentname, $status)
	{
		$this->db->query("INSERT INTO rescuerequest(system, pilot, startagent, requestdate, 
			finished, lastcontact, status) VALUES (:system, :pilot, :agent, now(), 1, now(), :status)");
		$this->db->bind(":system", $system);
		$this->db->bind(":pilot", $pilot);
		$this->db->bind(":agent", $agentname);
		$this->db->bind(":status", $status);
		
		// execute insert query
		$this->db->execute();
		// get new rescue ID
		$rescueID = $this->db->lastInsertId();
		
		// return the new created rescue ID
		return $rescueID;
	}
	
	/**
	 * Create a new note for a rescue request
	 * @param unknown $rescueID
	 * @param unknown $agentname
	 * @param unknown $note
	 */
	public function createRescueNote($rescueID, $agentname, $note)
	{
		$this->db->query("insert into rescuenote(rescueid, note, agent) values(:rescueid, :note, :agent)");
		$this->db->bind(":rescueid", $rescueID);
		$this->db->bind(":agent", $agentname);
		$this->db->bind(":note", $note);
		
		// execute the insert query
		$this->db->execute();
	}

	/**
	 * Update the last contact time stamp to the current time
	 * @param unknown $rescueID
	 */
	public function registerContact($rescueID)
	{
		$this->db->query("update rescuerequest set lastcontact = now() where id = :rescueid");
		$this->db->bind(":rescueid", $rescueID);
		// 		$database->debugDumpParams();
		$this->db->execute();
	}
	
	/**
	 * Set the reminder of the request  
	 * @param unknown $rescueID
	 * @param unknown $reminderDays
	 */
	public function setReminder($rescueID, $reminderDays)
	{
		$this->db->query("update rescuerequest set reminderdate = date_add(now(), INTERVAL :reminder day) where id = :rescueid");
		$this->db->bind(":rescueid", $rescueID);
		$this->db->bind(":reminder", $reminderDays);
		// 		$database->debugDumpParams();
		$this->db->execute();
	}
	
	/**
	 * Update the status of the rescue request
	 * @param unknown $rescueID
	 * @param unknown $status
	 */
	public function setStatus($rescueID, $status, $agentName = NULL)
	{
		$closeAgent = NULL;
		$finished = 0;
			
// 		if($status === 'closed')
		if(substr($status, 0, 6) === 'closed')
		{
			$closeAgent = $agentName;
			$finished = 1;
		}
		$this->db->query("update rescuerequest set status = :status, closeagent = :closeagent, finished = :finished where id = :rescueid");
		$this->db->bind(":status", $status);
		$this->db->bind(":closeagent", $closeAgent);
		$this->db->bind(":finished", $finished);
		$this->db->bind(":rescueid", $rescueID);
		$this->db->execute();
	}
	
	/**
	 * Check if a SAR request is active for pilot
	 * @return 0 if no request is active or 1 if one is active
	 */
	public function isRequestActive($pilot)
	{
		$this->db->query("select count(1) as cnt from rescuerequest where finished = 0 and pilot = :pilot");
		$this->db->bind(":pilot", $pilot);
		
		$result = $this->db->single();
		
		return $result['cnt'];
	}

	/**
	 * Get all requests by status
	 * @param number $finished 0 - all open requests (default); 1 - alll finished requests
	 * @return array
	 */
	public function getRequests($finished = 0)
	{
		// get requests from database
		$this->db->query("select * from rescuerequest where finished = :finished order by requestdate");
		$this->db->bind(":finished", $finished);
		// $database->execute();
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		return $data;
	}
	
	/**
	 * Get all requests by status
	 * @param number $finished 0 - all open requests (default); 1 - alll finished requests
	 * @return array
	 */
	public function getOpenRequests()
	{
		// get requests from database
		$this->db->query("select * from rescuerequest where finished = 0 and status = 'open' order by requestdate");
		$this->db->bind(":finished", $finished);
		// $database->execute();
		$data = $this->db->resultset();
		$this->db->closeQuery();
	
		return $data;
	}
	
	/**
	 * Get all requests by system
	 * @param number $system - find request for the system
	 * @param number $finished 0 - all open requests (default); 1 - all finished requests
	 * @param number $isCoord 0 - returns only open requests if search for open requests (default); 1 - returns all requests if search for open requests
	 * @return array
	 */
	public function getSystemRequests($system, $finished = 0, $isCoord = 0)
	{
		// set the default query
		$sql = "SELECT * FROM rescuerequest 
							WHERE system = :system and finished = :finished
							ORDER BY requestdate DESC";
		
		/*
		// check if search for open requests and user is NOT coordinator/admin
		if ($finished == 0 && $isCoord == 0)
		{
			// select only open requests
			$sql = "SELECT * FROM rescuerequest
							WHERE system = :system and finished = :finished and status in( 'open', 'system-located')
							ORDER BY requestdate DESC";
		}
		*/
		
		// get requests from database
		$this->db->query($sql);
		$this->db->bind(":system", $system);
		$this->db->bind(":finished", $finished);
		// $database->execute();
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		return $data;
	}
	
	/**
	 * Get all notes by request id
	 * @return array
	 */
	public function getNotes($requestID)
	{
		// get notes from database
		$this->db->query("SELECT notedate, agent, note FROM rescuenote
							WHERE rescueid = :rescueid ORDER BY notedate DESC");
		$this->db->bind(":rescueid", $requestID);
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		return $data;
	}
	
	/**
	 * Get all fields of a request
	 * @param unknown $requestID
	 * @return unknown
	 */
	public function getRequest($requestID)
	{
		$this->db->query("select * from rescuerequest where id = :rescueid");
		$this->db->bind(":rescueid", $requestID);
		$row= $this->db->single();
		$this->db->closeQuery();
		
		return $row;
	}
	
	/**
	 * Get count of all successful rescues
	 * @param unknown $rescuetype
	 * @return unknown
	 */
	public function getRescueCount($rescuetype)
	{
		$this->db->query("SELECT COUNT(id) as cnt FROM rescuerequest WHERE status = :rescuetype");
		$this->db->bind(":rescuetype", $rescuetype);
		$row= $this->db->single();
		$this->db->closeQuery();
		
		return $row['cnt'];
	}
	
	/**
	 * Get average wait time for successful SAR rescues
	 * @return array $arrint - return array of integers for calculation
	 */
	public function getSARWaitTime()
	{
		$this->db->query("SELECT datediff(LastUpdated, requestdate) as daystosar 
							FROM rescuerequest WHERE status = 'closed-rescued'");
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		$ctr = 0;
		foreach ($data as $value) {
			$arrint[$ctr] = $value['daystosar'];
			$ctr++;
		}
		
		return $arrint;
	}
}
?>