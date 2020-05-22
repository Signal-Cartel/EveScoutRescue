<!-- Tender Modal Form -->
<div id="TendModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
				<div class="modal-header tender">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Tender</h4>
				</div>
				<form name="tendform" id="tendform" action="process_tend.php" method="POST">
					<div class="modal-body black">
						<div class="pull-right">					
							<!-- EXISTING CACHE INFO -->
							<?php 
							// get cache information from database
							$row = $caches->getCacheInfo($system);
							if (!empty($row)) {
								$hasfil = ($row['has_fil'] == 1 ? 'checked' : '');
							?>
								<strong>Existing Cache Info</strong><br />
								Location: <?=$row['Location']?><br />
								Align: <?=$row['AlignedWith']?><br />
								Distance: <?=Output::htmlEncodeString($row['Distance'])?><br />
								Password: <input type="text" id="cachepassTend" 
											value="<?=Output::htmlEncodeString($row['Password'])?>" 
											readonly /><i id="copyclip" class="fa fa-clipboard" 
											onClick="SelectAllCopy('cachepassTend')"></i>
							<?php 
							}
							?>
							<!-- END EXISTING CACHE INFO -->
						</div>
						<div class="form-group">
							<label class="control-label" for="sys_tend">System</label>
							<input type="hidden" name="sys_tend" value="<?php echo $system ?>" />
							<span class="sechead"><?php echo $system ?></span>
						</div>
						<div class="field form-group">
							<label class="control-label" for="status">Status</label>
							<div class="radio">
								<label for="status_1">
									<input id="status_1" name="status" type="radio" value="Healthy" <?php if (0 == $caches->isTendingAllowed($system)) {echo ' disabled="disabled" '; } ?> required data-error="Please select a status for the cache">
									<?php if (0 == $caches->isTendingAllowed($system)) { ?>
										Tended within the last 24 hours.
									<?php 
									}
									else 
									{
									?>
										<strong>Healthy</strong> = Anchored, safe, contains 8 probes/scanner
									<?php 
									}
									?>
								</label>
							</div>
							<div class="radio">
								<label for="status_2">
									<input id="status_2" name="status" type="radio" value="Upkeep Required" required data-error="Please select a status for the cache">
									<strong>Upkeep Required</strong> = Needs probes and/or scanner
								</label>
							</div>
							<div class="radio">
								<label for="status_3">
									<input id="status_3" name="status" type="radio" value="Expired" required data-error="Please select a status for the cache">
									<strong>Expired</strong> = Could not find or is unusable
								</label>
							</div>
							<div>
								<label for="hasfil" style="margin-bottom: 20px">
									<input id="hasfil" name="hasfil" type="checkbox" <?=$hasfil?> value="1" style="width: 40px; height: 24px;" data-error="Does the cache contain a filament?">
									<p style="display: inline; position: relative; top: -6px;">
									<strong style="background-color: rgba(136, 0, 0, .6);">Filament: </strong>Does the cache contain a filament?
									</p>
								</label>
							</div>
							
							<div class="field form-group">
								<label class="control-label" for="notes">Notes<span class="descr">Is there any other important information we need to know?</span></label>
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
					</div>
						<script>
						  $( document ).ready(function() {
							$("#tendform").validator();
						  });
						</script>
				</form>
		</div>
	</div>
</div>
