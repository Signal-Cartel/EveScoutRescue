<?php
include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

// do not return anything if querystring is not set
if (isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
	// make sure someone doesn't try to pass in a '%' to return all records
	$query = $_REQUEST['query'] == '%' ? '' : '%' . $_REQUEST['query'] . '%';
    $array = array();

	$db = new Database();
	// return only active caches, ones that are not expired
	$db->query("SELECT System FROM cache WHERE System LIKE :query AND Status <> 'Expired' ORDER BY System");
	$db->bind(':query', $query);
	$rows = $db->resultset();
	
	foreach ($rows as $value) {
		$array[] = $value['System'];
	}
	
    //RETURN JSON ARRAY
    echo json_encode($array);
}
?>