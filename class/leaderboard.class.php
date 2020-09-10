<?php
// use database class

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}


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
				WHERE EntryType IN ('sower', 'tender')
				GROUP BY Pilot ORDER BY cnt DESC limit :limit");
		// bind the limit (default 3)
		$this->db->bind(":limit", $count);
		
		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}

	
	/**
	 * Get list of top pilots within specified date range
	 * @param int $count number or high score place
	 * @param int $lastDays last days range
	 * @return array $result - array of top pilots and related activity count
	 */
	public function getTop($count, $lastDays = 30)
	{
		$start = gmdate('Y-m-d 00:00:00', strtotime('-'.$lastDays.' days'));
		$end = gmdate('Y-m-d 23:29:59', strtotime("now"));
			
		$this->db->query("SELECT COUNT(*) AS cnt, a.Pilot, max(ActivityDate) as act
					FROM activity a
					LEFT OUTER JOIN payout_optout po ON po.pilot = a.Pilot
					WHERE (po.optout_type <> 'Stats' OR po.optout_type IS NULL) AND 
						(EntryType IN ('sower', 'tender') AND ActivityDate BETWEEN :start AND :end)
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
	 *  Get list of recently active pilots (by day)
	 */
	public function getActivePilots($days)
	{
		if ($days <=0)
		{
			$days = 60;
		}
				
		$start = gmdate('Y-m-d 00:00:00', strtotime('-'.$days.' days'));
		$end = gmdate('Y-m-d 23:59:59', strtotime("now"));
		$this->db->query("SELECT Pilot, max(ActivityDate) as maxdate FROM activity
					WHERE ActivityDate BETWEEN :start AND :end 
					GROUP BY Pilot ORDER BY maxdate DESC");
		$this->db->bind(':start', $start);
		$this->db->bind(':end', $end);
		
		$result = $this->db->resultset();
		
		$this->db->closeQuery();
		
		return $result;
	}


	/**
	 * Get payees for ESRC payouts
	 * "Agent" actions are paid via SAR Dispatch, so they are not counted here
	 * @param string $start_date Beginning of date range for desired results
	 * @param string $end_date End of date range for desired results
	 * @return array $result
	 */
	public function getESRCPayees($start_date, $end_date, $groupByPilot)
	{			
		if ($groupByPilot) {
			$sql = "SELECT Pilot, COUNT(DISTINCT(`System`)) as cnt FROM activity 
					WHERE EntryType <> 'agent' AND ActivityDate BETWEEN :start_date AND :end_date 
					GROUP BY Pilot";
		}
		else {
			$sql = "SELECT * FROM activity 
					WHERE EntryType <> 'agent' AND ActivityDate BETWEEN :start_date AND :end_date 
					ORDER By ActivityDate DESC";
		}
		$this->db->query($sql);
		$this->db->bind(':start_date', $start_date);
		$this->db->bind(':end_date', $end_date);
		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}

}
?>
