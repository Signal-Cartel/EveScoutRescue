<!-- Agent Modal Form -->
<div id="AgentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header adjunct">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Agent</h4>
      </div>
      <input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
      <div class="modal-body black">
      	<form name="agent">
	        <div class="form-group">
				<label class="control-label" for="sys_adj">System: </label>
				<input type="hidden" name="sys_adj" value="<?php echo $targetsystem ?>" />
				<span class="sechead"><?php echo $targetsystem ?></span>
			</div>
			<div class="field">
				<label class="control-label" for="aidedpilot">Aided Pilot<span class="descr">What is the name of the Capsuleer who required rescue?</span></label>
				<input type="text" class="form-control" id="aidedpilot" name="aidedpilot" />
			</div>
			<div class="field">
				<label class="control-label" for="notes">Notes<span class="descr">Is there any other important information we need to know?</span></label>
				<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
			</div>
		</form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="submit">Send</button>
		<a href="#" class="btn" data-dismiss="modal">Close</a>
      </div>
    </div>

  </div>
</div>

<script>
$(document).ready(function(){
	$("button#submit").click(function(){
		$.ajax({
			type: "POST",
			url: "process_agent.php",
			data: $('form.agent').serialize(),
			success: function(message){
				//$("#agent").html(message);
				//$("#AgentModal").modal('hide');
				$('#AgentModal .modal-body').html(message);
                $("#submit").remove();
			},
			error: function(){
				alert("Error");
			}
		});
	});
});
</script>