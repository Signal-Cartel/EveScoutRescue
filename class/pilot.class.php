<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}



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
	 * @param string $medalid
	 * @param boolean $multiple indicates whether $medalid contains multiple values; default to "false"
	 * @return array $rows
	 */
	public function getMedals($medalid, $multiple = false)
	{
		$where_clause = ($multiple === false) ? 'medalid = :medalid' : 'medalid IN ('. $medalid .')';
		
		$this->db->query("SELECT * FROM `medals` 
							WHERE $where_clause 
							ORDER BY dateawarded DESC, pilot");
		if ($multiple === false) { $this->db->bind(":medalid", $medalid); }
		$rows = $this->db->resultset();
		$this->db->closeQuery();
		
		return $rows;
	}


	/**
	 * Prepare HTML for hall of fame table
	 * @param string $medaltype the type of the medal for this list
	 * @param string $medalname the name of the medal for this list
	 * @param int $min the minimum number of caches at this level
	 * @param string $medalid the type of medal to display in this table
	 * @param array $rows the array of info to display in the table
	 */
	static function printTable($medaltype, $medalname, $min, $medalid, $rows) 
	{
		switch ($medaltype) {
			case 'esrc':
				$colDescr = 'Awarded to pilots upon sowing or tending '. $min .' rescue caches.';
				break;
			case 'sardisp':
				$colDescr = 'Awarded to a Signaleer for coming to the aid of '. $min .' or more 
					stranded capsuleers.';
				break;
			case 'sar_rescue':
				$colDescr = 'Awarded to pilots upon completing '. $min .' successful rescue '.
					 ($min == 1 ? 'mission' : 'missions');
				break;
		}
	?>
		<div class="col-md-4 white">
			<h2 style="text-align: center;"><?=$medalname?></h2>
			<p style="text-align: center;"><?=$colDescr?><br />
				<?php 
				$filename = '../img/'. $medalname .'.PNG';
				if (file_exists($filename)) {
					echo '<img height="216" src="'. $filename .'">';
				}
				?>
			</p>
			<table class="table white">
				<thead>
					<tr>
						<th>Pilot</th>
						<th>Date Awarded</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($rows as $value) {
						if (($value['medalid']) != $medalid) { continue; }	// skip row if it's not for the correct medal
						echo '<tr>';
						echo '	<td>'. $value['pilot'] .'</td>';
						echo '	<td>'. Output::getEVEdate($value['dateawarded']) .'</td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	<?php 
	}

}
?>