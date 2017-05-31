<!-- SAR Edit/Update Modal Form -->

<?php 
// prepare DB object
$database = new Database();
// create the query
$reqID = (isset($_REQUEST['req'])) ? $_REQUEST['req'] : '';
$database->query("select * from rescuerequest where id = :rescueid");
$database->bind(":rescueid", $reqID);
$request = $database->single();
$database->closeQuery();
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
					<td><strong><?=Output::htmlEncodeString($request['pilot'])?></strong></td>
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
			
			<!-- NOTES -->
			<?php 
			$database->query("select notedate, agent, note from rescuenote where rescueid = :rescueid order by notedate desc");
			$database->bind(":rescueid", $reqID);
			$notes = $database->resultset();
			$database->closeQuery();
			if (count($notes) > 0) {
			?>
			<table class="black sartable">
				<tr>
					<th colspan="3">Notes</th>
				</tr>
				<?php foreach($notes as $note) { ?>
				<tr>
					<td align="top"><?=date("M-d", strtotime($note['notedate']))?></td>
					<td align="top"><?=Output::htmlEncodeString($note['agent'])?></td>
					<td><?=Output::htmlEncodeString($note['note'])?></td>
				</tr>
				<?php } // end foreach ?>
			</table>
			<?php
			} // end count(rows)
			?>
			<hr>
			<div class="field">
				<label for="contacted">
					<strong>Check if contacted recently&nbsp;</strong>
					<input id="contacted" name="contacted" type="checkbox" class="checkbox pull-right" value="1">
				</label>
			</div>
			<div class="field">
				<label class="control-label" for="status">Status</label>
				<select class="form-control black" id="status" name="status">
					<?php if ($request['status'] === 'new') { ?>
					<option value="new" selected="selected">new</option>
					<?php } ?>
					<option value="pending" <?php if ($request['status'] === 'pending') { echo ' selected="selected"'; } ?>>pending</option>
					<option value="open" <?php if ($request['status'] === 'open') { echo ' selected="selected"'; } ?>>open</option>
					<option value="status_closed_rescued" <?php if ($request['status'] === 'status_closed_rescued') { echo ' selected="selected"'; } ?>>Closed - rescued</option>
					<option value="closed-escaped" <?php if ($request['status'] === 'closed-escaped') { echo ' selected="selected"'; } ?>>Closed - escaped by self</option>
					<option value="closed-escapedlocals" <?php if ($request['status'] === 'closed-escapedlocals') { echo ' selected="selected"'; } ?>>Closed - escaped by locals</option>
					<option value="closed-destruct" <?php if ($request['status'] === 'closed-destruct') { echo ' selected="selected"'; } ?>>Closed - self destruct</option>
					<option value="closed-noresponse" <?php if ($request['status'] === 'closed-noresponse') { echo ' selected="selected"'; } ?>>Closed - no response</option>
				</select>
			</div>
			<div class="ws"></div>
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