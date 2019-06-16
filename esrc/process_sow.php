<?php

/**
 * Separate data entry code from data entry page content. 
 * 
 * This file contains all the logic to check data, update database and prepare error logging
 * 
 */

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../class/db.class.php';
include_once '../class/systems.class.php';
include_once '../class/caches.class.php';
include_once '../class/output.class.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';

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
if (isset($_POST['sys_sow'])) {
	// yes, process the request
	$db = new Database();
	$systems = new Systems($db);
	$caches= new Caches($db);
	
	$pilot = $activitydate= $system = $location = $alignedwith = $distance = '';
	$password = $status = $aidedpilot = $notes = $errmsg = $entrytype = '';
	$noteDate = $successmsg = $success_url = "";
	
	$pilot = test_input($_POST["pilot"]);
	$activitydate = gmdate("Y-m-d H:i:s", strtotime("now"));
	$system = test_input($_POST["sys_sow"]);
	$location = test_input($_POST["location"]);
	$alignedwith = test_input($_POST["alignedwith"]);
	$distance = test_input($_POST["distance"]);
	$password = test_input($_POST["password"]);
	$status = isset($_POST["status"]) ? test_input($_POST["status"]) : 'Healthy';
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
	$entrytype = 'sower';
	
	// use the Systems class to validate the entered system name
	if ($systems->validatename($system) != 0) {
		$errmsg = $errmsg . "Invalid system name entered. Please select a valid system from the list.\n";
	}
	
	// check if another pilot already sowed a cache
	if (!empty($caches->getCacheInfo($system, TRUE)))
	{
		$errmsg = $errmsg . "A cache already exists in this system. Looks like someone beat you to it!\n";
	}
	
	if (empty($location) || empty($alignedwith) || empty($distance) || empty($password)) {
		$errmsg = $errmsg . "All fields in section 'SOWER' must be completed.\n";
	}
	
	if (!empty($location) && !empty($alignedwith) && $location === $alignedwith && $location != 'See Notes') {
		$errmsg = $errmsg . "Location and Aligned With cannot be set to the same value.\n";
	}
	
	if ((int)$distance < 22000 || (int)$distance > 50000) { 
		$errmsg = $errmsg . "Distance (".Output::htmlEncodeString($distance).") must be a number between 22000 and 50000.\n"; 
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = "search.php?sys=". $system ."&errmsg=". urlencode($errmsg)."&pass=". urlencode($password);
	} 
	// otherwise, perform DB UPDATES
	else {

		$sower_note = '';
		
		//prepare note
		if (!empty($notes)) {
			$noteDate = '[' . gmdate("M-d", strtotime("now")) . '] ';
			$sower_note = $noteDate . 'Sown by '. $pilot . ": " . $notes;
		}
		
		//perform [cache] insert
		$newID = $caches->createCache($system, $location, $alignedwith, $distance, $password, $activitydate, $sower_note);

		// create a new cache activity
		$caches->addActivity($newID, $system, $pilot, $entrytype, $activitydate, $notes, $aidedpilot, $status);

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