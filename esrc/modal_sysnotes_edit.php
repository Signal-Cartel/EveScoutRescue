<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
if (!defined('ESRC')) define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
include_once '../class/db.class.php';
require_once '../class/systems.class.php';

$db = new Database();
$systems = new Systems($db);

// set pilot name
$snPilot =  isset($charname) ? $charname : 'charname_not_set';

// note content
$editNote = '';

// get noteID and lookup existing note, if needed
$noteID = (isset($_REQUEST['noteid'])) ? $_REQUEST['noteid'] : -1;
if ($noteID > 0) {
    //look up note to edit
    $arrEditNote = $systems->getSystemNote($noteID);
    if (!empty($arrEditNote)) { 
        $editNote = $arrEditNote['note'];
        $snPilot = $arrEditNote['noteby'];
    }
}

// are we deleting this note?
$isDelete = (isset($_REQUEST['notedel']) && $_REQUEST['notedel'] == '1') ? true : false;
if ($isDelete) {
    $strTextArea = '<label class="control-label" for="notes">Are you sure you want to delete this note?<br />
        If yes, press Submit; if no, click "X" to close popup</label><textarea class="form-control" readonly 
        name="notes" rows="3">' . $editNote . '</textarea>';
}
else {
    $strTextArea = '<label class="control-label" for="notes">Notes</label><textarea class="form-control" 
        name="notes" rows="3">' . $editNote . '</textarea>';
}

// check if the request is made by a POST request
if (isset($_POST['systemname'])) {
	// yes, process the request
	$snPilot = $snNote = $snSystem = "";
    
    $snPilot = $_POST["pilot"];
    $snSystem = $_POST["systemname"];
    $phpPageForm = $_POST["refurl"];

    // prepare note
    $snNote = $_POST["notes"];
    $snNote = trim($snNote);
	$snNote = stripslashes($snNote);
	$snNote = htmlspecialchars_decode($snNote);    
	
	//FORM VALIDATION
	if (empty($snNote)) {
        $errmsg = "You must enter a note.\n";
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = $phpPageForm . "?sys=". $snSystem ."&errmsg=". urlencode($errmsg);
	} 
	// otherwise, perform DB UPDATES
	else {
        if ($noteID == -1) {
            //add new note
            $systems->addSystemNote($snSystem, $snPilot, $snNote);
        }
        else {
            // delete existing note; can only be done by Coordinators
            if ($_POST['delnote'] == 1) {
                if ($_POST['coord'] == 1) { $systems->deleteSystemNote($noteID); }
            }
            //edit existing note; can only be done by Coordinators and note author
            else {
                if ($_POST['coord'] == 1 || $charname == $snPilot) {
                    $systems->editSystemNote($noteID, $snNote, gmdate("Y-m-d H:i:s", time()));
                }
            }
        }

		//redirect back to original page to show updated info
		$redirectURL = $phpPageForm . "?sys=". $snSystem;
	}
	//END DB UPDATES
	?>
    <script>
        window.location.replace("<?=$redirectURL?>")
    </script>
<?php 
} // end form POST processing
?>

<!-- System Notes Add/Edit Modal Form -->
<div id="ModalSysNotesEdit" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header sysnotes">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title sechead"><?=$system?> - Add/Edit System Notes</h4>
        </div>
        <form name="agentform" id="agentform" action="modal_sysnotes_edit.php" method="POST">
            <div class="modal-body black">
                <div class="field">
                    <?=$strTextArea?>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-actions">
                        <input type="hidden" name="pilot" value="<?=$snPilot?>" />
                        <input type="hidden" name="systemname" value="<?=$system?>" />
                        <input type="hidden" id="noteid" name="noteid" value="<?=$noteID?>" />
                        <input type="hidden" name="refurl" value="<?=$phpPage?>" />
                        <input type="hidden" name="delnote" value="<?=($isDelete) ? 1 : 0?>" />
                        <input type="hidden" name="coord" value="<?=($isCoord) ? 1 : 0?>" />
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>
            </div>
        </form>
    </div>

  </div>
</div>