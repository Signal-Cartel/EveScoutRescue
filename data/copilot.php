<?php
// API for copilot
// once we release, we may want to devise some security on this so the API is not open
// place this file in the 'data' folder and name it copilot.php
// 
// request format then will be
// http://www.evescoutrescue.com/esrc/copilot.php?cache=J123456

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../class/db.class.php';
include_once '../class/systems.class.php';
$cache = strtoupper ( $_REQUEST ["cache"] );

try {
	$db = new Database ();
	
	$systems = new Systems($db);
	
	if ($cache && strlen ( $cache ) == 7) {
		// check for "No Sow" system
		
		if ($systems->validatename($cache) == 0) {
			$db->query ( "SELECT *
				FROM wh_systems
				WHERE System = :system" );
			$db->bind ( ':system', $cache );
			$row = $db->single ();
			// yes, add the system to the result
			$result ['system'] = $row;
			// echo json_encode($row);
		} else {
			throw new Exception ( 'Unknown system: ' . $cache );
		}
		// we can sow here
		// check for presence of a cache in system
		$db->query ( "SELECT c.System, Location, AlignedWith, Status, ExpiresOn,
							InitialSeedDate, LastUpdated
						FROM cache c
						WHERE c.System = :system AND Status <> 'Expired'" );
		$db->bind ( ':system', $cache );
		$row = $db->single ();
		if (count ( $row ) < 1) {
			throw new Exception ( "none" );
		}
		// echo json_encode($row);
		$result ['cache'] = $row;

		$db->query ( "SELECT count(1) as cnt
						FROM rescuerequest
						WHERE system = :system and finished = 0" );
		$db->bind ( ':system', $cache );
		$row = $db->single ();
		// echo json_encode($row);
		$result ['rescue'] = $row['cnt'];
		
	} // $cache not present or wrong format
else {
		throw new Exception ( 'Invalid system: ' . $cache );
	}
} catch ( Exception $e ) {
	$errorMsg = $e->getMessage ();
	if (($errorMsg != "invalid") && ($errorMsg != "none") || ! isset ( $errorMsg )) {
		// echo "error";
		$result ['error'] = $errorMsg;
	} else {
		$result ['error'] = $errorMsg;
		// echo 'Msg: '.$errorMsg;
	}
}

echo json_encode ( $result );
?>