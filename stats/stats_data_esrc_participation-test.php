<?php
define('ESRC', TRUE);
session_start();

include_once '../class/db.class.php';

// instantiate db object
$db = new Database();

// prepare SQL based on request
switch ($_REQUEST['type']) {
	case 'Agents':
		$sql = "SELECT a.Pilot, COUNT(EntryType) AS cnt 
			FROM activity a
			LEFT OUTER JOIN payout_optout po ON po.pilot = a.Pilot
			WHERE (po.optout_type <> 'Stats' OR po.optout_type IS NULL) AND 
				(ActivityDate BETWEEN :start AND :end AND (EntryType = 'Agent' OR EntryType = 'agent')) 
			GROUP BY Pilot, EntryType
			ORDER BY cnt DESC LIMIT 10";
		break;
	case 'Tenders':
		$sql = "SELECT a.Pilot, COUNT(EntryType) AS cnt 
			FROM activity a
			LEFT OUTER JOIN payout_optout po ON po.pilot = a.Pilot
			WHERE (po.optout_type <> 'Stats' OR po.optout_type IS NULL) AND 
				(ActivityDate BETWEEN :start AND :end AND (EntryType = 'Tender' OR EntryType = 'tender'))
			GROUP BY Pilot, EntryType
			ORDER BY cnt DESC LIMIT 10";
		break;
	case 'Sowers':
		$sql = "SELECT a.Pilot, COUNT(EntryType) AS cnt 
			FROM activity a
			LEFT OUTER JOIN payout_optout po ON po.pilot = a.Pilot
			WHERE (po.optout_type <> 'Stats' OR po.optout_type IS NULL) AND 
				(ActivityDate BETWEEN :start AND :end AND (EntryType = 'Sower' OR EntryType = 'sower')) 
			GROUP BY Pilot, EntryType
			ORDER BY cnt DESC LIMIT 10";
		break;
	case 'Overall':
	default:
		$sql = "SELECT a.Pilot, COUNT( EntryType ) AS cnt 
			FROM  `activity` a
			LEFT OUTER JOIN payout_optout po ON po.pilot = a.Pilot
			WHERE (po.optout_type <> 'Stats' OR po.optout_type IS NULL) AND 
				(ActivityDate BETWEEN :start AND :end)
			GROUP BY Pilot ORDER BY cnt DESC LIMIT 10";
		break;
}

echo $sql;
// get database results
// https://dev.evescoutrescue.com/stats/stats_data_esrc_participation-test.php?start=2025-02-27&end=2025-03-06&type=Overall
// https://dev.evescoutrescue.com/stats/stats_data_esrc_participation.php?start=2025-02-27&end=2025-03-06&type=Overall
$timestart = $_REQUEST['start'] . " 00:00:00";
$timeend = $_REQUEST['end'] . " 23:59:59";
echo "<br> " . $timestart;
echo "<br> " . $timeend;
echo "<br> " . DB_HOST;
echo "<br> " . $configPath;

$db->query($sql);
$db->bind (':start', $timestart);
$db->bind (':end', $timeend);
$result = $db->resultset();
$db->closeQuery();

$rows = array();
$table = array();
$table['cols'] = array(
	// Labels for your chart, these represent the column titles.
	/*
	 note that one column is in "string" format and another one is in "number" format
	 as pie chart only required "numbers" for calculating percentage
	 and string will be used for Slice title
	 */
	array('label' => 'Pilot', 'type' => 'string'),
	array('label' => 'Count', 'type' => 'number')
);

/* Extract the information from $result */
foreach($result as $r) {	
	$temp = array();
	
	// the following line will be used to slice the Pie chart
	$temp[] = array('v' => (string) $r['Pilot']);
	
	// Values of each slice
	$temp[] = array('v' => (int) $r['cnt']);
	$rows[] = array('c' => $temp);
}

$table['rows'] = $rows;

// convert data into JSON format
$jsonTable = json_encode($table);
echo $jsonTable;
?>