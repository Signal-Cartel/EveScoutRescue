<?php

include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

// do not return anything if querystring is not set
if (isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
    $query = '%' . $_REQUEST['query'] . '%';
    $array = array();

	$db = new Database();
	$db->query("SELECT System FROM wh_systems WHERE System LIKE :query ORDER BY System");
	$db->bind(':query', $query);
	$rows = $db->resultset();
	
	foreach ($rows as $value) {
		$array[] = $value['System'];
	}
	
    //RETURN JSON ARRAY
    echo json_encode ($array);
}
?>