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

	$pilot = $system = $aidedpilot = $errmsg = $entrytype = $noteDate = '';
	
	echo $_POST["pilot"];

	$pilot = test_input($_POST["pilot"]);
	$system = test_input($_POST["system"]);
	$aidedpilot = test_input($_POST["aidedpilot"]);
	$notes = test_input($_POST["notes"]);

	//FORM VALIDATION
	$entrytype = 'agent';
	if (empty($aidedpilot)) { 
		$errmsg = $errmsg . "You must indicate the name of the capsuleer who 
			required rescue.\n"; 
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		echo $errmsg;
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
		$noteDate = '[' . date("Y-M-d", strtotime("now")) . '] ';
		$adj_note = '<br />' . $noteDate . 'Adjunct: '. $pilot . '; Aided: ' . $aidedpilot;
		if (!empty($notes)) { $adj_note = $adj_note . '<br />' . $notes; }
			
		$db->query("UPDATE cache SET Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
		$db->bind(':note', $adj_note);
		$db->bind(':system', $system_adjunct);
		$db->execute();

		echo 'All good!';
		
	}
	//END DB UPDATES
}
else {
	echo 'Nothing set.';
}

?>