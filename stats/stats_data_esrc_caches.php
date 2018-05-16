<?php

include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

switch ($_REQUEST['type']) {
	case 'Daily':
	default:
		$sql = "SELECT ActivityDate, Sown, Tended, ActiveCaches FROM cache_activity
				WHERE ActivityDate BETWEEN :start AND :end ORDER BY ActivityDate";
		break;
	case 'Weekly':
		$sql = "SELECT YEARWEEK(ActivityDate) AS ActivityDate, SUM(Sown) AS Sown, 
					SUM(Tended) AS Tended, MAX(ActiveCaches) AS ActiveCaches FROM cache_activity	
				WHERE ActivityDate BETWEEN :start AND :end
				GROUP BY YEARWEEK(ActivityDate) ORDER BY YEARWEEK(ActivityDate)";
		break;
	case 'Monthly':
		$sql = 'SELECT CONCAT(YEAR(ActivityDate), "-", MONTHNAME(ActivityDate)) AS ActivityDate, 
					SUM(Sown) AS Sown, SUM(Tended) AS Tended, MAX(ActiveCaches) AS ActiveCaches 
				FROM cache_activity WHERE ActivityDate BETWEEN :start AND :end
				GROUP BY YEAR(ActivityDate), MONTH(ActivityDate) 
				ORDER BY YEAR(ActivityDate), MONTH(ActivityDate)';
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
	array('label' => 'Date', 'type' => 'string'),
	array('label' => 'Sown', 'type' => 'number'),
	array('label' => 'Tended', 'type' => 'number'),
	array('label' => 'Active Caches', 'type' => 'number')
);

/* Extract the information from $result */
foreach($result as $r) {	
	$temp = array();
	// format date strings for display
	switch ($_REQUEST['type']) {
		case 'Daily':
		default:
			$arrDate = explode('-', $r['ActivityDate']);
			$adYear = intval(substr($arrDate[0], -4)) - 1898;
			$adMonth =  date('M', strtotime($r['ActivityDate']));
			$adDay = substr($arrDate[2], 0, 2);
			$adDate = 'YC' . $adYear. '-' . $adMonth. '-' . $adDay;
			break;
		case 'Weekly':
			$adYear = intval(substr($r['ActivityDate'], 0, 4)) - 1898;
			$adWeek = substr($r['ActivityDate'], -2);
			$adDate = 'YC' . $adYear. '-' . $adWeek;
			break;
		case 'Monthly':
			$arrDate = explode('-', $r['ActivityDate']);
			$adYear = intval($arrDate[0]) - 1898;
			$adMonth = $arrDate[1];
			$adDate = 'YC' . $adYear. '-' . $adMonth;
			break;
	}
	
	$temp[] = array('v' => (string) $adDate);
	$temp[] = array('v' => (int) $r['Sown']);
	$temp[] = array('v' => (int) $r['Tended']);
	$temp[] = array('v' => (int) $r['ActiveCaches']);
	$rows[] = array('c' => $temp);
}

$table['rows'] = $rows;

// convert data into JSON format
$jsonTable = json_encode($table);
echo $jsonTable;
?>