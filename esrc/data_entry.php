<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);


include_once '../includes/auth-inc.php';
include_once '../class/db.class.php';
include_once '../class/systems.class.php';
include_once '../class/caches.class.php';

$locopts = array('See Notes','Star','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII','XIII','XIV','XV','XVI','XVII','XVIII','XIX','XX');
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php
$pgtitle = 'Data Entry';
include_once '../includes/head.php'; 
?>
	<script>
        $(document).ready(function() {
            $('input.system_sower').typeahead({
                name: 'system_sower',
                remote: '../data/typeahead.php?type=freesystem&query=%QUERY',
				minLength: 3, // send AJAX request only after user type in at least 3 characters
				limit: 8 // limit to show only 8 results
			});
			$('input.system_tender').typeahead({
                name: 'system_tender',
                remote: '../data/typeahead.php?type=cache&query=%QUERY',
				minLength: 3, // send AJAX request only after user type in at least 3 characters
				limit: 8 // limit to show only 8 results
			});
			$('input.system_adjunct').typeahead({
                name: 'system_adjunct',
                remote: '../data/typeahead.php?type=cache&query=%QUERY',
				minLength: 3, // send AJAX request only after user type in at least 3 characters
				limit: 8 // limit to show only 8 results
			});
			
			// initialize field order preference
			LocOnTop = true; //HTML loads with Location field on top - the default
			//see if a preference is stored
			if (typeof(Storage) !== "undefined"){
				//stored as string only
				if (localStorage.LocOnTop) {
					LocOnTop = (localStorage.LocOnTop == 'true');
				}
			}
			// if we returned a false value, swap the fields
			if (!LocOnTop){
				var tophtm = document.getElementById("topfield").innerHTML;
				var bottomhtm = document.getElementById("bottomfield").innerHTML;
				document.getElementById("topfield").innerHTML = bottomhtm;
				document.getElementById("bottomfield").innerHTML = tophtm;
			}
		})
	</script>
</head>
<body>
<?php
// data and request processing code is moved to a separate file
include_once 'data_entry_process.php';
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
					$targetsystemsow = htmlspecialchars_decode($_POST['system_sower']); 
				}
				elseif (isset($_GET['sowsys'])) { 
					$targetsystemsow= htmlspecialchars_decode($_GET['sowsys']); 
				}
				?>
				<div class="form-group">
					<label class="control-label" for="system_sower">System<span class="descr">Must be in format J######, where # is any number.</span></label>
					<input type="text" name="system_sower" size="30" class="system_sower" autocomplete="off" placeholder="J######" value="<?php echo isset($targetsystemsow) ? $targetsystemsow: '' ?>">
				</div>

				<!--This is the beginning of the swap fields------------------------------------------------------>
				<div id="topfield">
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
				</div><!-- End topfield -->
				<div class="pull-right">					
					<a class="btn btn-success btn-xs" tabindex="99" role="button"href="javascript: swapThem();">Location&lt;-&gt;Align</a>
				</div>
				<div id="bottomfield" >
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
				</div><!-- end bottomfield -->
				
				<script>
				function swapThem(){
					var tophtm = document.getElementById("topfield").innerHTML;
					var bottomhtm = document.getElementById("bottomfield").innerHTML;
					document.getElementById("topfield").innerHTML = bottomhtm;
					document.getElementById("bottomfield").innerHTML = tophtm;
					LocOnTop = !LocOnTop;
					if (typeof(Storage) !== "undefined"){
						localStorage.LocOnTop = LocOnTop;
					}	
				}
				</script>
				<!--This is the end of the swap fields------------------------------------------------------>

				<div class="field">
					<label class="control-label" for="f11">Distance (km)<span class="descr">How far is the cache from the Location planet? Must be a number between 22000 and 50000.</span></label>
					<input type="text" class="form-control " id="distance" name="distance" value="<?php echo isset($_POST['distance']) ? Output::htmlEncodeString($_POST['distance']) : '' ?>" type="number" />
				</div>
	
				<div class="field">
					<label class="control-label" for="password">Password<span class="descr">What is the password for the secure container?</span></label>
					<input type="text" class="form-control" id="password" name="password" value="<?php echo isset($_POST['password']) ? Output::htmlEncodeString($_POST['password']) : '' ?>"  />
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
							$targetsystemtend = Output::htmlEncodeString($_POST['system_tender']); 
						}
						elseif (isset($_GET['tendsys'])) { 
							$targetsystemtend = Output::htmlEncodeString($_GET['tendsys']); 
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
								<b>Healthy</b> = Anchored, safe, and full of supplies<br />
								<b>Upkeep Required</b> = Needs re-supplied<br />
								<b>Expired</b> = Could not find or is unusable</p>
						</div>
					</div>
					<!--END TENDER-->
				</div>
				<div class="col-sm-6"><!--ADJUNCT-->
					<div class="sechead adjunct">ADJUNCT</div>
					<div class="adjunctlight">
						<?php
						if (isset($_POST['system_adjunct'])) { 
							$targetsystemadj = Output::htmlEncodeString($_POST['system_adjunct']); 
						}
						elseif (isset($_GET['adjsys'])) { 
							$targetsystemadj = Output::htmlEncodeString($_GET['adjsys']); 
						}
						?>
						<div class="form-group">
							<label class="control-label" for="system_adjunct">System</label>
							<input type="text" name="system_adjunct" size="30" class="system_adjunct" autocomplete="off" placeholder="J######" value="<?php echo (isset($targetsystemadj)) ? $targetsystemadj : '' ?>">
						</div>
						<div class="field">
							<label class="control-label" for="aidedpilot">Aided Pilot<span class="descr">What is the name of the Capsuleer who required rescue?</span></label>
							<input type="text" class="form-control" id="aidedpilot" name="aidedpilot" value="<?php echo isset($_POST['aidedpilot']) ? Output::htmlEncodeString($_POST['aidedpilot']) : '' ?>" />
						</div>
					</div>
					<!--END ADJUNCT-->
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="ws"></div>
					<label class="control-label white" for="notes">Notes<span class="descr white">Is there any other important information we need to know?</span></label>
					<textarea class="form-control" id="notes" name="notes" rows="3"><?php echo isset($_POST['notes']) ? Output::htmlEncodeString($_POST['notes']) : '' ?></textarea>
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