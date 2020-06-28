<!-- Agent Modal Form -->
<?php
// get active cache info
$rowAgent = $caches->getCacheInfo($system);
$cacheid = $rowAgent['CacheID'];
$hasfil =  $rowAgent['has_fil'];
//$fil_check = ($hasfil == 1 ? '' : 'disabled');
  $fil_check = '';
?>
<div id="AgentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header adjunct">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agent</h4>
      </div>
      <form name="agentform" id="agentform" action="process_agent.php" method="POST">
	      <div class="modal-body black">
		  	<div class="form-group">
				<label class="control-label" for="sys_adj">System:
					<input type="hidden" name="sys_adj" value="<?php echo $system ?>"/>
				</label>
				<span class="sechead"><?php echo $system ?></span>
			</div>
			<div class="checkbox">
			  	<label class="control-label" for="updateexp">
			  		<input type="checkbox" id="updateexp" name="updateexp" value="1" onClick="checkLogic(this);">
					Cache was accessed					
				</label>
			</div>
			<span class="control-label">Pilot was rescued using:</span>
			<div class="checkbox">
			  	<label class="control-label" for="succesrc">
					<input type="checkbox" id="succesrc" name="succesrc" value="1" onClick="checkLogic(this);">	
					Probes & scanner
				</label>
			</div>
			<div class="checkbox">
			  	<label class="control-label" for="succesrcf">
			  		<input type="checkbox" <?=$fil_check ?> id="succesrcf" name="succesrcf" value="1" onClick="checkLogic(this);">
					Filament					
				</label>
			</div>
			<div class="field">
				<label class="control-label" for="aidedpilot">Aided Pilot<span class="descr">What is the name of the Capsuleer who required assistance?</span>
					<input type="text" class="form-control" id="aidedpilot" name="aidedpilot" />
				</label>
			</div>
			<div class="field">
				<label class="control-label" for="notes">Notes<span class="descr">Other information we need to know?</span>
					<textarea maxlength="1000" class="form-control" id="notes" name="notes" rows="3"></textarea>
				</label>
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
<script>  

	
 function checkLogic(ele){
	var cacheAccessed = document.getElementById('updateexp');
	var usedProbes = document.getElementById('succesrc');
	var usedFilament = document.getElementById('succesrcf');
	var choice = ele.id;
	switch(choice) {
	  case 'updateexp':
		if (usedFilament.checked == true || usedProbes.checked == true){
			cacheAccessed.checked = true;	
		}
		break;
	  case 'succesrc':
			usedFilament.checked = false;
			cacheAccessed.checked = true;
		break;
	  case 'succesrcf':
			usedProbes.checked = false;
			cacheAccessed.checked = true;
		break;
	  default:
		return;
	}

 }

</script>














