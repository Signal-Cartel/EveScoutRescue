<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

// use database class
require_once '../class/db.class.php';

class Caches
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
	 * Get number of total active caches
	 * @return number of all activ caches
	 */
	public function getActiveCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM cache WHERE Status <> 'Expired'");
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}
	
	/**
	 * Get number of all actions
	 */
	public function getActionTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity");
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}

	/**
	 * Get number of 'sown' actions
	 */
	public function getSownTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'sower'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	/**
	 * Get number of 'tend' actions
	 */
	public function getTendTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'tender'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	/**
	 * Get values of a current system cache
	 */
	public function getCacheInfo($system, $limited = FALSE)
	{
		// check if a system is supplied
		if (!isset($system))
		{
			return;
		}
		
		// check to return only limited information
		if ($limited)
		{
			// yes
			$sql = "SELECT c.System, Location, AlignedWith, Status, ExpiresOn, InitialSeedDate, LastUpdated FROM cache c
						WHERE c.System = :system AND Status <> 'Expired'";
		}
		else
		{
			// not, return all cache infos
			$sql = "SELECT * FROM cache WHERE System = :system AND Status <> 'Expired'";
		}
		$this->db->query($sql);
		$this->db->bind(':system', $system);
		
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Get all infos of a cache
	 * @param $cacheID the id of the cache
	 */
	public function getCacheData($cacheID)
	{
		$this->db->query("SELECT * FROM cache WHERE CacheID = :id");
		$this->db->bind(':id', $cacheID);
		$result = $this->db->single();
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Check if a cache is allowed to be tender
	 * @param unknown $system the system to check
	 * @return void|mixed 0 - cache is not allowed to be tendet; 1 - cache can be tended
	 */
	public function isTendingAllowed($system)
	{
		// check if a system is supplied
		if (!isset($system))
		{
			return;
		}

		// select 0/1 from cache if time diff is >= 24 hours
		$this->db->query("SELECT count(1) as cnt FROM cache 
			WHERE System = :system AND ((Status = 'Healthy' AND 
			(time_to_sec(timediff(UTC_TIMESTAMP(), LastUpdated)) / 3600) >= 24) OR 
			(status = 'Upkeep Required' and ExpiresOn >= UTC_TIMESTAMP))");
		$this->db->bind(':system', $system);
		
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}
	
	/**
	 * Returns the number of caches to expire.
	 * @param unknown $days remaining days
	 * @return mixed number of caches to expire
	 */
	public function expireInDays($days)
	{
		// check if parameter is set
		if (!isset($days))
		{
			// no, set default 5 days
			$days = 5;
		}
		
		// check if the number is greaten than limit
		if ($days > 30)
		{
			// set days to the limit
			$days = 30;
		}
		
		$this->db->query("SELECT count(1) as cnt FROM cache where status <> 'Expired' and datediff(CURRENT_DATE(), lastupdated) > :days");
		$this->db->bind(':days', 30 - $days);
		
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}
	
	/**
	 * Create a new activity for a system
	 * @param $system the name of the Systems
	 * @param $pilot the creating pilot
	 * @param $entrytype the type of the entry (... <add values here> ...)
	 * @param $activitydate the date of the activity
	 * @param $notes notes to add
	 * @param $aidedpilot the aided pilot for agent records
	 */
	public function addActivity($cacheid, $system, $pilot, $entrytype, $activitydate, $notes, $aidedpilot, $cacheStatus = '')
	{
		$this->db->beginTransaction();
		// insert to [activity] table
		$this->db->query("INSERT INTO activity (CacheID, ActivityDate, Pilot, EntryType, System,
						AidedPilot, Note, CacheStatus)
					VALUES (:cacheid, :activitydate, :pilot, :entrytype, :system, :aidedpilot, :note, :cacheStatus)");
		$this->db->bind(':cacheid', $cacheid);
		$this->db->bind(':activitydate', $activitydate);
		$this->db->bind(':pilot', $pilot);
		$this->db->bind(':entrytype', $entrytype);
		$this->db->bind(':system', $system);
		$this->db->bind(':aidedpilot', $aidedpilot);
		$this->db->bind(':note', $notes);
		$this->db->bind(':cacheStatus', $cacheStatus);
		$this->db->execute();
		//get ID from newly inserted [activity] record to use in [cache] record insert/update below
		//$newID = $this->db->lastInsertId();
		//end db transaction
		$this->db->endTransaction();
		
		//return $newID;
	}
	
	/**
	 * Add the text to the end of the current note of the cache
	 * @param unknown $system the current cache of the system
	 * @param unknown $noteText the text to add
	 */
	public function addNoteToCache($cacheid, $noteText)
	{
		$this->db->beginTransaction();
		$this->db->query("UPDATE cache SET Note = CONCAT(Note, :note), LastUpdated = lastupdated WHERE CacheID = :cacheid");
		$this->db->bind(':cacheid', $cacheid);
		$this->db->bind(':note', $noteText);

		$this->db->execute();
		//end db transaction
		$this->db->endTransaction();
	}
	
	/**
	 * Expire a cache in a syste,
	 * @param unknown $system the system of the cache
	 */
	public function expireCache($cacheid, $activitydate)
	{
		$this->db->beginTransaction();
		$this->db->query("UPDATE cache SET Status = 'Expired', lastupdated = :lastupdated WHERE CacheID = :cacheid");
		$this->db->bind(':cacheid', $cacheid);
		$this->db->bind(':lastupdated', $activitydate);
		
		$this->db->execute();
		//end db transaction
		$this->db->endTransaction();
	}
	
	/**
	 * Set the new expire time of a cache and the status
	 * @param unknown $system the system of the cache
	 * @param unknown $status the status to set
	 * @param unknown $expires the expire date
	 */
	public function updateExpireTime($cacheid, $status, $expires, $activitydate)
	{
		$this->db->beginTransaction();
		$this->db->query("UPDATE cache SET ExpiresOn = :expdate, Status = :status, lastupdated = :lastupdated
					WHERE CacheID = :cacheid");
		$this->db->bind(':cacheid', $cacheid);
		$this->db->bind(':status', $status);
		$this->db->bind(':expdate', $expires);
		$this->db->bind(':lastupdated', $activitydate);
		
		$this->db->execute();
		//end db transaction
		$this->db->endTransaction();
	}
	
	/**
	 * Create a new cache in a system
	 * @param unknown $cacheID
	 * @param unknown $system
	 * @param unknown $location
	 * @param unknown $alignedwith
	 * @param unknown $distance
	 * @param unknown $password
	 * @param unknown $activitydate
	 * @param unknown $sower_note
	 */
	public function createCache($system, $location, $alignedwith, $distance, $password, $activitydate, $sower_note)
	{
		$this->db->beginTransaction();
		$this->db->query ("INSERT INTO cache (InitialSeedDate, System, Location,
			AlignedWith, Distance, Password, Status, ExpiresOn, LastUpdated, Note)
			VALUES (:sowdate, :system, :location, :aw, :distance, :pw, :status, :expdate, :lastupdated, :note)" );
		$this->db->bind ( ':sowdate', $activitydate );
		$this->db->bind ( ':system', $system );
		$this->db->bind ( ':location', $location );
		$this->db->bind ( ':aw', $alignedwith );
		$this->db->bind ( ':distance', $distance );
		$this->db->bind ( ':pw', $password );
		$this->db->bind ( ':status', 'Healthy' );
		$this->db->bind ( ':expdate', gmdate ( "Y-m-d", strtotime ( "+30 days", time () ) ) );
		$this->db->bind ( ':lastupdated', $activitydate );
		$this->db->bind ( ':note', $sower_note );
		$this->db->execute ();
		//get ID from newly inserted cache ID for use in [activity] record insert/update
		$newID = $this->db->lastInsertId();
		//end db transaction
		$this->db->endTransaction();

		return $newID;
	}

	/**
	 * Edit existing cache info for the given system
	 * @param unknown $cacheid
	 * @param unknown $location
	 * @param unknown $alignedwith
	 * @param unknown $distance
	 * @param unknown $password
	 */
	public function updateCache($cacheid, $location, $alignedwith, $distance, $password)
	{
		$this->db->beginTransaction();
		$this->db->query ("UPDATE cache SET Location = :location, AlignedWith = :aw, Distance = :distance, 
					Password = :pw WHERE CacheID = :cacheid");
		$this->db->bind ( ':cacheid', $cacheid);
		$this->db->bind ( ':location', $location );
		$this->db->bind ( ':aw', $alignedwith );
		$this->db->bind ( ':distance', $distance );
		$this->db->bind ( ':pw', $password );
		$this->db->execute ();
		//end db transaction
		$this->db->endTransaction();
	}

	/**
	 * Check if the user is a recent sower in given system.
	 * @param unknown $username - the user name of the current user
	 * @param unknown $system - the system in question
	 * @return boolean - return <code>TRUE</code> if the status is true or <code>FALSE</code> otherwise
	 */
	public function isRecentSower($username, $cacheid)
	{
		$this->db->query("SELECT count(1) as cnt FROM `activity` WHERE CacheID = :cacheid AND Pilot = :pilot 
			AND EntryType = 'sower' AND ActivityDate > DATE_SUB(NOW(), INTERVAL 1 DAY)");
		$this->db->bind(":cacheid", $cacheid);
		$this->db->bind(":pilot", $username);
		$data = $this->db->single();
		$this->db->closeQuery();
		return ($data['cnt'] === 1) ? true : false;
	}

	/**
	 * Count of # of systems where sowing/tending have occurred in the given time period.
	 * @param int $daysBack - the number of days back we want the count to go (e.g., "7" would count the last week)
	 * @return int $cnt - return the number of systems
	 */
	public function getSystemsVisited($daysBack)
	{
		$this->db->query("SELECT COUNT(DISTINCT System) AS cnt FROM `activity` 
			WHERE ActivityDate >= (CURDATE() - INTERVAL :daysBack DAY)");
		$this->db->bind(":daysBack", $daysBack);
		$data = $this->db->single();
		$this->db->closeQuery();
		return $data['cnt'];
	}
}
?>