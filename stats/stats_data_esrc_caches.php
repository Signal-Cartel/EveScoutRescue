<?php

include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

$sql = "SELECT ActivityDate, Sown, Tended
		FROM   cache_activity
		WHERE  ActivityDate BETWEEN :start AND :end
		ORDER  BY ActivityDate";

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
	array('label' => 'Date', 'type' => 'string'),
	array('label' => 'Sown', 'type' => 'number'),
	array('label' => 'Tended', 'type' => 'number')
);

/* Extract the information from $result */
foreach($result as $r) {	
	$temp = array();
	// format date string for display
	$arrDate = explode('-', $r['ActivityDate']);
	$adYear = intval(substr($arrDate[0], -4)) - 1898;
	$adMonth =  date('M', strtotime($r['ActivityDate']));
	$adDay = substr($arrDate[2], 0, 2);
	$adDate = 'YC' . $adYear. '-' . $adMonth. '-' . $adDay;
	
	$temp[] = array('v' => (string) $adDate);
	$temp[] = array('v' => (int) $r['Sown']);
	$temp[] = array('v' => (int) $r['Tended']);
	$rows[] = array('c' => $temp);
}

$table['rows'] = $rows;

// convert data into JSON format
$jsonTable = json_encode($table);
echo $jsonTable;
?>