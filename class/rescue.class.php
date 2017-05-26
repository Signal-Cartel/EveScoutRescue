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
		$this->db->query("insert into rescuerequest(system, pilot, canrefit, launcher, startagent) values(:system, :pilot, :canrefit, :launcher, :agent)");
		$this->db->bind(":system", $system);
		$this->db->bind(":pilot", $pilot);
		$this->db->bind(":canrefit", $canrefit);
		$this->db->bind(":launcher", $launcher);
		$this->db->bind(":agent", $agentname);
		
		// execute insert query
		$this->db->execute();
		// get new rescue ID
		$rescueID = $this->db->lastInsertId();
		// 	echo "<br> Creaded request: ".$rescueID.": Note:".$data['notes']."<br>";
	
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
		$this->db->query("update rescuerequest set lastcontact = current_timestamp() where id = :rescueid");
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
		$this->db->query("update rescuerequest set reminderdate = date_add(current_timestamp(), INTERVAL :reminder day) where id = :rescueid");
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
		$this->db->query("select id, requestdate, system, pilot, canrefit, launcher, status, finished from rescuerequest where finished = :finished order by requestdate");
		$this->db->bind(":finished", $finished);
		// $database->execute();
		$data = $this->db->resultset();
		// echo "<pre>";
		// print_r($database);
		// echo "\n";
		// print_r($data);
		// echo "</pre>";
		$this->db->closeQuery();
		
		return $data;
	}

}
?>