<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
if (!defined('ESRC')) define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
include_once '../class/db.class.php';
require_once '../class/systems.class.php';
require_once '../class/caches.class.php';

$db = new Database();
$systems = new Systems($db);
$caches = new Caches($db);

// set pilot name
$snPilot =  isset($charname) ? $charname : 'charname_not_set';
// check for SAR Coordinator login
if (!isset($_SESSION['isCoord'])){
	$isCoord = $_SESSION['isCoord'] = ($users->isSARCoordinator($charname) || $users->isAdmin($charname));	
}
else{
	$isCoord = $_SESSION['isCoord'];
}



// note content
$editNote = '';
$snType = '';
$formfunction = 'Add Note';

// get noteID and lookup existing note, if needed
$noteID = (isset($_REQUEST['noteid'])) ? $_REQUEST['noteid'] : -1;

if ($noteID > 0) {
	$formfunction = 'Edit Note';
    //look up note to edit
    $arrEditNote = $systems->getSystemNote($noteID);
    if (!empty($arrEditNote)) { 
        $editNote = $arrEditNote['note'];
        $snPilot = $arrEditNote['noteby'];
		$snType = $arrEditNote['notetype'];
    }
}

// are we deleting this note?
$isDelete = (isset($_REQUEST['notedel']) && $_REQUEST['notedel'] == '1') ? true : false;
if ($isDelete) {
	$formfunction = 'Delete Note';
    $strTextArea = '<p>Are you sure you want to delete this note?<p>
					<p>' . $editNote . '</p>
					<p>If no, click "X" to close this popup.</p>';
	$strTypeArea = '<input type="hidden" name="ntype" value="" />';
}
else {
    $strTextArea = '<label class="control-label" for="notes">Notes</label><textarea class="form-control" 
        name="notes" rows="3">' . $editNote . '</textarea>';
	
	//$tempchecked = ($snType == '' ) ? 'checked' : '';
	$infochecked = ($snType == 'info') ? 'checked' : '';
	$warnchecked = ($snType == 'warn') ? 'checked' : '';
	if ($infochecked == '' and $warnchecked == ''){$infochecked = 'checked';}
	//<input type="radio" id="temp" name="ntype" value="" '. $tempchecked. '>
	//<label for="temp"><span style="color: white;">Note is temporary - displayed in white</span></label><br>
	$strTypeArea = '
				<input type="radio" id="info" name="ntype" value="info" '. $infochecked. '>
				<label for="info"><span style="color: green;">Note is permanent and informative - displayed in green</span></label><br>
				<input type="radio" id="warn" name="ntype" value="warn"'. $warnchecked. '>
				<label for="warn"><span style="color: red;">Note is permanent and cautionary - displayed in red</span></label>';			
}

// check if the request is made by a POST request
if (isset($_POST['systemname'])) {
	// yes, process the request
	$snPilot = $snNote = $snSystem = $snType = "";

    $snPilot = $_POST["pilot"];
    $snSystem = $_POST["systemname"];
	$snType = $_POST["ntype"];
		
    $phpPageForm = $_POST["refurl"];

    // prepare note
	$snNote='';
	if (isset($_POST["notes"])){
		$snNote = $_POST["notes"];
		$snNote = trim($snNote);
	}

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = $phpPageForm . "?sys=". $snSystem ."&errmsg=". urlencode($errmsg);
	} 
	// otherwise, perform DB UPDATES
	else {
        if ($noteID == -1) {
            //add new note
            $systems->addSystemNoteType($snSystem, $snPilot, $snNote, $snType);
        }
        else {
            // delete existing note; can only be done by Coordinators
            if (isset ($_POST['notedel']) and $_POST['notedel'] == 1) {
                if ($isCoord) { $systems->deleteSystemNote($noteID); }
            }
            //edit existing note; can only be done by Coordinators and note author
            else {
                if ($isCoord || $charname == $snPilot) {
                    $systems->editSystemNoteType($noteID, $snNote, gmdate("Y-m-d H:i:s", time()), $snType );
                }
            }
        }

		//redirect back to original page to show updated info
		$redirectURL = $phpPageForm . "?sys=". $snSystem;
	}

	?>
    <script>
        window.location.replace("<?=$redirectURL?>")
    </script>
<?php
}//END FORM PROCESS

?>

<!-- System Notes Add/Edit Modal Form -->
<div id="ModalSysNotesEdit" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header sysnotes">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title sechead"><?=$system?> - <?=$formfunction?></h4>
        </div>
        <form name="agentform" id="agentform" action="modal_sysnotes_edit.php" method="POST">
            <div class="modal-body black">
                <div class="field">
					<?=$strTextArea?>
                </div>
                <div class="field">
					<?=$strTypeArea?>
				</div>
		
            </div>
            <div class="modal-footer">
                <div class="form-actions">
                        <input type="hidden" name="pilot" value="<?=$snPilot?>" />
                        <input type="hidden" name="systemname" value="<?=$system?>" />
                        <input type="hidden" id="noteid" name="noteid" value="<?=$noteID?>" />
                        <input type="hidden" name="refurl" value="<?=$phpPage?>" />
                        <input type="hidden" name="notedel" value="<?=($isDelete) ? 1 : 0?>" />

                    <button type="submit" class="btn btn-info"><?=$formfunction?></button>
                </div>
            </div>
        </form>
    </div>

  </div>
</div>