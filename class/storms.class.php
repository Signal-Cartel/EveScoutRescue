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
	 * @param string $pilot Name of pilot submitting testimonial
	 * @param bit $anon Anonymize display of testimonial; 1 = anonymous, 0 = not anonymous
	 * @param string $method rescue method, either via rescue cache or search and rescue
	 * @param string $testimonial Text of testimonial
	 * @return boolean TRUE if new report created; FALSE if no new report created
	 */
	public function createStormEntry($evesystem, $pilot, $stormtype, $stormstrength)
	{
		$dateobserved = new DateTime('now', new DateTimeZone('UTC'));

		// check ford duplicate entry before committing
		$this->db->beginTransaction();
		$this->db->query("SELECT COUNT(id) AS countMatches FROM storm_tracker 
							WHERE evesystem = :evesystem AND stormtype = :stormtype
								AND dateobserved >= NOW() - INTERVAL 1 DAY");
		$this->db->bind(":evesystem", $evesystem);
		$this->db->bind(":stormtype", $stormtype);
		$result = $this->db->single();
		$this->db->closeQuery();
		
		if ($result['countMatches'] == 0) {	// no duplicate report found, so add new one
			$this->db->query("INSERT INTO storm_tracker (evesystem, pilot, stormtype, stormstrength, 
									dateobserved)
								VALUES (:evesystem, :pilot, :stormtype, :stormstrength, :dateobserved)");
			$this->db->bind(":evesystem", $evesystem);
			$this->db->bind(":pilot", $pilot);
			$this->db->bind(":stormtype", $stormtype);
			$this->db->bind(":stormstrength", $stormstrength);
			$this->db->bind(":dateobserved", $dateobserved->format('Y-m-d H:i:s'));
			$this->db->execute();
		}

		$this->db->endTransaction();
		return ($result['countMatches'] == 0) ? true : false;
	}
    
	
	/**
	 * Get list of storms.
	 * @param string $last24hrs Get storm reports only from last 24 hours; default to true
	 * @return array $result
	 */
	public function getStorms($last24hrs = true)
	{
        $where_clause = ($last24hrs === false) ? '' : 
            'WHERE dateobserved >= NOW() - INTERVAL 1 DAY';

		$this->db->query("SELECT st.*, st.id AS stormid, mr.regionName, u.characterid
							FROM storm_tracker st
							INNER JOIN mapSolarSystems mss ON st.evesystem = mss.solarSystemName
							INNER JOIN mapRegions mr ON mss.regionID = mr.regionID
							INNER JOIN `user` u ON st.pilot = u.character_name
							$where_clause
							ORDER BY dateobserved DESC");
        $result = $this->db->resultset();
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