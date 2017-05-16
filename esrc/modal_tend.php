<!-- Tender Modal Form -->
<div id="TendModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header tender">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Tender</h4>
      </div>
      <form name="tendform" id="tendform" action="process_tend.php" method="POST">
	      <div class="modal-body black">
			<div class="form-group">
				<label class="control-label" for="sys_tend">System: </label>
				<input type="hidden" name="sys_tend" value="<?php echo $targetsystem ?>" />
				<span class="sechead"><?php echo $targetsystem ?></span>
			</div>
			<div class="field">
				<label class="control-label" for="status">Status</label>
				<div class="radio">
					<label for="status_1">
						<input id="status_1" name="status" type="radio" value="Healthy">Healthy
					</label>
				</div>
				<div class="radio">
					<label for="status_2">
						<input id="status_2" name="status" type="radio" value="Upkeep Required">Upkeep Required
					</label>
				</div>
				<div class="radio">
					<label for="status_3">
						<input id="status_3" name="status" type="radio" value="Expired">Expired
					</label>
				</div>
				<p class="descr">What was the condition of the cache when you left it?<br />
					<b>Healthy</b> = Anchored, safe, and full of supplies<br />
					<b>Upkeep Required</b> = Needs re-supplied<br />
					<b>Expired</b> = Could not find or is unusable
				</p>
			</div>
		  	<div class="field">
				<label class="control-label" for="notes">Notes<span class="descr">Is there any other important information we need to know?</span></label>
				<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
			</div>
	      </div>
	      <div class="modal-footer">
	        <div class="form-actions">
				<input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
			    <button type="submit" class="btn btn-info">Submit</button>
			</div>
	      </div>
      </form>
    </div>

  </div>
</div>