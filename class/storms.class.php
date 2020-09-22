<?php

// check if called from an allowed page
if (!defined('ESRC')) {
	echo "Do not call the script direct!";
	exit ( 1 );
}


class Storms
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
	 * Create new entry into [storm_tracker] table
	 * @param array $arrayStormReport Array of values needed to populate report record
	 */
	public function createStormEntry($arrayStormReport)
	{
		$dateobserved = new DateTime('now', new DateTimeZone('UTC'));

		$sql = "INSERT INTO storm_tracker (storm_id, evesystem, pilot, stormtype, stormstrength, 
					dateobserved, observation_type)
				VALUES (:storm_id, :evesystem, :pilot, :stormtype, :stormstrength, :dateobserved, 
					:observation_type)";
		
		$this->db->query($sql);
		$this->db->bind(":storm_id", $arrayStormReport['storm_id']);
		$this->db->bind(":evesystem", $arrayStormReport['evesystem']);
		$this->db->bind(":pilot", $arrayStormReport['pilot']);
		$this->db->bind(":stormtype", $arrayStormReport['stormtype']);
		$this->db->bind(":stormstrength", $arrayStormReport['stormstrength']);
		$this->db->bind(":dateobserved", $dateobserved->format('Y-m-d H:i:s'));
		$this->db->bind(":observation_type", $arrayStormReport['observation_type']);
		$this->db->execute();
	}


	/**
	 * Get list of most recent storm reports for each defined storm.
	 * @return array $result
	 */
	public function getRecentReports()
	{
		$sql = "SELECT st.*, mr.regionName, u.characterid
				FROM storm_tracker st
				INNER JOIN mapSolarSystems mss ON st.evesystem = mss.solarSystemName
				INNER JOIN mapRegions mr ON mss.regionID = mr.regionID
				INNER JOIN `user` u ON st.pilot = u.character_name
				WHERE st.id IN (
					SELECT MAX(id) AS rowid
					FROM storm_tracker
					WHERE storm_id > 0
					GROUP BY storm_id
					ORDER BY MAX(dateobserved) DESC
				)
				GROUP BY st.storm_id
				ORDER BY st.id DESC";
		$this->db->query($sql);
        $result = $this->db->resultset();
        $this->db->closeQuery();

		return $result;
	}


	/**
	 * Get name of defined storm based on storm_id.
	 * @return array $result
	 */
	static public function getStormName($id)
	{
		switch ($id) {
			case 1: return 'Electric A'; break;
			case 2: return 'Electric B'; break;
			case 3: return 'Exotic A'; break;
			case 4: return 'Exotic B'; break;
			case 5: return 'Gamma A'; break;
			case 6: return 'Gamma B'; break;
			case 7: return 'Plasma A'; break;
			case 8: return 'Plasma B'; break;
		}
	}
    
	
	/**
	 * Get list of storms.
	 * @param string $interval Get storm reports between now and this inteval; defaults to 36 hours
	 * @return array $result
	 */
	public function getStormReports($return_type = '', $stormid = 0, $interval = '36 HOUR')
	{
		switch ($return_type) {
			case 'named':
				$where_clause = 'WHERE storm_id = :storm_id';
			break;

			case 'interval':
				$where_clause = 'WHERE dateobserved >= NOW() - INTERVAL '. $interval;
			break;

			case 'all':
			default:
				$where_clause = 'WHERE 1=1';
		}
		$this->db->query("SELECT st.*, mr.regionName, u.characterid
							FROM storm_tracker st
							INNER JOIN mapSolarSystems mss ON st.evesystem = mss.solarSystemName
							INNER JOIN mapRegions mr ON mss.regionID = mr.regionID
							INNER JOIN `user` u ON st.pilot = u.character_name
							$where_clause
							ORDER BY dateobserved DESC");
		if ($stormid > 0) { $this->db->bind(':storm_id', $stormid); }
        $result = $this->db->resultset();
        $this->db->closeQuery();

		return $result;
	}

	/**
	 * Get most active storm chasing pilot,
		* most recent downtime to prior downtime
		* This is used by discord to award top pilot with icon on name
	 */
	public function getTopStormChaser()
	{
		$sql = "SELECT u.characterid AS `uid`, st.pilot AS Pilot, Count(st.id) As Actions
				FROM storm_tracker st
				INNER JOIN `user` u ON st.pilot = u.character_name			  
				WHERE
					(
						hour(UTC_TIMESTAMP) between 0 and 10 
						and st.dateobserved >= UTC_DATE - interval 37 hour -- 11am two days ago
						and st.dateobserved < UTC_DATE - interval 13 hour -- 11am yesterday
					)
					OR
					(
						hour(UTC_TIMESTAMP) between 11 and 23
						and st.dateobserved >= UTC_DATE - interval 13 hour -- 11am yesterday
						and st.dateobserved < UTC_DATE + interval 11 hour -- today 11am
					)
				GROUP BY st.pilot
				ORDER BY Actions DESC";

		$this->db->query($sql);
		$result = $this->db->single();		
		$this->db->closeQuery();	

		return $result;
	}


	/**
	 * Delete storm report entry from db
	 * @param int $rowid ID of database row to delete
	 */
	public function removeStormEntry($rowid)
	{
		$this->db->beginTransaction();
		$this->db->query("DELETE FROM storm_tracker WHERE id = :id");
		$this->db->bind(':id', $rowid);
		$this->db->execute();
		$this->db->endTransaction();
	}

}
?>