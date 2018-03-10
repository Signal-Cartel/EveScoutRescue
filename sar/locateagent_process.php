<?php
// What kind of security can we put in place to make sure this page is only called by Allison?

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../class/db.class.php';
require_once '../class/rescue.class.php';

/**
 * Test provided input data to be valid.
 * @param unknown $data data to check
 * @return string processed and cleaned data
 */
function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars_decode($data);
	return $data;
}


// check if the request has required parameters
if (isset($_REQUEST['pilot']) && isset($_REQUEST['sys'])) {
	// create object instances
	$db = new Database();
	$rescue = new Rescue($db);
	
	$pilot = $system = '';
	$pilot = test_input($_REQUEST["pilot"]);
	$system = strtoupper(test_input($_REQUEST["sys"]));
	
	// get record(s) for active SAR requests in reported system
	// should almost always be only one request, but handling for multiple just in case
	$arrOpenSARs = $rescue->getSystemRequests($system);
	
	// update locateagent and status for open SAR requests in the given system
	foreach($arrOpenSARs as $val) {
		$db->beginTransaction();
		$rescue->setLocateAgent($val['id'], $pilot);
		$db->endTransaction();
		echo 'Request #'. $val['id'] .': locateagent set to '. $pilot;
	}
}
else {
	echo 'Nope.';
}

?>