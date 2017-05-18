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
	
	$pilot = $system = $location = $alignedwith = $distance = '';
	$password = $status = $aidedpilot = $notes = $errmsg = $entrytype = '';
	$noteDate = $successmsg = $success_url = "";
	
	$pilot = test_input($_POST["pilot"]);
	$system = test_input($_POST["sys_sow"]);
	$location = test_input($_POST["location"]);
	$alignedwith = test_input($_POST["alignedwith"]);
	$distance = test_input($_POST["distance"]);
	$password = test_input($_POST["password"]);
	$status = isset($_POST["status"]) ? test_input($_POST["status"]) : '';
	$notes = test_input($_POST["notes"]);

	//FORM VALIDATION
	$entrytype = 'sower';
	
	if (empty($location) || empty($alignedwith) || empty($distance) || empty($password)) {
		$errmsg = $errmsg . "All fields in section 'SOWER' must be completed.\n";
	}
	
	if (!empty($location) && !empty($alignedwith) && $location === $alignedwith && $location != 'See Notes') {
		$errmsg = $errmsg . "Location and Aligned With cannot be set to the same value.\n";
	}
	
	// use the Systems class to validate the entered system name
	if ($systems->validatename($system) != 0) { 
		$errmsg = $errmsg . "Invalid system name entered. Please select a valid system from the list.\n"; 
	}
	
	if (22000 >= (int)$distance || (int)$distance >= 50000) { 
		$errmsg = $errmsg . "Distance must be a number between 22000 and 50000.\n"; 
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = "search.php?system=". $system ."&errmsg=". urlencode($errmsg);
		?>
		<script>
			window.location.replace("<?=$redirectURL?>")
		</script>
		<?php 
	} 
	
	// otherwise, perform DB UPDATES
	else {
		// make the system ID uppercase
		$system = strtoupper($system);

		//begin db transaction
		$db->beginTransaction();
		// insert to [activity] table
		$db->query("INSERT INTO activity (Pilot, EntryType, System, AidedPilot, Note, IP) VALUES (:pilot, :entrytype, :system, :aidedpilot, :note, :ip)");
		$db->bind(':pilot', $pilot);
		$db->bind(':entrytype', $entrytype);
		$db->bind(':system', $system);
		$db->bind(':aidedpilot', $aidedpilot);
		$db->bind(':note', $notes);
		$db->bind(':ip', $_SERVER['REMOTE_ADDR']);
		$db->execute();
		//get ID from newly inserted [activity] record to use in [cache] record insert/update below
		$newID = $db->lastInsertId();
		//end db transaction
		$db->endTransaction();
		//prepare note
		$noteDate = '[' . date("Y-M-d", strtotime("now")) . '] ';
		$sower_note = $noteDate . 'Sown by '. $pilot;
		if (!empty($notes)) { $sower_note = $sower_note . "\n" . $notes; }
		//perform [cache] insert
		$db->query("INSERT INTO cache (CacheID, InitialSeedDate, System, Location, AlignedWith, Distance, Password, Status, ExpiresOn, Note) VALUES (:cacheid, :sowdate, :system, :location, :aw, :distance, :pw, :status, :expdate, :note)");
		$db->bind(':cacheid', $newID);
		$db->bind(':sowdate', date("Y-m-d H:i:s", strtotime("now")));
		$db->bind(':system', $system);
		$db->bind(':location', $location);
		$db->bind(':aw', $alignedwith);
		$db->bind(':distance', $distance);
		$db->bind(':pw', $password);
		$db->bind(':status', 'Healthy');
		$db->bind(':expdate', date("Y-m-d H:i:s", strtotime("+30 days",time())));
		$db->bind(':note', $sower_note);
		$db->execute();
		//redirect back to search page to show updated info
		$redirectURL = "search.php?system=". $system;
		?>
		<script>
			window.location.replace("<?=$redirectURL?>")
		</script>
		<?php 
	}
	//END DB UPDATES
}

?>