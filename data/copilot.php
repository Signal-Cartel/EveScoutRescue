<?php
// API for copilot
// once we release, we may want to devise some security on this so the API is not open
// place this file in the 'data' folder and name it copilot.php
// 
// request format then will be
// http://www.evescoutrescue.com/data/copilot.php?cache=J123456

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../class/db.class.php';
include_once '../class/systems.class.php';
include_once '../class/caches.class.php';
include_once '../class/rescue.class.php';

try {
	$cache = strtoupper ( $_REQUEST ["cache"] );
	
	session_start();
	
	if (session_status() != PHP_SESSION_ACTIVE || !isset($_SESSION['auth_copilot']))
	{
		$result['authorized'] = "Invalid session. Disabled or not allowed for copilot";
//  		throw new Exception ( "Invalid session" );
	}
	else 
	{
		$result['authorized'] = 1;
	}
	
	$db = new Database ();
	
	$systems = new Systems($db);
	$caches = new Caches($db);
	$rescue = new Rescue($db);
	
	if ($cache && strlen ( $cache ) == 7) {
		// check for "No Sow" system
		
		if ($systems->validatename($cache) == 0) {
			$row = $systems->getWHInfo($cache);
			// yes, add the system to the result
			if (isset($row['Notes'])) {
				$row['Notes'] = utf8_encode($row['Notes']);
			}
			$result ['system'] = $row;
		} else {
			throw new Exception ( 'Unknown system: ' . $cache );
		}
		
		// we can sow here
		// check for presence of a cache in system
		$row = $caches->getCacheInfo($cache, TRUE);
		$result ['cache'] = $row;
		
		// report only open SAR requests
		$requests = $rescue->getSystemRequests($cache, 0);
		$result ['rescue'] = count($requests);

		// check if tending is allowed for cache		
		$result['tending'] = $caches->isTendingAllowed($cache);
	} // $cache not present or wrong format
	else
	{
		throw new Exception ( 'Invalid system: ' . $cache );
	}
} catch ( Exception $e ) {
    // map an error message to a response field
    $errorMsg = $e->getMessage ();
    $result ['error'] = $errorMsg;
}

echo json_encode ( $result );
?>