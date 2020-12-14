<?php
// Reviewed for UTC consistency 2020-0524

// check if called from an allowed page
if (!defined('ESRC')) {
	echo "Do not call the script direct!";
	exit ( 1 );
}


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
		//$dtnow = gmdate("Y-m-d H:i:s", strtotime("now"));
		$this->db->query("INSERT INTO rescuerequest(system, pilot, canrefit, launcher, startagent, requestdate, lastcontact) 
			VALUES (:system, :pilot, :canrefit, :launcher, :agent, NOW(), NOW())");
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
	 * Create a new ESRC rescue request - added cacheID and filament May 2020
	 *
	 * @param unknown $system
	 * @param unknown $pilot
	 * @param unknown $agentname
	 * @param unknown $status
	 * @return unknown
	 * @param $eqused 'pas' for probes and scanner or 'fil' for filament or 'bth' for both
	 */
	public function createESRCRequestNew($system, $cache, $pilot, $agentname, $status, $eqused)
	{
		$this->db->query("INSERT INTO rescuerequest(system, cacheID, pilot, startagent, requestdate, finished, lastcontact, status, eq_used) VALUES (:system, :cache, :pilot, :agent, NOW(), 1, NOW(), :status, :eqused)");
		$this->db->bind(":system", $system);
		$this->db->bind(":cache", $cache);
		$this->db->bind(":pilot", $pilot);
		$this->db->bind(":agent", $agentname);
		$this->db->bind(":status", $status);
		$this->db->bind(":eqused", $eqused);
		
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
		$this->db->query("update rescuerequest set lastcontact = NOW() where id = :rescueid");
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
		$finished = 0;
		$closeAgent = NULL;
		$closedate = NULL;
		$qrystring = "UPDATE rescuerequest set status = :status, closeagent = :closeagent, finished = :finished, closedate = NULL WHERE id = :rescueid";
		
		if(substr($status, 0, 6) === 'closed')
		{
			$finished = 1;
			$closeAgent = $agentName;
			$qrystring = "UPDATE rescuerequest set status = :status, closeagent = :closeagent, finished = :finished, closedate = NOW() WHERE id = :rescueid";
		}
		
		$this->db->query($qrystring);
		$this->db->bind(":status", $status);
		$this->db->bind(":closeagent", $closeAgent);
		$this->db->bind(":finished", $finished);
		$this->db->bind(":rescueid", $rescueID);
		$this->db->execute();
	}
	
	/**
	 * Update the locateagent for the rescue request
	 * @param unknown $rescueID - ID of record to update
	 * @param unknown $locateagent - Name of pilot who located system
	 */
	public function setLocateAgent($rescueID, $locateagent)
	{	
		$this->db->query("UPDATE rescuerequest 
							SET locateagent = :locateagent, status = 'system-located' 
							WHERE id = :rescueid");
		$this->db->bind(":locateagent", $locateagent);
		$this->db->bind(":rescueid", $rescueID);
		$this->db->execute();
	}

	/**
	 * Update the locateagent for the rescue request w/o changing status
	 * @param unknown $rescueID - ID of record to update
	 * @param unknown $locateagent - Name of pilot who located system
	 */
	public function updateLocateAgent($rescueID, $locateagent)
	{	
		$this->db->query("UPDATE rescuerequest 
							SET locateagent = :locateagent 
							WHERE id = :rescueid");
		$this->db->bind(":locateagent", $locateagent);
		$this->db->bind(":rescueid", $rescueID);
		$this->db->execute();
	}
	
	/**
	 * Add a rescueagent for the rescue request
	 * @param unknown $rescueID - ID of request record to update
	 * @param unknown $rescueeagent - Name of pilot who helped with rescue
	 */
	public function createRescueAgent($rescueID, $rescueagent)
	{
		$this->db->query("INSERT INTO rescueagents (reqid, pilot)
							VALUES (:rescueid, :rescueagent)");
		$this->db->bind(":rescueagent", $rescueagent);
		$this->db->bind(":rescueid", $rescueID);
		$this->db->execute();
	}
	
	/**
	 * Check if the given rescueagent already exists for the given rescue request
	 * @param unknown $rescueID - ID of request record to update
	 * @param unknown $rescueeagent - Name of pilot who helped with rescue
	 * @return 0 if rescueagent does not exist or 1 if rescueagent does exist
	 */
	public function checkRescueAgent($rescueID, $rescueagent)
	{
		$this->db->query("SELECT COUNT(1) AS cnt FROM rescueagents 
							WHERE pilot = :rescueagent AND reqid = :rescueid");
		$this->db->bind(":rescueagent", $rescueagent);
		$this->db->bind(":rescueid", $rescueID);
		$result = $this->db->single();
		
		return $result['cnt'];
	}

	/**
	 * Delete a specific rescue agent
	 * @param unknown $rowid - ID of [rescueagent] record to update
	 */
	public function deleteRescueAgent($rowid)
	{
		$this->db->query("DELETE FROM rescueagents WHERE id = :rowid");
		$this->db->bind(":rowid", $rowid);
		$this->db->execute();
	}
	
	/**
	 * Get all rescueagents for the given rescue request
	 * @param unknown $rescueID - ID of request record to update
	 * @return array $result - details on all rescue agents for this SAR request
	 */
	public function getRescueAgents($rescueID)
	{
		$this->db->query("SELECT * FROM rescueagents WHERE reqid = :rescueid");
		$this->db->bind(":rescueid", $rescueID);
		$result = $this->db->resultset();
		
		return $result;
	}



	/**
	 * Get open request for Rescue Assistant
	 * @param $system
	 * @param $pilot = rescuee pilot name
	 * @return id
	 */
	public function getRescueAssistantRequest($system, $pilot)
	{
		// set the default query
		$sql = "SELECT * FROM rescuerequest WHERE system = :system AND pilot = :pilot AND finished = 0
					ORDER BY requestdate DESC";
		// get requests from database
		$this->db->query($sql);
		$this->db->bind(":system", $system);
		$this->db->bind(":pilot", $pilot);
		$data = $this->db->resultset();
		$this->db->closeQuery();
		foreach ($data as $value) {
			$id = $value['id'];
			break;
		}
		return $id;
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
	 * Get all requests by status and date
	 * @param number $finished 0 - all open requests (default); 1 - all finished requests
	 * @return array
	 * gmdate("Y-m-d H:i:s", strtotime("now"));
	 */
	public function getRequests($finished = 0, $start = '', $end = '')
	{
		// set start and end dates to defaults for "all time" if not passed into function
		
		$start = (empty($start)) ? '2017-03-18' : $start . " 00:00:00";
		$end = (empty($end)) ? gmdate('Y-m-d H:i:s', strtotime('now')) : $end . " 23:59:59";
		
		// get requests from database
		$this->db->query("SELECT rr.*, datediff(NOW(), rr.requestdate) AS daysopen, w.Class 
							FROM rescuerequest rr, wh_systems w
							WHERE rr.system = w.System AND rr.finished = :finished 
							AND rr.lastcontact BETWEEN :start AND :end
							ORDER BY rr.requestdate");
		$this->db->bind(":finished", $finished);
		$this->db->bind(":start", $start);
		$this->db->bind(":end", $end);
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		return $data;
	}

	/**
	 * Get all requests by status and date
	 * @param number $finished 0 - all open requests (default); 1 - all finished requests
	 * @return array
	 * gmdate("Y-m-d H:i:s", strtotime("now"));
	 */
	public function getClosedRequests($finished = 1, $start = '', $end = '')
	{
		// set start and end dates to defaults for "all time" if not passed into function
		
		$start = (empty($start)) ? '2017-03-18' : $start . " 00:00:00";
		$end = (empty($end)) ? gmdate('Y-m-d H:i:s', strtotime('now')) : $end . " 23:59:59";
		
		// get requests from database
		$this->db->query("Select rr.*, ra.rescueagents,
  DateDiff(rr.closedate, rr.requestdate) As daysopen,
  w.Class
From
  rescuerequest rr Left Join
  (SELECT rescueagents.reqid, GROUP_CONCAT( rescueagents.pilot
SEPARATOR '<br>' ) AS rescueagents
FROM rescueagents
GROUP BY rescueagents.reqid) as ra
    On ra.reqid = rr.id,
  wh_systems w
Where
  rr.system = w.System And
  rr.finished = :finished And
  rr.lastcontact Between :start And :end
Order By
  rr.requestdate DESC");
		$this->db->bind(":finished", $finished);
		$this->db->bind(":start", $start);
		$this->db->bind(":end", $end);
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
		$sql = "SELECT * FROM rescuerequest WHERE system = :system AND finished = :finished
					ORDER BY requestdate DESC";
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
		$this->db->query("SELECT notedate, agent, note 
			FROM rescuenote WHERE rescueid = :rescueid ORDER BY notedate DESC");
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
	 * @param string $start - start date for search
	 * @param string $end - end date for search
	 * @return unknown
	 */
	public function getRescueCount($rescuetype, $start = '', $end = '')
	{
		// set start and end dates to defaults for "all time" if not passed into function
		$start = (empty($start)) ? '2017-03-18' : $start . " 00:00:00";
		$end = (empty($end)) ? gmdate('Y-m-d H:i:s', strtotime('now')) : $end . " 23:59:59";
		$this->db->query("SELECT COUNT(id) as cnt FROM rescuerequest 
			WHERE status = :rescuetype AND lastcontact BETWEEN :start AND :end ");
		$this->db->bind(":rescuetype", $rescuetype);
		$this->db->bind(":start", $start);
		$this->db->bind(":end", $end);
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
		$this->db->query("SELECT datediff(closedate, requestdate) as daystosar 
							FROM rescuerequest WHERE status = 'closed-rescued'");
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		$ctr = 0;
		$arrint = [];
		foreach ($data as $value) {
			$arrint[$ctr] = $value['daystosar'];
			$ctr++;
		}
		
		return $arrint;
	}

	public function getSARWaitTimeMode()
	{
		$this->db->query(
		"SELECT COUNT( rescuerequest.id ) AS Count_id, DATEDIFF( closedate, requestdate ) AS daystosar
		FROM rescuerequest
		WHERE rescuerequest.status = 'closed-rescued'
		GROUP BY DATEDIFF( closedate, requestdate )
		ORDER BY Count_id DESC
		LIMIT 1"
		);
		$data = $this->db->resultset();
		$this->db->closeQuery();
		
		$ctr = 0;
		$arrint = [];
		foreach ($data as $value) {
			$arrint[$ctr] = $value['daystosar'];
			$ctr++;
		}
		
		return $arrint;
	}
	
	
	//


}
?>