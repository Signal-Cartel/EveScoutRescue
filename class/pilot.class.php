<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

// use database class
require_once '../class/db.class.php';

class Pilot
{
	var $db= null;
	
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
	 * Get number of pilot activities within a time frame.
	 * @param unknown $pilot name of the pilot
	 * @param unknown $start time frame start
	 * @param unknown $end time frame end
	 * @return mixed
	 */
	public function getActivityCount($pilot, $start, $end)
	{
		$this->db->query("SELECT COUNT(1) AS cnt
					FROM activity
					WHERE ActivityDate BETWEEN :start AND :end
					AND Pilot = :pilot");
		$this->db->bind(':pilot', $pilot);
		$this->db->bind(':start', $start);
		$this->db->bind(':end', $end);
		
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}

	/**
	 * Get number of pilot activities per type.
	 * @param unknown $pilot name of the pilot
	 * @return mixed
	 */
	public function getActivityTypeCount($pilot, $activity)
	{
		$this->db->query("SELECT COUNT(1) AS cnt
					FROM activity
					WHERE Pilot = :pilot and EntryType = :activity");
		$this->db->bind(':pilot', $pilot);
		$this->db->bind(':activity', $activity);
		
		$result = $this->db->single();
		$this->db->closeQuery();
		
		return $result['cnt'];
	}
	
	/**
	 * Get all activities of a pilot
	 * @param unknown $pilot the name of the pilot
	 * @return array
	 */
	public function getActivities($pilot)
	{
		$this->db->query("SELECT ActivityDate, EntryType, System
									FROM activity
									WHERE Pilot = :pilot
									ORDER BY ActivityDate DESC");
		$this->db->bind(':pilot', $pilot);
		
		$rows = $this->db->resultset();
		
		$this->db->closeQuery();
		
		return $rows;
	}

	/**
	 * Get medals for given medal type
	 * @param unknown $medalid
	 * @return unknown
	 */
	public function getMedals($medalid)
	{
		$this->db->query("SELECT * FROM `medals` WHERE medalid = :medalid ORDER BY dateawarded DESC, pilot");
		$this->db->bind(":medalid", $medalid);
		$rows = $this->db->resultset();
		$this->db->closeQuery();
		
		return $rows;
	}
}
?>