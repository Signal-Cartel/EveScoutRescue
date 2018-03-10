<!-- SAR Edit/Update Modal Form -->

<?php 
// prepare DB object
$database = new Database();
// create the query
$reqID = (isset($_REQUEST['req'])) ? $_REQUEST['req'] : '';
// get all rescue information
$request = $rescue->getRequest($reqID);
?>

<style>
	.sartable th, td {
	    padding: 4px;
	    vertical-align: text-top;
	}
</style>

<div id="ModalSAREdit" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sar">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Search &amp; Rescue</h4>
      </div>
      <form name="sareditform" id="sareditform" action="rescueaction.php" method="POST">
	      <div class="modal-body black">
	      	<table class="black sartable">
				<tr>
					<td align="right">System:</td>
					<td><strong><?=$system?></strong></td>
				</tr>
				<tr>
					<td align="right">Stranded Pilot:</td>
					<td><strong>
						<?php 
						// display pilot name only to coordinators
						if ($isCoord == 0)
						{
							echo 'PROTECTED<br />';
						}
						else
						{
							echo Output::htmlEncodeString($request['pilot']).'<br />';
						}
						?>
						</strong>
					</td>
				</tr>
				<tr>
					<td align="right">Request Created:</td>
					<td><strong><?=Output::getEveDate($request['requestdate'])?></strong></td>
				</tr>
				<tr>
					<td align="right">Creating Agent:</td>
					<td><strong><?=Output::htmlEncodeString($request['startagent'])?></strong></td>
				</tr>
				<?php
				// display closin agent only if status is closed
				if ($request['finished'] == 1) { ?>
				<tr>
					<td align="right">Closing Agent:</td>
					<td><strong><?=Output::htmlEncodeString($request['closeagent'])?></strong></td>
				</tr>
				<?php }	?>
				<tr>
					<td align="right">Last Contacted:</td>
					<td><strong><?=Output::getEveDate($request['lastcontact'])?></strong></td>
				</tr>
			</table>
			
			<hr>
			<?php 
			// display Last Contact checkbox and Status dropdown only to coordinators 
			if ($isCoord == 1)
			{
			?>
			<div class="field">
				<label for="contacted">
					<strong>Update "Last Contact" date to today?&nbsp;</strong>
					<input id="contacted" name="contacted" type="checkbox" class="checkbox pull-right" value="1">
				</label>
			</div>
			<div class="field">
				<label class="control-label" for="status">Status</label>
				<select class="form-control black" id="status" name="status">
					<?php if ($request['status'] === 'new') { ?>
					<option value="new" selected="selected">new</option>
					<?php } ?>
					<option value="pending" <?php if ($request['status'] === 'pending') { echo ' selected="selected"'; } ?>>Pending</option>
					<option value="open" <?php if ($request['status'] === 'open') { echo ' selected="selected"'; } ?>>Open</option>
					<option value="system-located" <?php if ($request['status'] === 'system-located') { echo ' selected="selected"'; } ?>>System Located</option>
					<option value="closed-esrc" <?php if ($request['status'] === 'closed-esrc') { echo ' selected="selected"'; } ?>>Closed - rescued (ESRC)</option>
					<option value="closed-rescued" <?php if ($request['status'] === 'closed-rescued') { echo ' selected="selected"'; } ?>>Closed - rescued (SAR)</option>
					<option value="closed-escaped" <?php if ($request['status'] === 'closed-escaped') { echo ' selected="selected"'; } ?>>Closed - escaped by self</option>
					<option value="closed-escapedlocals" <?php if ($request['status'] === 'closed-escapedlocals') { echo ' selected="selected"'; } ?>>Closed - escaped by locals</option>
					<option value="closed-destruct" <?php if ($request['status'] === 'closed-destruct') { echo ' selected="selected"'; } ?>>Closed - self destruct</option>
					<option value="closed-destroyed" <?php if ($request['status'] === 'closed-destroyed') { echo ' selected="selected"'; } ?>>Closed - destroyed by locals/3rd party</option>
					<option value="closed-noresponse" <?php if ($request['status'] === 'closed-noresponse') { echo ' selected="selected"'; } ?>>Closed - no response</option>
					<option value="closed-declined" <?php if ($request['status'] === 'closed-declined') { echo ' selected="selected"'; } ?>>Closed - declined</option>
					<option value="closed-dup" <?php if ($request['status'] === 'closed-dup') { echo ' selected="selected"'; } ?>>Closed - duplicate</option>
				</select>
			</div>
			<div class="ws"></div>
			<?php 
			}
			else 
			{
			?>
				<input type="hidden" name="status" id="status" value="<?=$request['status']?>">
			<?php
			}
			?>
		  	<div class="field">
				<label class="control-label" for="notes">Enter a new note</label>
				<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
			</div>
	      </div>
	      <div class="modal-footer">
	        <div class="form-actions">
	        	<input type="hidden" name="request" value="<?=$reqID?>">
	        	<input type="hidden" name="action" value="UpdateRequest">
	        	<input type="hidden" name="system" value="<?php echo $system ?>" />
				<button type="submit" class="btn btn-info">Submit</button>
			</div>
	      </div>
      </form>
    </div>

  </div>
</div>