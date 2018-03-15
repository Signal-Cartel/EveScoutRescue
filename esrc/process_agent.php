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
include_once '../class/users.class.php';
include_once '../class/config.class.php';
require_once '../class/rescue.class.php';

// check if the user is alliance member
if (!Users::isAllianceUserSession())
{
	// void the session entries on 'attack'
	session_unset();
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH);
	// stop processing
	exit;
}

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
if (isset($_POST['sys_adj'])) {
	// yes, process the request
	$db = new Database();

	$activitydate = $pilot = $system = $aidedpilot = $errmsg = $entrytype = $noteDate = '';

	$activitydate = gmdate("Y-m-d H:i:s", strtotime("now"));
	$pilot = test_input($_POST["pilot"]);
	$system = test_input($_POST["sys_adj"]);
	$aidedpilot = test_input($_POST["aidedpilot"]);
	$notes = test_input($_POST["notes"]);
	$updateexp = $_POST['updateexp'];

	// check the system
	if (isset($system)) {
		// make the system uppercase
		$system = strtoupper($system);
	}
	else {
		$errmsg = $errmsg . "No system name is set.";
	}
	
	//FORM VALIDATION
	$entrytype = 'agent';
	if (empty($aidedpilot)) { 
		$errmsg = $errmsg . "You must indicate the name of the capsuleer who required rescue."; 
	}
	//END FORM VALIDATION

	//display error message if there is an error
	if (!empty($errmsg)) {
		$redirectURL = "search.php?sys=". $system ."&errmsg=". urlencode($errmsg);
	} 
	// otherwise, perform DB UPDATES
	else {
		// CACHE update
		// create a new instance of Caches class
		$caches = new Caches($db);
		// add a new agent activity
		$caches->addActivity($system, $pilot, $entrytype, $activitydate, $notes, $aidedpilot);
		// add note to cache
		$noteDate = '[' . date("M-d", strtotime("now")) . '] ';
		$agent_note = '<br />' . $noteDate . 'Rescue Agent: '. $pilot . '; Aided: ' . $aidedpilot;
		if (!empty($notes)) { $agent_note = $agent_note. '<br />' . $notes; }
		$caches->addNoteToCache($system, $agent_note);
		// update expiration date if needed
		if (intval($updateexp) == 1) {
			$caches->updateExpireTime($system, 'Upkeep Required', gmdate("Y-m-d", strtotime("+30 days", time())), $activitydate);
		}
		
		// RESCUE update
		// create a new instance of Rescue class
		$rescue = new Rescue($db);
		// add a new Rescue record
		$db->beginTransaction();
		$newRescueID = $rescue->createESRCRequest($system, $aidedpilot, $pilot, 'closed-esrc');
		// insert rescue note if set
		if (isset($notes) && $notes != '') {
			$notes = 'ESRC - ' . $notes;
			$rescue->createRescueNote($newRescueID, $pilot, $notes);
		}
		$db->endTransaction();
		
		// refresh page to display newly entered data
		$redirectURL = "search.php?sys=". $system;
	}
	?>
		<script>
			window.location.replace("<?=$redirectURL?>")
		</script>
	<?php 
	//END DB UPDATES
}

?>