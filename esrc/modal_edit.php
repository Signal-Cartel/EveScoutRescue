<!-- Edit Cache Modal Form -->
<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
if (!defined('ESRC')) define('ESRC', TRUE);

include_once '../class/db.class.php';
include_once '../class/caches.class.php';

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
if (isset($_POST['sys_edit'])) {
	// yes, process the request
	$db = new Database();
	$caches = new Caches($db);
	
	$editPilot = $editActivitydate = $editCacheid = $editLocation = $editAlignedwith = $editDistance = '';
	$editPassword = $editNewNote = $noteDate = $editSystem = "";
    
    $editCacheid = $_POST["cacheid"];
    $editPilot = test_input($_POST["pilot"]);
    $editActivitydate = gmdate("Y-m-d H:i:s", strtotime("now"));
    $editSystem = $_POST["sys_edit"];
	$editLocation = test_input($_POST["location"]);
	$editAlignedwith = test_input($_POST["alignedwith"]);
	$editDistance = test_input($_POST["distance"]);
	$editPassword = test_input($_POST["password"]);
	$editNewNote = test_input($_POST["newNote"]);
	
	//FORM VALIDATION
	if (empty($editLocation) || empty($editAlignedwith) || empty($editDistance) || empty($editPassword)) {
		$errmsg = $errmsg . "All fields in section 'SOWER' must be completed.\n";
	}
	
	if (!empty($editLocation) && !empty($editAlignedwith) && $editLocation === $editAlignedwith && $editLocation != 'See Notes') {
		$errmsg = $errmsg . "Location and Aligned With cannot be set to the same value.\n";
	}
	
	if ((int)$editDistance < 22000 || (int)$editDistance > 50000) { 
		$errmsg = $errmsg . "Distance (".Output::htmlEncodeString($editDistance).") must be a number between 22000 and 50000.\n"; 
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = "search.php?sys=". $editSystem ."&errmsg=". urlencode($errmsg);
	} 
	// otherwise, perform DB UPDATES
	else {
		// edit existing cache
		$caches->updateCache($editCacheid, $editLocation, $editAlignedwith, $editDistance, $editPassword);

        //prepare note
		$noteDate = '[' . gmdate("M-d", strtotime("now")) . '] ';
		$edit_note = '<br />' . $noteDate . 'Cache info updated by '. $editPilot;
		if (!empty($editNewNote)) { $edit_note = $edit_note. "\n" . $editNewNote; }
        $caches->addNoteToCache($editCacheid, $edit_note);

		//redirect back to search page to show updated info
		$redirectURL = "search.php?sys=". $editSystem;
	}
	//END DB UPDATES
	?>
		<script>
			window.location.replace("<?=$redirectURL?>")
		</script>
		<?php 
} // end form POST processing

// array for Location and AlignedWith select lists
$locopts = $systems_top->getSowLocations($sysNoteRow['PlanetCount']);
?>

<div id="EditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sower">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Edit Cache Info</h4>
      </div>
      <form name="editform" id="editform" action="modal_edit.php" method="POST">
	      <div class="modal-body black">
			<div class="form-group">
				<label class="control-label" for="sys_edit">System: </label>
				<input type="hidden" name="sys_edit" value="<?php echo $system ?>" />
				<span class="sechead"><?php echo $system ?></span>
			</div>

            <div class="field form-group">
                <label class="control-label" for="location">Location<span class="descr">By which celestial is the 
                    cache located? If somewhere other than a planet or star, please mention in a note.</span></label>
                <select class="form-control" id="location" name="location" required>
                    <option value="">- Select -</option>
                    <?php
                    foreach ($locopts as $val) {
                        $strSelected = '';
                        if ($val == $row['Location']) { $strSelected = ' selected="selected"'; }
                        echo '<option value="' . $val . '"'. $strSelected .'>' . $val . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="field form-group">
                <label class="control-label" for="alignedwith">Aligned With<span class="descr">With which celestial is 
                    the cache aligned? If somewhere other than a planet or star, please mention in a note.</span></label>
                <select class="form-control" id="alignedwith" name="alignedwith" required>
                    <option value="">- Select -</option>
                    <?php 
                    foreach ($locopts as $val) {
                        $strSelected = '';
                        if ($val == $row['AlignedWith']) { $strSelected = ' selected="selected"'; }
                        echo '<option value="' . $val . '"'. $strSelected .'>' . $val . '</option>';
                    }
                    ?>
                </select>
            </div>

			<div class="field form-group">
				<label class="control-label" for="distance">Distance (km)<span class="descr">How far is the cache from 
                    the Location planet? Must be a number between 22000 and 50000.</span></label>
				<input class="form-control " id="distance" name="distance" type="number" value="<?=$row['Distance']?>"
                    min="22000" max="50000" step="1" required/>
			</div>

			<div class="field form-group">
				<label class="control-label" for="password">Password<span class="descr">What is the 
					password for the secure container? (Generated password is pre-filled. Click to 
					paste your own password.)</span></label>
				<input type="text" class="form-control" id="password" name="password" 
					value="<?=$row['Password']?>" maxlength="15" onclick="select();" required />
			</div>
		  	<div class="field form-group">
				<label class="control-label" for="notes">Notes<span class="descr">Will be appended to existing notes.</span></label>
				<textarea class="form-control" id="newNote" name="newNote" rows="3"></textarea>
			</div>
	      </div>
	      <div class="modal-footer">
	        <div class="form-actions">
				<input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
                <input type="hidden" name="cacheid" value="<?=$row['CacheID']?>" />
			    <button type="submit" class="btn btn-info">Submit</button>
			</div>
	      </div>   
      </form>
    </div>

  </div>
</div>

<script>
  $( document ).ready(function() {
    $("#editform").validator();
  });
</script>