<?php

/**
 * Separate data entry code from data entry page content. 
 * 
 * This file contains all the logic to check data, update database and prepare error logging
 * 
 */

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}


 /**
  * Test provided input data to be valid.
 * @param unknown $data data to check
 * @return string processed and cleaned data
 */

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars_decode($data);
	return $data;
}


// check if the request is made by a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	// yes, process the request
	$db = new Database();
	$systems = new Systems($db);
	$caches = new Caches($db);

	$pilot = $system_sower = $system_tender = $system_adjunct = $location = $alignedwith = $distance = $password = $status = $aidedpilot = $notes = $errmsg = $entrytype = $noteDate = $successmsg = $success_url = "";

	$pilot = test_input($_POST["pilot"]);
	$system_sower = test_input($_POST["system_sower"]);
	$system_tender = test_input($_POST["system_tender"]);
	$system_adjunct = test_input($_POST["system_adjunct"]);
	$location = test_input($_POST["location"]);
	$alignedwith = test_input($_POST["alignedwith"]);
	$distance = test_input($_POST["distance"]);
	$password = test_input($_POST["password"]);
	$status = isset($_POST["status"]) ? test_input($_POST["status"]) : '';
	$aidedpilot = test_input($_POST["aidedpilot"]);
	$notes = test_input($_POST["notes"]);

	//FORM VALIDATION
	//see what kind of entry this is
	// NO system provided
	if (empty($system_sower) && empty($system_tender) && empty($system_adjunct)) {
		$errmsg = $errmsg . "You must enter or select a system.\n";
	}
	// SOWER entry
	elseif (!empty($system_sower) && empty($system_tender) && empty($system_adjunct)) {
		$entrytype = 'sower';

		if (empty($location) || empty($alignedwith) || empty($distance) || empty($password)) {
			$errmsg = $errmsg . "All fields in section 'SOWER' must be completed.\n";
		}

		if (!empty($location) && !empty($alignedwith) && $location === $alignedwith && $location != 'See Notes') {
			$errmsg = $errmsg . "Location and Aligned With cannot be set to the same value.\n";
		}

		// use the Systems class to validate the entered system name
		if ($systems->validatename($system_sower) != 0) { $errmsg = $errmsg . "Invalid system name entered. Please select a valid system from the list.\n"; }

		if (22000 >= (int)$distance || (int)$distance >= 50000) { $errmsg = $errmsg . "Distance must be a number between 22000 and 50000.\n"; }
	}
	// TENDER entry
	elseif (empty($system_sower) && !empty($system_tender) && empty($system_adjunct)) { // more than one system provided
		$entrytype = 'tender';

		if (empty($status)) { $errmsg = $errmsg . "You must indicate the status of the cache you are tending.\n"; }

		if (!$caches->isTendingAllowed($system_tender)) { $errmsg = $errmsg . "You may not tend a cache that has been sown or tended within the last 24 hours. Last Updated: ".$caches->getCacheInfo($system_tender)['LastUpdated']."\n"; };
	}
	// ADJUNCT entry
	elseif (empty($system_sower) && empty($system_tender) && !empty($system_adjunct)) { // more than one system provided
		$entrytype = 'adjunct';

		if (empty($aidedpilot)) { $errmsg = $errmsg . "You must indicate the name of the capsuleer who required rescue.\n"; }
	}
	// more than one system provided
	else {
		$errmsg = $errmsg . "You must enter or select only one system.\n";
	}
	//END FORM VALIDATION

	//DB UPDATES
	if (empty($errmsg)) {
		// make the system ID uppercase
		${"system_$entrytype"} = strtoupper(${"system_$entrytype"});

		//begin db transaction
		$db->beginTransaction();
		// insert to [activity] table
		$db->query("INSERT INTO activity (Pilot, EntryType, System, AidedPilot, Note, IP) VALUES (:pilot, :entrytype, :system, :aidedpilot, :note, :ip)");
		$db->bind(':pilot', $pilot);
		$db->bind(':entrytype', $entrytype);
		$db->bind(':system', ${"system_$entrytype"});
		$db->bind(':aidedpilot', $aidedpilot);
		$db->bind(':note', $notes);
		$db->bind(':ip', $_SERVER['REMOTE_ADDR']);
		$db->execute();
		//get ID from newly inserted [activity] record to use in [cache] record insert/update below
		$newID = $db->lastInsertId();
		//end db transaction
		$db->endTransaction();
		$noteDate = '[' . date("Y-M-d", strtotime("now")) . '] ';
		$sqlRollback = "DELETE FROM activity WHERE ID = " . $newID; // in case we need to roll this back
		//handle each sort of entrytype
		switch ($entrytype) {
			// SOWER
			case 'sower':
				//1. check to make sure system name entered is a valid wormhole system
				if ($systems->validatename($system_sower)) {
					$errmsg = $errmsg . "Invalid wormhole system name entered. Please correct name and resubmit.";
					$_POST['system_sower'] = '';
					//roll back [activity] table commit
					$db->query($sqlRollback);
					$db->execute();
				}
				else {
					//2. check for duplicates - there can only be one non-expired cache per system
					$db->query("SELECT System FROM cache WHERE System = :system AND Status <> 'Expired'");
					$db->bind(':system', $system_sower);
					$row = $db->single();
					if (!empty($row)) {
						$errmsg = $errmsg . "Duplicate entry detected. Please tend existing cache before entering a new one for this system.";
						//roll back [activity] table commit
						$db->query($sqlRollback);
						$db->execute();
					}
					else {
						//3. check for "Do Not Sow" systems
						//   - when wormhole residents ask us not to sow caches in their
						//     holes, we agree to suspend doing so for three months
						$lockDate = $systems->locked($system_sower);
						if (isset($lockDate)) {
							$dateNoSow = date("Y-M-d", strtotime($lockDate));
							$errmsg = $errmsg . "Upon request of the current wormhole residents, caches are not to be sown in this system until ".$dateNoSow;
							//roll back [activity] table commit
							$db->query($sqlRollback);
							$db->execute();
						}
						else {
							//4. system name is valid and not a duplicate, so go ahead and insert
							$sower_note = $noteDate . 'Sown by '. $pilot;
							if (!empty($notes)) { $sower_note = $sower_note . '<br />' . $notes; }
								
							$db->query("INSERT INTO cache (CacheID, InitialSeedDate, System, Location, AlignedWith, Distance, Password, Status, ExpiresOn, Note) VALUES (:cacheid, :sowdate, :system, :location, :aw, :distance, :pw, :status, :expdate, :note)");
							$db->bind(':cacheid', $newID);
							$db->bind(':sowdate', date("Y-m-d H:i:s", strtotime("now")));
							$db->bind(':system', $system_sower);
							$db->bind(':location', $location);
							$db->bind(':aw', $alignedwith);
							$db->bind(':distance', $distance);
							$db->bind(':pw', $password);
							$db->bind(':status', 'Healthy');
							$db->bind(':expdate', date("Y-m-d H:i:s", strtotime("+30 days",time())));
							$db->bind(':note', $sower_note);
							$db->execute();
							//for user feedback message
							$successcolor = '#ccffcc';
						}
					}
				}
				break;
					
				// TENDER
			case 'tender':
				//1. check to make sure system name entered is an eligible wormhole system - one with an active (non-expired) cache
				$db->query("SELECT System FROM cache WHERE System = :system AND Status <> 'Expired'");
				$db->bind(':system', $system_tender);
				$row = $db->single();
				if (empty($row)) {
					$errmsg = $errmsg . "Invalid wormhole system name entered. Please correct name and resubmit.";
					$_POST['system_tender'] = '';
					//roll back [activity] table commit
					$db->query($sqlRollback);
					$db->execute();
				}
				else {
					//2. system name is valid, so go ahead and insert
					$tender_note = '<br />' . $noteDate . 'Tended by '. $pilot;
					if (!empty($notes)) { $tender_note = $tender_note . '<br />' . $notes; }
					//handle each tender option
					switch ($status) {
						case 'Healthy':
							$db->query("UPDATE cache SET ExpiresOn = :expdate, Status = :status, Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
							$db->bind(':expdate', date("Y-m-d H:i:s", strtotime("+30 days",time())));
							$db->bind(':status', 'Healthy');
							$db->bind(':note', $tender_note);
							$db->bind(':system', $system_tender);
							$db->execute();
							break;
						case 'Upkeep Required':
							$db->query("UPDATE cache SET ExpiresOn = :expdate, Status = :status, Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
							$db->bind(':expdate', date("Y-m-d H:i:s", strtotime("+30 days",time())));
							$db->bind(':status', 'Upkeep Required');
							$db->bind(':note', $tender_note);
							$db->bind(':system', $system_tender);
							$db->execute();
							break;
						case 'Expired':
							//FYI: daily process to update expired caches in [cache] is running via cron-job.org
							$db->query("UPDATE cache SET Status = :status, Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
							$db->bind(':status', 'Expired');
							$db->bind(':note', $tender_note);
							$db->bind(':system', $system_tender);
							$db->execute();
							break;
					}
					//for user feedback message
					$successcolor = '#d1dffa';
				}
				break;
					
				// ADJUNCT
			case 'adjunct':
				//1. check to make sure system name entered is an eligible wormhole system - one with an active (non-expired) cache
				$db->query("SELECT System FROM cache WHERE System = :system AND Status <> 'Expired'");
				$db->bind(':system', $system_adjunct);
				$row = $db->single();
				if (empty($row)) {
					$errmsg = $errmsg . "Invalid wormhole system name entered. Please correct name and resubmit.";
					$_POST['system_adjunct'] = '';
					//roll back [activity] table commit
					$db->query($sqlRollback);
					$db->execute();
				}
				else {
					//2. system name is valid, so go ahead and insert
					$adj_note = '<br />' . $noteDate . 'Adjunct: '. $pilot . '; Aided: ' . $aidedpilot;
					if (!empty($notes)) { $adj_note = $adj_note . '<br />' . $notes; }
						
					$db->query("UPDATE cache SET Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
					$db->bind(':note', $adj_note);
					$db->bind(':system', $system_adjunct);
					$db->execute();
					//for user feedback message
					$successcolor = '#fffacd';
				}
				break;
		} //switch ($entrytype)

		//all good, so prepare success message(s) and clear previously submitted form values
		if (empty($errmsg)) {
			if (isset(${"system_$entrytype"})) {
				$success_url = '<a href="search.php?system='. ${"system_$entrytype"} .'">Confirm data entry</a> or <a href="'. htmlspecialchars($_SERVER['PHP_SELF']) .'">enter another one.</a>';
			}
			else {
				$success_url = '<a href="'. htmlspecialchars($_SERVER['PHP_SELF']) .'">Enter another one.</a>';
			}
			//clear POST values from previous form submission
			$_POST = array();
		}
	}
	//END DB UPDATES
}


?>