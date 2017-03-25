<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/class/class.db.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY
if (isset($_REQUEST['query'])) {
    $query = $_REQUEST['query'];
    $array = array();

	$db = new Db();
	// make available to view only caches that are not expired
	$options = $db -> select("SELECT System FROM cache WHERE System LIKE '%{$query}%' AND Status <> 'Expired' ORDER BY System");
	foreach ($options as $value1) {
		foreach ($value1 as $value2) {
		  $array[] = $value2;
		}
	}
    //RETURN JSON ARRAY
    echo json_encode ($array);
}
?>