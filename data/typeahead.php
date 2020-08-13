<?php
define('ESRC', TRUE);

include_once '../class/db.class.php';

//CREATE QUERY TO DB AND PUT RECEIVED DATA INTO ASSOCIATIVE ARRAY

$defaultlimit = 5;

/**
 * query - parameter should contain a part of the WH system name. It's expanded by the code
 * with wildcards.
 * type - supports the values 'cache' (search active caches) and
 *        'system' (search known systems); this may be extended later if necessary
 *        an unknown type make a fall back to all systems
 */

// do not return anything if querystring is not set
if (isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
	$query = $_REQUEST['query'] == '%' ? '' : '%' . strtoupper($_REQUEST['query']) . '%';
}
else
{
	$query = '%';
}

if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
	$type = $_REQUEST['type'];
}
else
{
	// set system as default type if no type is set
	$type = 'system';
}

switch ($type) {
	case 'system' :
		// search for all systems
		$sql = "SELECT System FROM wh_systems WHERE System LIKE :query ORDER BY System limit :limit";
		break;
	case 'cache' :
		// search for active caches
		$sql = "SELECT System FROM cache WHERE System LIKE :query AND Status <> 'Expired' ORDER BY System limit :limit";
		break;
	case 'freesystem':
		// search for systems without a cache
		$sql = "SELECT System FROM wh_systems WHERE System LIKE :query AND system not in (select system from cache where status <> 'Expired') and (DoNotSowUntil is NULL or DoNotSowUntil is not null and DoNotSowUntil < CURRENT_DATE()) order by system limit :limit";
		break;
	default :
		// default is search for all systems
		$sql = "SELECT System FROM wh_systems WHERE System LIKE :query ORDER BY System limit :limit";
		break;
}
// result data
$result = array();

// get database connection
$db = new Database ();
// set the query string
$db->query ( $sql );
// bind the values
$db->bind ( ':query', $query );
$db->bind ( ':limit', $defaultlimit );
// execute the query and get result
$rows = $db->resultset ();

// copy result data to result array
foreach ( $rows as $value ) {
	$result [] = $value ['System'];
}

// close the db cursor
$db->closeQuery();

// RETURN JSON ARRAY
echo json_encode ( $result );
?>