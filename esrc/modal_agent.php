<!-- Agent Modal Form -->
<?php
// get active cache info
$rowAgent = $caches->getCacheInfo($system);
$cacheid = $rowAgent['CacheID'];
?>
<div id="AgentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header adjunct">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Agent</h4>
      </div>
      <form name="agentform" id="agentform" action="process_agent.php" method="POST">
	      <div class="modal-body black">
		  	<div class="form-group">
				<label class="control-label" for="sys_adj">System: </label>
				<input type="hidden" name="sys_adj" value="<?php echo $system ?>" />
				<span class="sechead"><?php echo $system ?></span>
			</div>
			<div class="checkbox">
			  	<label class="control-label" for="updateexp">
			  		<input type="checkbox" id="updateexp" name="updateexp" value="1"> Cache was 
			  			accessed</label>
			</div>
			<div class="checkbox">
			  	<label class="control-label" for="succesrc">
			  		<input type="checkbox" id="succesrc" name="succesrc" value="1"> Pilot was able 
			  			to successfully exit system thanks to the rescue cache</label>
			</div>
			<div class="field">
				<label class="control-label" for="aidedpilot">Aided Pilot<span class="descr">What 
					is the name of the Capsuleer who required assistance?</span></label>
				<input type="text" class="form-control" id="aidedpilot" name="aidedpilot" />
			</div>
			<div class="field">
				<label class="control-label" for="notes">Notes<span class="descr">Is there any other 
					important information we need to know?</span></label>
				<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
			</div>
	      </div>
	      <div class="modal-footer">
	        <div class="form-actions">
					<input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
					<input type="hidden" name="CacheID" value="<?=$row['CacheID']?>" />
			    <button type="submit" class="btn btn-info">Submit</button>
			</div>
	      </div>
      </form>
    </div>

  </div>
</div>