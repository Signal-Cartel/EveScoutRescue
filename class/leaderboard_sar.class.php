<?php
// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

// use database class
require_once '../class/db.class.php';

class SARLeaderboard
{
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
	 * Get the all time high pilots. 
	 * @param int $count number or high score places (default 3)
	 * @param string $type desired "status" filter
	 */
	public function getAllHigh($count, $type)
	{
		// check if parameter if wrong
		if ($count <= 0)
		{
			// reset to at least one result
			$count = 3;
		}
		// prepare the all time high query
		$allTimeQuery = $this->db->query("SELECT COUNT(1) AS cnt, startagent 
											FROM rescuerequest
											WHERE status = :status
											GROUP BY startagent 
											ORDER BY cnt
											DESC limit :limit");
		// bind the limit (default 3)
		$this->db->bind(":limit", $count);
		$this->db->bind(":status", $type);
		
		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Get list of top pilots by last days (default 30)
	 * @param int $count number or high score places (default 3)
	 * @param int $lastDays last days range (default 30)
	 * @param string $type desired "status" filter
	 */
	public function getTopLastDays($count, $lastDays, $type)
	{
		// check if parameter if wrong
		if ($count <= 0)
		{
			// reset to at least one result
			$count = 1;
		}
		
		if ($lastDays <= 0)
		{
			$lastDays = 30;
		}
		
		// prepare the query
		$start = gmdate('Y-m-d', strtotime('-'.$lastDays.' days'));
		$end = gmdate('Y-m-d', strtotime("+ 1 day"));
		$this->db->query("SELECT COUNT(*) AS cnt, startagent, max(lastcontact) as act
					FROM rescuerequest
					WHERE status = :status AND lastcontact BETWEEN :start AND :end
					GROUP BY startagent
					ORDER BY cnt desc, act
					DESC limit :limit");
		$this->db->bind(':status', $type);
		$this->db->bind(':start', $start);
		$this->db->bind(':end', $end);
		// bind the limit (default 5)
		$this->db->bind(":limit", $count);

		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Get list of top pilots of current week
	 * @param int $count number or high score places (default 3)
	 * @param string $type desired "status" filter
	 */
	public function getTopPilotsWeek($count, $type)
	{
		if (gmdate('w', strtotime("now")) == 0) {
			$start = gmdate('Y-m-d', strtotime("now"));
		}
		elseif (gmdate('w', strtotime("now")) == 1) {
			$start= gmdate('Y-m-d', strtotime("- 1 day"));
		}
		else {
			$start = gmdate('Y-m-d', gmdate(strtotime('last Sunday')));
		}
		$end = gmdate('Y-m-d', strtotime("+ 1 day"));
			
		$this->db->query("SELECT COUNT(*) AS cnt, startagent, max(lastcontact) as act
					FROM rescuerequest
					WHERE status = :status AND lastcontact BETWEEN :start AND :end
					GROUP BY startagent
					ORDER BY cnt desc, act
					DESC limit :limit");
		$this->db->bind(':status', $type);
		$this->db->bind(':start', $start);
		$this->db->bind(':end', $end);
		$this->db->bind(':limit', $count);
		
		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Get personal stats of a pilot
	 */

	/**
	 *  Get list of recently active pilots (by day)
	 */
	public function getActivePilots($days)
	{
		if ($days <=0)
		{
			$days = 60;
		}
		
		$start = gmdate('Y-m-d', strtotime('-'.$days.' days'));
		$end = gmdate('Y-m-d', strtotime("+ 1 days"));
		$this->db->query("SELECT Pilot, max(ActivityDate) as maxdate FROM activity
					WHERE ActivityDate BETWEEN :start AND :end
					GROUP BY Pilot ORDER BY maxdate DESC");
		$this->db->bind(':start', $start);
		$this->db->bind(':end', $end);
		
		$result = $this->db->resultset();
		
		$this->db->closeQuery();
		
		return $result;
	}
}

?>