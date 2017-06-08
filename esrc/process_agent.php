<?php

/**
 * Separate data entry code from data entry page content. 
 * 
 * This file contains all the logic to check data, update database and prepare error logging
 * 
 */

include_once '../class/db.class.php';

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

	//FORM VALIDATION
	$entrytype = 'agent';
	if (empty($aidedpilot)) { 
		$errmsg = $errmsg . "You must indicate the name of the capsuleer who required rescue."; 
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = "search.php?sys=". $system ."&errmsg=". urlencode($errmsg);
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
		$db->query("INSERT INTO activity (ActivityDate, Pilot, EntryType, System, 
						AidedPilot, Note, IP) 
					VALUES (:activitydate, :pilot, :entrytype, :system, :aidedpilot, :note, :ip)");
		$db->bind(':activitydate', $activitydate);
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
		$noteDate = '[' . date("M-d", strtotime("now")) . '] ';
		$agent_note = '<br />' . $noteDate . 'Rescue Agent: '. $pilot . '; Aided: ' . $aidedpilot;
		if (!empty($notes)) { $agent_note = $agent_note. '<br />' . $notes; }
			
		$db->query("UPDATE cache SET Note = CONCAT(Note, :note), LastUpdated = lastupdated 
					WHERE System = :system AND Status <> 'Expired'");
		$db->bind(':note', $agent_note);
// 		$db->bind(':lastupdated', $activitydate);
		$db->bind(':system', $system);
		$db->execute();
		
		$redirectURL = "search.php?sys=". $system;
		?>
		<script>
			window.location.replace("<?=$redirectURL?>")
		</script>
		<?php 

	}
	//END DB UPDATES
}

?>