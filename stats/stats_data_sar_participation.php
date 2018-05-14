<?php

include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

switch ($_REQUEST['type']) {
	case 'Dispatchers':
		$sql = "SELECT startagent, COUNT(startagent) AS cnt FROM rescuerequest
			WHERE requestdate BETWEEN :start AND :end
			GROUP BY startagent	ORDER BY cnt DESC LIMIT 10";
		$fieldname = 'startagent';
		break;
	case 'Locators':
		$sql = "SELECT locateagent, COUNT(locateagent) AS cnt FROM rescuerequest
			WHERE lastcontact BETWEEN :start AND :end
			GROUP BY locateagent ORDER BY cnt DESC LIMIT 10";
		$fieldname = 'locateagent';
		break;
	case 'Rescuers':
	default:
		$sql = "SELECT pilot, COUNT(pilot) AS cnt FROM rescueagents
			WHERE entrytime BETWEEN :start AND :end
			GROUP BY pilot ORDER BY cnt DESC LIMIT 10";
		$fieldname = 'pilot';
		break;
}

// get database connection
$db = new Database();
$db->query($sql);
$db->bind (':start', $_REQUEST['start']);
$db->bind (':end', $_REQUEST['end']);
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
	$temp[] = array('v' => (string) $r[$fieldname]);
	
	// Values of each slice
	$temp[] = array('v' => (int) $r['cnt']);
	$rows[] = array('c' => $temp);
}

$table['rows'] = $rows;

// convert data into JSON format
$jsonTable = json_encode($table);
echo $jsonTable;
?>