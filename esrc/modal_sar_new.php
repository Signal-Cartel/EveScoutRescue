<?php
$name_placeholder = '';
if(isset($_REQUEST['pilot'])){
	$name_placeholder = urldecode($_REQUEST['pilot']);
}

?>
<!-- SAR New Modal Form -->
<div id="ModalSARNew" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sar">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Search &amp; Rescue</h4>
      </div>
      <form name="sarnewform" id="sarnewform" action="rescueaction.php" method="POST">
	      <div class="modal-body black">
			<div class="form-group">
				<label class="control-label" for="system">System: </label>
				<input type="hidden" name="system" value="<?php echo $system ?>" />
				<span class="sechead"><?php echo $system ?></span>
			</div>
			<div class="field">
				<label class="control-label" for="pilot">Stranded Pilot
					<span class="descr">Other contact names, if any, should be entered in Notes.</span>
				</label>
				
				<input type="text" class="form-control " id="pilot" name="pilot" value="<?php echo $name_placeholder;?>"/>
			</div>
			
			<label class="checkbox-inline">
				<input id="canrefit" name="canrefit" type="checkbox" value="1">
				<strong>Can Refit</strong>
			</label>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<label class="checkbox-inline">
				<input id="launcher" name="launcher" type="checkbox" value="1">
				<strong>Has Probe Launcher</strong>
			</label>
			<div class="ws"></div>

		  	<div class="field">
				<label class="control-label" for="notes">Notes<span class="descr">Is there any other important information we need to know?</span></label>
				<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
			</div>
	      </div>
	      <div class="modal-footer">
	        <div class="form-actions">
	        	<input type="hidden" name="action" value="Create">
				<button type="submit" class="btn btn-info">Submit</button>
			</div>
	      </div>
      </form>
    </div>

  </div>
</div>