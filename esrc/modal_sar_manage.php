<!-- SAR Edit/Update Modal Form -->

<?php
// prepare DB object
//$database = new Database();
// create the query
$reqID = (isset($_REQUEST['req'])) ? $_REQUEST['req'] : '';
// get all rescue information
$request = $rescue->getRequest($reqID);

$agents = array_key_exists('RescueAgents', $request) ? explode(',', $request['RescueAgents']) : Array();
	
	// data display different for coordinators and pilots involved in rescue
	$isSARAgent = (
		($charname == $request['startagent']) 
		or (($charname == $request['locateagent'])and($_SESSION['is911']==1)) 
		or (in_array($charname, $agents) and ($_SESSION['is911']==1)) 
		or $isCoord
	) ? 1 :0;
	
	$isRescueAgent = (($charname == $request['locateagent']) or $isCoord) ? 1 : 0;
	
	
	
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
						// display pilot name only to coordinators and involved pilots
						if ($_SESSION['isCoord'] == 1 or $isSARAgent)
						{
							echo Output::htmlEncodeString($request['pilot']).'<br />';
						}
						else
						{
							echo 'PROTECTED<br />';
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
				// display closing agent only if status is closed
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
					<option value="new"
						<?php
							$located_disable = false;
							if ($request['status'] === 'new') {
								$located_disable = true;
								echo ' selected="selected"';
							}
						?> >new
					</option>
					<option value="pending" 
						<?php 
							if ($request['status'] === 'pending') { 
								$located_disable = true;
								echo ' selected="selected"'; 
							}
						?> >Pending
					</option>
					
					<option value="open" 
						<?php
							if ($request['status'] === 'open') {
								$located_disable = true;
								echo ' selected="selected"'; 
							}
						?> >Open
					</option>
					
					<?php if (!$located_disable){?>
						<option value="system-located" <?php if ($request['status'] === 'system-located') { echo ' selected="selected"'; } ?>>System Located</option>	
					<?php } ?>
					
					
					<!--<option value="closed-esrc" <?php if ($request['status'] === 'closed-esrc') { echo ' selected="selected"'; } ?>>Closed - rescued (ESRC)</option>-->
					<option value="closed-rescued" <?php if ($request['status'] === 'closed-rescued') { echo ' selected="selected"'; } ?>>Closed - rescued (SAR)</option>
					<option value="closed-escaped" <?php if ($request['status'] === 'closed-escaped') { echo ' selected="selected"'; } ?>>Closed - escaped by self</option>
					<option value="closed-escapedlocals" <?php if ($request['status'] === 'closed-escapedlocals') { echo ' selected="selected"'; } ?>>Closed - escaped by locals</option>
					<option value="closed-destruct" <?php if ($request['status'] === 'closed-destruct') { echo ' selected="selected"'; } ?>>Closed - self destruct</option>
					<option value="closed-destroyed" <?php if ($request['status'] === 'closed-destroyed') { echo ' selected="selected"'; } ?>>Closed - destroyed by locals/3rd party</option>
					<option value="closed-zkill" <?php if ($request['status'] === 'closed-zkill') { echo ' selected="selected"'; } ?>>Closed - zKill Activity</option>
					<option value="closed-noresponse" <?php if ($request['status'] === 'closed-noresponse') { echo ' selected="selected"'; } ?>>Closed - no response</option>
					<option value="closed-declined" <?php if ($request['status'] === 'closed-declined') { echo ' selected="selected"'; } ?>>Closed - declined</option>
					<option value="closed-dup" <?php if ($request['status'] === 'closed-dup') { echo ' selected="selected"'; } ?>>Closed - duplicate</option>
				</select>
			</div>
			<div class="field">
					<!-- should we check if mail already sent? -->
					<?php if ($request['status'] === 'closed-esrc') { ?>
							<label for="followup" class="pull-right">
									<strong>ESRC Follow-up Mail&nbsp;</strong>
									<a href="rescue_success_mail.php?req=<?=$reqID?>&typ=esrc" target="ESR-Mail" id="followup" class="btn btn-info" role="button">Create</a>
							</label>
					<?php } ?>
					<?php if ($request['status'] === 'closed-sar') { ?>
							<label for="followup" class="pull-right">
									<strong>SAR Follow-up Mail&nbsp;</strong>
									<a href="rescue_success_mail.php?req=<?=$reqID?>&typ=sar" target="ESR-Mail" id="followup" class="btn btn-info" role="button">Create</a>
							</label>
					<?php } ?>

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
				<label class="control-label" for="notes">Enter a new note - <?=$charname?></label>
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
