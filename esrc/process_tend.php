<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

/**
 * Separate data entry code from data entry page content. 
 * 
 * This file contains all the logic to check data, update database and prepare error logging
 * 
 */

include_once '../class/db.class.php';
include_once '../class/caches.class.php';
include_once '../class/output.class.php';

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

// check if the request is made by a POST request
if (isset($_POST['sys_tend'])) {
	// yes, process the request
	$db = new Database();
	// create a new cache class
	$caches = new Caches($db);
	
	$activitydate = $pilot = $system = $status = $aidedpilot = ''; 
	$errmsg = $entrytype = $noteDate = '';

	$activitydate = gmdate("Y-m-d H:i:s", strtotime("now"));
	$pilot = test_input($_POST["pilot"]);
	$system = test_input($_POST["sys_tend"]);
	$status= test_input($_POST["status"]);
	$notes = test_input($_POST["notes"]);

	// check the system
	if (isset($system))
	{
		// make the system uppercase
		$system = strtoupper($system);
	}
	else
	{
		$errmsg = $errmsg . "No system name is set.";
	}
	
	//FORM VALIDATION
	$entrytype = 'tender';
	if (empty($status)) { 
		$errmsg = $errmsg . "You must indicate the status of the cache you are tending.\n"; 
	}

	// check if an error is already set
	if (empty($errmsg))
	{
		// no, get cache info
		$cacheInfo = $caches->getCacheInfo($system, TRUE);
		
		// check if a cache exists
		if (empty($cacheInfo))
		{
			// no cache exists
			$errmsg = $errmsg . "Cache is expired in the meantime.\n";
		}
		else if (0 == $caches->isTendingAllowed($system) && $status === 'Healthy')
		{
			$errmsg = $errmsg . "Cache is already tended by another pilot. Try another system please.\n";
		}
		else if (($status === 'Expired' && $cacheInfo['Status'] == 'Expired') || ($status === 'Upkeep Required' && $cacheInfo['Status'] == 'Upkeep Required'))
		{
			// no cache exists
			$errmsg = $errmsg . "Could not set cache to '".Output::htmlEncodeString($status)."' twice.\n";
		}
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = "search.php?sys=". $system ."&errmsg=". urlencode($errmsg);
	} 
	// otherwise, perform DB UPDATES
	else {
		// add new activity
		$newID = $caches->addActivity($system, $pilot, $entrytype, $activitydate, $notes, $aidedpilot);

		//prepare note for update
		$noteDate = '[' . date("M-d", strtotime("now")) . '] ';
		$tender_note = '<br />' . $noteDate . 'Tended by '. $pilot.' as '.$status.'.';
		if (!empty($notes)) { $tender_note = $tender_note. '<br />' . $notes; }		
		//perform [cache] update
		
		$caches->addNoteToCache($system, $tender_note);
		
		switch ($status) {
			case 'Expired':
				$caches->expireCache($system, $activitydate);
				break;
			default:	//'Healthy', 'Upkeep Required'
				$caches->updateExpireTime($system, $status, gmdate("Y-m-d", strtotime("+30 days", time())), $activitydate);
		}
		//redirect back to search page to show updated info
		$redirectURL = "search.php?sys=". $system;
	}
	//END DB UPDATES
	?>
	<script>
		window.location.replace("<?=$redirectURL?>")
	</script>
	<?php 
}

?>