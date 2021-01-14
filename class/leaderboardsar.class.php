<?php
// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}


class LeaderboardSAR
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
	 * Get list of top pilots
	 * @param string $type desired "status" filter
	 * @param string $agenttype desired agent filter
	 * @param int $count number or high score places
	 * @param int $lastDays last days range
	 */
	public function getTop($type, $agenttype, $count, $lastDays)
	{		
		// set start and end of period
		$start = gmdate('Y-m-d 00:00:00', strtotime('-'.$lastDays.' days'));
		$end = gmdate('Y-m-d 23:59:59', strtotime("now"));
		
		// prepare the query
		$this->db->query("SELECT COUNT(*) AS cnt, $agenttype, max(lastcontact) as act
							FROM rescuerequest
							WHERE status LIKE :status AND lastcontact BETWEEN :start AND :end 
							GROUP BY $agenttype
							ORDER BY cnt desc, act
							DESC limit :limit");
		$this->db->bind(':status', $type);
		$this->db->bind(':start', $start);
		$this->db->bind(':end', $end);
		$this->db->bind(":limit", $count);

		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Get list of top successful rescue agents
	 * @return $result
	 */
	public function getTopRescueAgents()
	{
		// prepare the query
		$this->db->query("SELECT COUNT(ra.pilot) AS cnt, ra.pilot FROM rescuerequest rr, rescueagents ra 
							WHERE rr.status = 'closed-rescued' AND rr.id=ra.reqid
							GROUP BY ra.pilot
							ORDER BY cnt DESC, ra.entrytime DESC");
		$result = $this->db->resultset();
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Get max days for successful rescue
	 * @param unknown $rescuetype
	 * @return unknown
	 */
	public function getRescueMaxDays($rescuetype)
	{
		$this->db->query("SELECT max(datediff(rr.closedate, rr.requestdate)) AS maxdaystosar
							FROM rescuerequest rr WHERE rr.status = :rescuetype");
		$this->db->bind(":rescuetype", $rescuetype);
		$row= $this->db->single();
		$this->db->closeQuery();
		
		return $row['maxdaystosar'];
	}


	/**
	 * Get payees for SAR Dispatch payouts
	 * @param string $start_date Beginning of date range for desired results
	 * @param string $end_date End of date range for desired results
	 * @param boolean $groupByPilot TRUE to return grouped list; FALSE to return detailed list
	 * @return array $result
	 */
	public function getSARDispatchPayees($start_date, $end_date, $groupByPilot)
	{			
		$start_date = date('Y-m-d 00:00:00', strtotime($start_date));
		$end_date = date('Y-m-d 23:59:59', strtotime($end_date));
		if ($groupByPilot) {
			$sql = "SELECT startagent, COUNT(*) as cnt FROM rescuerequest 
					WHERE status <> 'closed-dup' AND requestdate BETWEEN :start_date AND :end_date 
					GROUP BY startagent";
		}
		else {
			$sql = "SELECT requestdate, startagent, `status`, `system`, pilot
					FROM rescuerequest
					WHERE `status` <> 'closed-dup' AND requestdate BETWEEN :start_date AND :end_date
					ORDER BY requestdate DESC";
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
