<?php
// use database class
require_once '../class/db.class.php';

class Leaderboard
{
	var $db = null;
	
	public function __construct()
	{
		// create a new database class instace
		$this->db = new Database();
	}

	/**
	 * Get the all time high pilots. 
	 * @param int $count number or high score places (default 3)
	 */
	public function getAllHigh(int $count = 3)
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
	public function getTopLastDays(int $count = 5, int $lastDays = 30)
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
		$start = date('Y-m-d', strtotime('-'.$lastDays.' days'));
		$end = date('Y-m-d', strtotime("tomorrow"));
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
	
	/**
	 * Get personal stats of a pilot
	 */

	/**
	 *  Get list of recently active pilots (by day)
	 */
	public function getActivePilots(int $days = 60)
	{
		if ($days <=0)
		{
			$days = 60;
		}
		
		$start = date('Y-m-d', strtotime('-'.$lastDays.' days'));
		$end = date('Y-m-d', strtotime("tomorrow"));
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