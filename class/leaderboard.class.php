<?php
// use database class

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

require_once '../class/db.class.php';

class Leaderboard
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
	 */
// 	public function getAllHigh(int $count)
	public function getAllHigh($count)
	{
		// check if parameter if wrong
		if ($count <= 0)
		{
			// reset to at least one result
			$count = 3;
		}
		// prepare the all time high query
		$allTimeQuery = $this->db->query("SELECT COUNT(1) AS cnt, Pilot FROM activity
				GROUP BY Pilot ORDER BY cnt DESC limit :limit");
		// bind the limit (default 3)
		$this->db->bind(":limit", $count);
		
		$result = $this->db->resultset();
		
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Get list of top pilots by last days (default 30)
	 * @param int $count number or high score places (default 3)
	 * @param int $lastDays last days range (default 30)
	 */
// 	public function getTopLastDays(int $count, int $lastDays)
	public function getTopLastDays($count, $lastDays)
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
		$this->db->query("SELECT COUNT(*) AS cnt, Pilot
					FROM activity
					WHERE ActivityDate BETWEEN :start AND :end
					GROUP BY Pilot
					ORDER BY cnt DESC limit :limit");
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
	 */
// 	public function getTopPilotsWeek(int $count)
	public function getTopPilotsWeek($count)
	{
		if(gmdate('w', strtotime("now")) == 0) {
			$start = gmdate('Y-m-d', strtotime("now"));
		}
		else {
			$start = gmdate('Y-m-d', strtotime('last Sunday'));
		}
		$end = gmdate('Y-m-d', strtotime("+ 1 day"));
			
		$this->db->query("SELECT COUNT(*) AS cnt, Pilot, max(ActivityDate) as act
					FROM activity
					WHERE ActivityDate BETWEEN :start AND :end
					GROUP BY Pilot
					ORDER BY cnt desc, act DESC limit :limit");
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
// 	public function getActivePilots(int $days)
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