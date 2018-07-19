<?php

include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

$defaultlimit = 5;

// do not return anything if querystring is not set
if (isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
	$query = $_REQUEST['query'] == '%' ? '' : '%' . strtoupper($_REQUEST['query']) . '%';
}
else {
	$query = '%';
}

// result data
$result = array();

// get database connection
$db = new Database ();
// set the query string
$db->query ("SELECT character_name FROM user WHERE character_name LIKE :query ORDER BY character_name LIMIT :limit");
// bind the values
$db->bind ( ':query', $query );
$db->bind ( ':limit', $defaultlimit );
// execute the query and get result
$rows = $db->resultset ();

// copy result data to result array
foreach ( $rows as $value ) {
	$result [] = $value ['character_name'];
}

// close the db cursor
$db->closeQuery();

// RETURN JSON ARRAY
echo json_encode ( $result );
?>