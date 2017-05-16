<!-- Agent Modal Form -->
<div id="AgentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header adjunct">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Agent</h4>
      </div>
      <div class="modal-body black">
      	<form name="agentform" id="agentform" action="process_agent.php" method="POST">
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
			<input type="hidden" name="pilot" id="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
		</form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="submitForm">Send</button>
		<a href="#" class="btn" data-dismiss="modal">Close</a>
      </div>
    </div>

  </div>
</div>

<script>
$(document).ready(function(){
	$("#agentform").on("submit", function(e) {
        var postData = $(this).serializeArray();
        var formURL = $(this).attr("action");
		$.ajax({
			type: "POST",
			url: formURL,
			data: postData,
			success: function(data, textStatus, jqXHR) {
                //$('#AgentModal .modal-header .modal-title').html("Result");
                $('#AgentModal .modal-body').html(data);
                $("#submitForm").remove();
            },
            error: function(jqXHR, status, error) {
                console.log(status + ": " + error);
            }
        });
        e.preventDefault();
	});
	$("#submitForm").on('click', function() {
        $("#agentform").submit();
    });
});
</script>