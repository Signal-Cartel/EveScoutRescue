<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$db = new Database();
$lb = new Leaderboard($db);
$pgtitle = 'ESRC Payouts';


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<div class="col-sm-12" style="text-align: center; height: 100px; vertical-align: middle;">
		<?php require '../page_templates/admin_payouts-header.php'; ?>
	</div>
</div>

<?php
if (!isset($_POST['payout'])) {		// show detailed records if "Payout" is not checked	?>

	<div class="row" id="systable" style="padding-top: 20px;">
		<div class="col-sm-10 white">
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Date</th>
						<th class="white">Pilot</th>
						<th class="white">Type</th>
						<th class="white">System</th>
						<th class="white">Aided&nbsp;Pilot</th>
						<th class="white" style="width: 35%;">Note</th>
					</tr>
				</thead>
				<tbody>

				<?php
				$ctrTotalActions = $ctrSows = $ctrTends = 0;
				$rows = $lb->getESRCPayees($start, $end, false);
				foreach ($rows as $value) {
					$ctrTotalActions++;	// increment total actions counter
					if ($value['EntryType'] == 'sower') {
						$actioncellformat = ' style="background-color:#ccffcc;color:black;"';
						$ctrSows++;
					}
					if ($value['EntryType'] == 'tender') {
						$actioncellformat= ' style="background-color:#d1dffa;color:black;"';
						$ctrTends++;
					} 	?>

					<tr>
						<td class="white text-nowrap">
							<?=date("Y-m-d H:i:s", strtotime($value['ActivityDate']))?></td>
						<td class="text-nowrap">
							<a class="payout" target="_blank" 
								href="/esrc/personal_stats.php?pilot=<?=urlencode($value['Pilot'])?>">
								<?=$value['Pilot']?>'</a> - <a class="payout" target="_blank" 
								href="https://evewho.com/pilot/<?=$value['Pilot']?>">EW</a></td>
						<td class="white"<?=$actioncellformat?>><?=ucfirst($value['EntryType'])?></td>
						<td><a class="payout" href="/esrc/search.php?sys=<?=$value['System']?>" 
							target="_blank"><?=$value['System']?></a></td>
						<td><a class="payout" target="_blank" 
							href="https://evewho.com/pilot/<?=$value['AidedPilot']?>"> 
							<?=Output::htmlEncodeString($value['AidedPilot'])?></td>
						<td class="white"><?=Output::htmlEncodeString($value['Note'])?></td>
					</tr>
					
					<?php
				}	?>

				</tbody>
			</table>
		</div>

		<div class="col-sm-2 white">
			<table class="table table-condensed" style="width: auto;">
				<tr>
					<td class="white">Sown:</td>
					<td class="white text-right"><?=$ctrSows?></td>
				</tr>
				<tr>
					<td class="white">Tended:</td>
					<td class="white text-right"><?=$ctrTends?></td>
				</tr>
				<tr>
					<td class="white">TOTAL:</td>
					<td class="white text-right"><?=$ctrTotalActions?></td>
				</tr>
			</table>

			<?php
			$caches = new Caches($db);
			$ctrTotalCaches = $caches->getActiveCount();
			?>

			Active caches:<br />
			<?=$ctrTotalCaches?> of 2603 (<?=round((intval($ctrTotalCaches)/2603)*100,1)?>%)
		</div>
	</div>
<?php
}

//show payout data if "Payout" is checked
else {		?>
	
	<div class="row" id="systable" style="padding-top: 20px;">
		<div class="col-sm-12">
			<p class="white">
			Note that total count of actions here may differ from what is listed on non-Payout summary.
			This is because pilots are only paid for sows/tends in a given system once each week. 
			Multiple tends in the same system in the same week, e.g., do not count toward the total 
			count for payout. However, they <i>do</i> count toward activity counts for medals and such.
			</p>
			<table class="table" style="margin: auto; width: auto;">
				<thead>
					<tr>
						<th class="white">Pilot</th>
						<th class="white">Count</th>
						<th class="white">Payout</th>
					</tr>
				</thead>
				<tbody>

				<?php
				$ctrParticipants = $ctrParticipantsLessOptouts = 0; 
				$ctrTotalActions = $ctrActionsLessOptouts = 0;
				
				$pilot = new Pilot($db);
				$arrPilotsOptedOut = $pilot->getOptedOutPilots('ESRC');

				$rows = $lb->getESRCPayees($start, $end, true);

				// get $ctrActionsLessOptouts first to use in calculating individual payout amounts
				foreach ($rows as $value) {
					$isOptedOut = array_search($value['Pilot'], array_column($arrPilotsOptedOut, 'pilot'));
					if ($isOptedOut === false) {
						$ctrActionsLessOptouts = $ctrActionsLessOptouts + intval($value['cnt']);
					}
				}

				// build table rows
				foreach ($rows as $value) {
					// increment counters
					$ctrParticipants++;
					$ctrTotalActions = $ctrTotalActions + intval($value['cnt']);
					
					// some pilots have opted out of payments for ESRC activity; set values accordingly
					$isOptedOut = array_search($value['Pilot'], array_column($arrPilotsOptedOut, 'pilot'));
					if ($isOptedOut === false) {
						$ctrParticipantsLessOptouts++;
						$countAmt = $value['cnt'];
						$strActualCount = '';
						$payoutAmt = round((intval($value['cnt'])/intval($ctrActionsLessOptouts))*intval($_REQUEST['totamt']),2);
					}
					else {	// opted out pilots receive credit for 0
						$countAmt = 0;
						$strActualCount = ' ('. $value['cnt'] .')';
						$payoutAmt = 0;
					}	?>

					<tr>
						<td><a class="payout" target="_blank" 
							href="/esrc/personal_stats.php?pilot=<?=urlencode($value['Pilot'])?>">
							<?=$value['Pilot']?></a> - <a class="payout" target="_blank" 
							href="https://evewho.com/pilot/<?=$value['Pilot']?>">EW</a></td>
						<td class="white" align="right"><?=$countAmt . $strActualCount?></td>
						<td><input type="text" id="amt<?=$ctrParticipants?>" value="<?=$payoutAmt?>" />
							<i id="copyclip" class="white fa fa-clipboard" 
							onClick="SelectAllCopy('amt<?=$ctrParticipants?>')"></i></td>
					</tr>
				
					<?php
				}	?>

					<tr>
						<td class="white">
							Paid Participants: <?=$ctrParticipantsLessOptouts?><br>
							All Participants: <?=$ctrParticipants?>

						</td>
						<td class="white" align="right">
							PAID TOTAL: <?=$ctrActionsLessOptouts?><br>
							Sow/Tend Total: <?=$ctrTotalActions?>
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php
}	


// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
