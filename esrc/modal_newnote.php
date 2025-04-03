<!-- New Note Modal Form -->
<!-- EXISTING CACHE INFO -->
<?php 
// get cache information from database
$row = $caches->getCacheInfo($system);
if (empty($row)) {
	$row = Array();
	$row['CacheID'] = "";
	$row['Status'] = "";
}
?>
							
							
<div id="NoteModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
				<div class="modal-header tender">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">New cache note</h4>
				</div>
				<form name="noteform" id="noteform" action="process_note.php" method="POST">
					<div class="modal-body black">
						<div class="pull-right">					

							<!-- END EXISTING CACHE INFO -->
						</div>
						<div class="form-group">
							<label class="control-label" for="sys_note">System</label>
							<input type="hidden" name="sys_note" value="<?php echo $system ?>" />
							<input type="hidden" name="status" value="<?=$row['Status']?>" />
							<span class="sechead"><?php echo $system ?></span>
						</div>

		
							<div class="field form-group">
								<label class="control-label" for="notes">Note<span class="descr">140 character limit</span></label>
								<textarea class="form-control" id="notes-new" name="notes" rows="4" cols="35" maxlength="140"></textarea>
							</div>

						<div class="modal-footer">
							<div class="form-actions">
									<input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
									<input type="hidden" name="CacheID" value="<?=$row['CacheID']?>" />
								<button type="submit" class="btn btn-info">Submit</button>
							</div>
						</div>
					</div>
						<script>
						  $( document ).ready(function() {
							$("#noteform").validator();
						  });
						</script>
				</form>
		</div>
	</div>
</div>
