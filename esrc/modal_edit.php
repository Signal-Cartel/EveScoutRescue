<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
// Edit Cache Modal Form 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
if (!defined('ESRC')) define('ESRC', TRUE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
	//$data = stripslashes($data);
	$data = htmlspecialchars($data);
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
	$editNewNote = trim($_POST["newNote"]);
	$edithasfil = !empty($_POST['hasfil']) ? intval($_POST['hasfil']) : 0; // used filament
	
	//FORM VALIDATION
	if (empty($editLocation) || empty($editAlignedwith) || empty($editDistance) || empty($editPassword)) {
		$errmsg = $errmsg . "All fields in section 'SOWER' must be completed.\n";
	}
	
	if (!empty($editLocation) && !empty($editAlignedwith) && $editAlignedwith != 'Unaligned' && $editLocation === $editAlignedwith && $editLocation != 'See Notes') {
		$errmsg = $errmsg . "Location and Aligned With cannot be set to the same value.\n";
	}
	
	if ($editDistance != 'Unaligned' and ((int)$editDistance < 22000 || (int)$editDistance > 50000)) { 
		$errmsg = $errmsg . "Operation failed. Distance must be a number between 22000 and 50000.\n"; 
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = "search.php?sys=". $editSystem ."&errmsg=". urlencode($errmsg);
	} 
	// otherwise, perform DB UPDATES
	else {
		// edit existing cache
		echo "<!-- edit existing cache -->";
		$caches->updateCacheNew($editCacheid, $editLocation, $editAlignedwith, $editDistance, $editPassword, $edithasfil);

        //note to update

			$caches->addNoteToCache($editCacheid, $editNewNote);
		echo "<!--cacheid " . $editCacheid . " -->";	
		echo "<!-- new note contains " . $editNewNote . " -->";
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
?>

<div id="EditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sower">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Cache Info</h4>
      </div>
      <form name="editform" id="editform" action="modal_edit.php" method="POST">
	      <div class="modal-body black">
			<div class="form-group">
				<label class="control-label" for="sys_edit" style="font-size: 1.4em;"><?php echo $system ?></label>
				<input type="hidden" name="sys_edit" value="<?php echo $system ?>" />
				
			</div>

            <div class="field form-group">
                <label class="control-label" for="location">Location planet<span class="descr">If somewhere other than a planet or star, please mention in a note.</span></label>
                <select class="form-control" id="location" name="location" required>
                    <option value="">- Select -</option>
                    <?php
					// array for Location and AlignedWith select lists
					$locopts = $systems_top->getSowLocations($sysNoteRow['PlanetCount']);
                    foreach ($locopts as $val) {
                        $strSelected = '';
                        if (isset($row['Location']) && $val == $row['Location']) { $strSelected = ' selected="selected"'; }
                        echo '<option value="' . $val . '"'. $strSelected .'>' . $val . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="field form-group">
                <label class="control-label" for="alignedwith">Align planet<span class="descr">If somewhere other than a planet or star, please mention in a note.</span></label>
                <select class="form-control" id="alignedwith" name="alignedwith" required>
                    <option value="">- Select -</option>
                    <?php 
                    foreach ($locopts as $val) {
                        $strSelected = '';
                        if (isset($row['Location']) && $val == $row['AlignedWith']) { $strSelected = ' selected="selected"'; }
                        echo '<option value="' . $val . '"'. $strSelected .'>' . $val . '</option>';
                    }
                    ?>
                </select>
            </div>

			<div class="field form-group">
				<label class="control-label" for="distance">Distance (km) from location planet<span class="descr">Between 22000 and 50000.</span></label>
				<input class="form-control " id="distance" name="distance" value="<?=$row['Distance']?>" required/>
			</div>

			<div class="field form-group">
				<label class="control-label" for="password">Password<span class="descr">(Generated password is pre-filled. Click to 
					paste your own password.)</span></label>
				<input type="text" class="form-control" id="password" name="password" 
					value="<?=$row['Password']?>" maxlength="15" onclick="select();" required />
			</div>
			
			<?php			
			 $hasfilcheck = ($row['has_fil'] == 1 ? 'checked' : '');
			?>
			<div class="field form-group" style="display: none;">
				<label class="control-label" for="hasfil">Filament<span class="descr">Does the cache contain a filament?</span></label>
				<input type="checkbox" class="form-control" id="hasfil" name="hasfil" 
					value = '1' <?=$hasfilcheck ?>/>
			</div>

		  	<div class="field form-group">
				<?
				// NOTES
				// fill text area with existing note, but limit input length to current - to discourage 'new notes' here
				$lng = strlen($strNotes) + 70;
				?>
				<label class="control-label" for="newNote">Notes<span class="descr">Edit note</span></label>
				<textarea class="form-control" id="newNote" name="newNote" rows="3" maxlength="<?=$lng?>"><?=$strNotes?></textarea>
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