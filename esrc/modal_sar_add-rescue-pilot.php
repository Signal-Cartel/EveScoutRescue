<!-- SAR Add Rescue Pilot Modal Form -->

<?php
// get rescue data
$reqID = $_REQUEST['reqp'] ?? '';
$request = $rescue->getRequest($reqID);
$agent_type = $_REQUEST['agent'] ?? '';
// check for required permissions
$isSARAgent = $users->isSARAgent($charname, $reqID);
$isRescueAgent = $users->isRescueAgent($charname, $reqID);	
?>

<style>
    .newrescuepilot {
        border: 2px solid #CCCCCC;
        border-radius: 8px 8px 8px 8px;
        font-size: .9em;
        height: 15px;
        line-height: 15px;
        outline: medium none;
        padding: 8px 4px;
        width: 100%;
	}
	
	.tt-dropdown-menu {
		width: 130%;
		margin-top: 5px;
		padding: 8px 0px;
		background-color: #fff;
		border: 1px solid #ccc;
		border: 1px solid rgba(0, 0, 0, 0.2);
		border-radius: 8px 8px 8px 8px;
		font-size: 100%;
		color: #111;
		background-color: #F1F1F1;
	}
</style>

<div id="ModalSARAddPilot" class="modal fade" role="dialog">
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
						if ($isCoord == 0 and $isSARAgent == 0 and $isRescueAgent == 0 )
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
			{   ?>
			
            <div class="field">
				<label class="control-label" for="status">Add Rescue Pilot</label>
				<div class="form-group">
					<input type="text" name="newrescuepilot" id="newrescuepilot" 
						class="newrescuepilot" size="30" autoFocus="autoFocus" 
						autocomplete="off" placeholder="Pilot Name">
				</div>
			</div>
			<div class="ws"></div>
			
            <?php
			}   ?>
		  	
	      </div>
	      <div class="modal-footer">
	        <div class="form-actions">
				<input type="hidden" name="request" value="<?=$reqID?>">
	        	<input type="hidden" name="action" value="AddRescuePilot">
				<input type="hidden" name="system" value="<?=$system?>">
				<input type="hidden" name="agent_type" value="<?=$agent_type?>">
				<button type="submit" class="btn btn-info">Submit</button>
			</div>
	      </div>
      </form>
    </div>

  </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
        $('input.newrescuepilot').typeahead({
            name: 'newrescuepilot',
            remote: '../admin/data_user_roles_lookup.php?query=%QUERY'
        });
    })
</script>