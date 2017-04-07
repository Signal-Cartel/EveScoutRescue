<?php
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

include_once '../includes/auth-alliance-req.php';
include_once '../class/db.class.php';

$locopts = array('See Notes','Star','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII','XIII','XIV','XV','XVI','XVII','XVIII','XIX','XX');
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php
include_once '../includes/bg.php';
$pgtitle = 'Data Entry';
include_once '../includes/head.php'; 
?>
	<script>
        $(document).ready(function() {
            $('input.system_sower').typeahead({
                name: 'system_sower',
                remote: 'jsystems.php?query=%QUERY',
				minLength: 3, // send AJAX request only after user type in at least 3 characters
				limit: 8 // limit to show only 8 results
            });
			$('input.system_tender').typeahead({
                name: 'system_tender',
                remote: 'activecaches.php?query=%QUERY',
				minLength: 3, // send AJAX request only after user type in at least 3 characters
				limit: 8 // limit to show only 8 results
            });
			$('input.system_adjunct').typeahead({
                name: 'system_adjunct',
                remote: 'activecaches.php?query=%QUERY',
				minLength: 3, // send AJAX request only after user type in at least 3 characters
				limit: 8 // limit to show only 8 results
            });
        })
    </script>
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST")
{ 
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
		
		if (preg_match('/\b[J][0-9]{6}\b/', $system_sower) != 1) { $errmsg = $errmsg . "System must be in the format: J######, where # is any number.\n"; }
		
		if (22000 >= (int)$distance || (int)$distance >= 50000) { $errmsg = $errmsg . "Distance must be a number between 22000 and 50000.\n"; }
	}
	// TENDER entry
	elseif (empty($system_sower) && !empty($system_tender) && empty($system_adjunct)) { // more than one system provided
		$entrytype = 'tender';
		
		if (empty($status)) { $errmsg = $errmsg . "You must indicate the status of the cache you are tending.\n"; }
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
		$db = new Database();
		//begin db transaction
		$db->beginTransaction();
		// insert to 'action' table
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
		$noteDate = '[' . date("Y-M-d", strtotime("now")) . '] ';
		//handle each sort of entrytype
		switch ($entrytype) {
			// SOWER
			case 'sower':
				//1. check to make sure system name entered is a valid wormhole system
				$db->query("SELECT System FROM wh_systems WHERE System = :system");
				$db->bind(':system', $system_sower);
				$row = $db->single();
				if (empty($row)) {
					$errmsg = $errmsg . "Invalid wormhole system name entered. Please correct name and resubmit.";
					$_POST['system_sower'] = '';
					//roll back [activity] table commit
					$db->cancelTransaction();
				}
				else {
					//2. check for duplicates - there can only be one non-expired cache per system
					$db->query("SELECT System FROM cache WHERE System = :system AND Status <> 'Expired'");
					$db->bind(':system', $system_sower);
					$row = $db->single();
					if (!empty($row)) {
						$errmsg = $errmsg . "Duplicate entry detected. Please tend existing cache before entering a new one for this system.";
						//roll back [activity] table commit
						$db->cancelTransaction();
					}
					else {
						//3. system name is valid and not a duplicate, so go ahead and insert
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
						//end db transaction
						$db->endTransaction();
						$successcolor = '#ccffcc';
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
					$db->cancelTransaction();
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
							//end db transaction
							$db->endTransaction();
							break;
						case 'Upkeep Required':
							$db->query("UPDATE cache SET ExpiresOn = :expdate, Status = :status, Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
							$db->bind(':expdate', date("Y-m-d H:i:s", strtotime("+30 days",time())));
							$db->bind(':status', 'Upkeep Required');
							$db->bind(':note', $tender_note);
							$db->bind(':system', $system_tender);
							$db->execute();
							//end db transaction
							$db->endTransaction();
							break;
						case 'Expired':
							$db->query("UPDATE cache SET Status = :status, Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
							$db->bind(':status', 'Expired');
							$db->bind(':note', $tender_note);
							$db->bind(':system', $system_tender);
							$db->execute();
							//end db transaction
							$db->endTransaction();
							//FYI: daily process to update expired caches in [cache] is running via cron-job.org
							break;
					}
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
					$db->cancelTransaction();
				}
				else {
					//2. system name is valid, so go ahead and insert
					$adj_note = '<br />' . $noteDate . 'Adjunct: '. $pilot . '; Aided: ' . $aidedpilot;
					if (!empty($notes)) { $adj_note = $adj_note . '<br />' . $notes; }
					
					$db->query("UPDATE cache SET Note = CONCAT(Note, :note) WHERE System = :system AND Status <> 'Expired'");
					$db->bind(':note', $adj_note);
					$db->bind(':system', $system_adjunct);
					$db->execute();
					//end db transaction
					$db->endTransaction();
					$successcolor = '#fffacd';
				}
				break;
		}
		
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
<div class="container">
	<form name="esrc" id="esrc" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method='post' enctype='multipart/form-data'>
	<div class="ws"></div>
	<div class="row" id="formtop">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Rescue Cache Data Entry</span><br /><br />
			<a href="search.php" class="btn btn-info" role="button" tabindex="100">Go to Search</a>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<div class="row" id="formtop2">
		<div class="col-sm-12" style="text-align: center;">
			<span class="white" style="font-weight: bold;">Complete ONLY ONE of the sections below. Notes may be provided with any type of entry.</span>
		</div>
	</div>
	<div class="ws"></div>
<?php
//display error message div if there is one to show
if (!empty($errmsg)) {
?>
	<div class="row" id="errormessage" style="background-color: #ff9999;">
		<div class="col-sm-12 message">
			<?php echo nl2br($errmsg); ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
}
else {
	//display success message div if there is one to show
	if (!empty($success_url)) {
?>
	<div class="row" id="successmessage" style="background-color: <?php echo $successcolor;?>">
		<div class="col-sm-12 message">
			<?php echo strtoupper($entrytype) . ' record entered successfully! ' . $success_url; ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
	}
}
?>
	<div class="row" id="formmain">
		<div class="col-sm-6">
			<!--SOWER-->
			<div class="sechead sower">SOWER</div>
			<div class="sowerlight">
				<?php
				if (isset($_POST['system_sower'])) { 
					$targetsystemsow = htmlspecialchars($_POST['system_sower']); 
				}
				elseif (isset($_GET['sowsys'])) { 
					$targetsystemsow= htmlspecialchars($_GET['sowsys']); 
				}
				?>
				<div class="form-group">
					<label class="control-label" for="system_sower">System<span class="descr">Must be in format J######, where # is any number.</span></label>
					<input type="text" name="system_sower" size="30" class="system_sower" autocomplete="off" placeholder="J######" value="<?php echo isset($targetsystemsow) ? $targetsystemsow: '' ?>">
				</div>
				
				<div class="field">
					<label class="control-label" for="location">Location<span class="descr">By which celestial is the cache located? If somewhere other than a planet or star, please mention in a note.</span></label>
					<select class="form-control" id="location" name="location">
						<option value="">- Select -</option>
						<?php
						foreach ($locopts as $val) {
							$selectedLoc = '';
							if (isset($_POST['location']) && $_POST['location'] == $val) {
								$selectedLoc = ' selected="selected"';
							}
							echo '<option value="' . $val . '"' . $selectedLoc . '>' . $val . '</option>';
						}
						?>
					</select>
				</div>

				<div class="field">
					<label class="control-label" for="alignedwith">Aligned With<span class="descr">With which celestial is the cache aligned? If somewhere other than a planet or star, please mention in a note.</span></label>
					<select class="form-control" id="alignedwith" name="alignedwith" >
						<?php $strSelectedAW = isset($_POST['alignedwith']) ? ' selected="selected"' : '' ?>
						<option value="">- Select -</option>
						<?php 
						foreach ($locopts as $val) {
							$selectedLoc = '';
							if (isset($_POST['alignedwith']) && $_POST['alignedwith'] == $val) {
								$selectedLoc = ' selected="selected"';
							}
							echo '<option value="' . $val . '"' . $selectedLoc . '>' . $val . '</option>';
						}
						?>
					</select>
				</div>

				<div class="field">
					<label class="control-label" for="f11">Distance (km)<span class="descr">How far is the cache from the Location planet? Must be a number between 22000 and 50000.</span></label>
					<input type="text" class="form-control " id="distance" name="distance" value="<?php echo isset($_POST['distance']) ? htmlspecialchars($_POST['distance']) : '' ?>" type="number" />
				</div>
	
				<div class="field">
					<label class="control-label" for="password">Password<span class="descr">What is the password for the secure container?</span></label>
					<input type="text" class="form-control" id="password" name="password" value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>"  />
				</div>
			</div>
			<!--END SOWER-->
		</div>
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-6">
					<!--TENDER-->
					<div class="sechead tender">TENDER</div>
					<div class="tenderlight">
						<?php
						if (isset($_POST['system_tender'])) { 
							$targetsystemtend = htmlspecialchars($_POST['system_tender']); 
						}
						elseif (isset($_GET['tendsys'])) { 
							$targetsystemtend = htmlspecialchars($_GET['tendsys']); 
						}
						?>
						<div class="form-group">
							<label class="control-label" for="system_tender">System</label>
							<input type="text" name="system_tender" size="30" class="system_tender" id="system_tender" autocomplete="off" placeholder="J######" value="<?php echo isset($targetsystemtend) ? $targetsystemtend : '' ?>">
						</div>
						<div class="field">
							<label class="control-label" for="status">Status</label>
							<?php 
							$checkedH = (isset($_POST['status']) && $_POST['status'] == 'Healthy') ? ' checked="checked"' : ''; 
							$checkedU = (isset($_POST['status']) && $_POST['status'] == 'Upkeep Required') ? ' checked="checked"' : '';
							$checkedE = (isset($_POST['status']) && $_POST['status'] == 'Expired') ? ' checked="checked"' : '';
							?>
							<div class="radio">
								<label for="status_1"><input id="status_1" name="status" type="radio" value="Healthy"<?php echo $checkedH; ?>>Healthy</label>
							</div>
							<div class="radio">
								<label for="status_2"><input id="status_2" name="status" type="radio" value="Upkeep Required"<?php echo $checkedU; ?>>Upkeep Required</label>
							</div>
							<div class="radio">
								<label for="status_3"><input id="status_3" name="status" type="radio" value="Expired"<?php echo $checkedE; ?>>Expired</label>
							</div>
							<p class="descr">What was the condition of the cache when you left it?<br />
								&nbsp;&nbsp;&nbsp;<b>Healthy</b> = Anchored, safe, and full of supplies<br />
								&nbsp;&nbsp;&nbsp;<b>Upkeep Required</b> = Needs maintenance/supplies<br />
								&nbsp;&nbsp;&nbsp;<b>Expired</b> = Could not find or is unusable</p>
						</div>
					</div>
					<!--END TENDER-->
				</div>
				<div class="col-sm-6"><!--ADJUNCT-->
					<div class="sechead adjunct">ADJUNCT</div>
					<div class="adjunctlight">
						<?php
						if (isset($_POST['system_adjunct'])) { 
							$targetsystemadj = htmlspecialchars($_POST['system_adjunct']); 
						}
						elseif (isset($_GET['adjsys'])) { 
							$targetsystemadj = htmlspecialchars($_GET['adjsys']); 
						}
						?>
						<div class="form-group">
							<label class="control-label" for="system_adjunct">System</label>
							<input type="text" name="system_adjunct" size="30" class="system_adjunct" autocomplete="off" placeholder="J######" value="<?php echo (isset($targetsystemadj)) ? $targetsystemadj : '' ?>">
						</div>
						<div class="field">
							<label class="control-label" for="aidedpilot">Aided Pilot<span class="descr">What is the name of the Capsuleer who required rescue?</span></label>
							<input type="text" class="form-control" id="aidedpilot" name="aidedpilot" value="<?php echo isset($_POST['aidedpilot']) ? htmlspecialchars($_POST['aidedpilot']) : '' ?>" />
						</div>
					</div>
					<!--END ADJUNCT-->
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="ws"></div>
					<label class="control-label white" for="notes">Notes<span class="descr white">Is there any other important information we need to know?</span></label>
					<textarea class="form-control" id="notes" name="notes" rows="3"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '' ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="ws"></div>
	<div class="form-actions">
		<input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
	    <button type="submit" class="btn btn-lg">Submit</button>
	</div>
	</form>
</div>
</body>
</html>