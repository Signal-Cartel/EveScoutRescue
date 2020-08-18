<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$db = new Database();
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
//show detailed records if "Payout" is not checked
if (!isset($_POST['payout'])) {	?>

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
				$ctrtotact = $ctrsow = $ctrtend = 0;
				// "agent" actions are paid via SAR Dispatch, so do not count here
				$db->query("SELECT * FROM activity 
							WHERE EntryType <> 'agent' AND ActivityDate BETWEEN :start AND :end 
							ORDER By ActivityDate DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					$ctrtotact++;
					// calculate action cell format
					$actioncellformat= '';
					switch ($value['EntryType']) {
						case 'sower':
							$actioncellformat = ' style="background-color:#ccffcc;color:black;"';
							break;
						case 'tender':
							$actioncellformat= ' style="background-color:#d1dffa;color:black;"';
							break;
						default:
							// ??
					}
					echo '<tr>';
					// add 4 hours to convert to UTC (EVE) for display
					echo '<td class="white text-nowrap">'. 
							date("Y-m-d H:i:s", strtotime($value['ActivityDate'])) .
						 '</td>';
					echo '<td class="text-nowrap">
							<a class="payout" target="_blank" href="/esrc/personal_stats.php?pilot='. urlencode($value['Pilot']) .'">'. 
							$value['Pilot'] .'</a> - <a class="payout" target="_blank" 
							href="https://evewho.com/pilot/'. $value['Pilot'] .'">EW</a></td>';
					echo '<td class="white" '. $actioncellformat .'>'. ucfirst($value['EntryType']) .'</td>';
					switch ($value['EntryType']) {
						case 'sower':
							$ctrsow++;
							break;
						case 'tender':
							$ctrtend++;
							break;
					}
					echo '<td><a class="payout" href="/esrc/search.php?sys='. $value['System'] .'" target="_blank">'. 
							$value['System'] .'</a></td>';
					echo '<td><a class="payout" target="_blank" 
							href="https://evewho.com/pilot/'. $value['AidedPilot'] .'">'. 
							Output::htmlEncodeString($value['AidedPilot']) .'</td>';
					echo '<td class="white">'. Output::htmlEncodeString($value['Note']) .'</td>';
					echo '</tr>';
				}
		
				$db->query("SELECT COUNT(*) as cnt FROM cache WHERE Status <> 'Expired'");
				$row = $db->single();
				$ctrtot = $row['cnt'];
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
			Actions this period: <?php echo $ctrtotact; ?><br />
			Sown: <?php echo $ctrsow; ?><br />
			Tended: <?php echo $ctrtend; ?><br /><br />
			Total caches in space:<br />
			<?php echo $ctrtot; ?> of 2603 (<?php echo round((intval($ctrtot)/2603)*100,1); ?>%)
		</div>
	</div>
<?php
}

//show payout data if "Payout" is checked
else {	
	// get array of pilots who have opted out
	$db->query("SELECT pilot FROM payout_optout WHERE optout_type = 'ESRC'");
	$arrPilotsOptedOut = $db->resultset();
	$db->closeQuery();

	//count of all actions performed in the specified period
	// "agent" actions are paid via SAR Dispatch, so do not count here
	$db->query("SELECT Pilot, COUNT(DISTINCT(System)) as cnt FROM activity 
				WHERE EntryType <> 'agent' AND ActivityDate BETWEEN :start AND :end 
				GROUP BY Pilot");
	$db->bind(':start', $start);
	$db->bind(':end', $end);
	$rows = $db->resultset();
	$db->closeQuery();
	$ctrtot = $ctrLessOptouts = 0;
	foreach ($rows as $value) {
		$isOptedOut = array_search($value['Pilot'], array_column($arrPilotsOptedOut, 'pilot'));
		// skip loop if pilot has opted out from payout
		$ctrtot = $ctrtot + intval($value['cnt']);
		if ($isOptedOut !== false) { continue; }
		$ctrLessOptouts = $ctrLessOptouts + intval($value['cnt']);
	}	?>
	
	<div class="row" id="systable" style="padding-top: 20px;">
		<div class="col-sm-10">
			<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Pilot</th>
						<th class="white">Count</th>
						<th class="white">Payout</th>
					</tr>
				</thead>
				<tbody>
					<?php
					//summary data
					$ctrParticipants = $ctrParticipantsLessOptouts = 0;
					foreach ($rows as $value) {
						$ctrParticipants++;
						$isOptedOut = array_search($value['Pilot'], 
							array_column($arrPilotsOptedOut, 'pilot'));
						// set values accordingly; opted out pilots receive credit for 0
						if ($isOptedOut === false) {
							$countAmt = $value['cnt'];
							$strActualCount = '';
							$payoutAmt = round((intval($value['cnt'])/intval($ctrLessOptouts))*intval($_REQUEST['totamt']),2);
							$ctrParticipantsLessOptouts++;
						}
						else {
							$countAmt = 0;
							$strActualCount = ' ('. $value['cnt'] .')';
							$payoutAmt = 0;
						}
						echo '<tr>';
						echo '<td><a class="payout" target="_blank" href="/esrc/personal_stats.php?pilot='. urlencode($value['Pilot']) .'">'. 
							$value['Pilot'] .'</a> - <a class="payout" target="_blank" 
							href="https://evewho.com/pilot/'. $value['Pilot'] .'">EW</a></td>';
						echo '<td class="white" align="right">'. $countAmt . $strActualCount .'</td>';
						echo '<td><input type="text" id="amt'.$ctrParticipants.'" value="'. $payoutAmt .'" />
									<i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy(\'amt'.$ctrParticipants.'\')"></i>
							  </td>';
						echo '</tr>';
					}
					?>
					<tr>
						<td class="white">
							Paid Participants: <?=$ctrParticipantsLessOptouts?><br>
							All Participants: <?=$ctrParticipants?>

						</td>
						<td class="white" align="right">
							PAID TOTAL: <?=$ctrLessOptouts?><br>
							Sow/Tend Total: <?=$ctrtot?>
						</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
			Note that total count of actions here may differ from what is listed on non-Payout summary.
			This is because pilots are only paid for sows/tends in a given system once each week. 
			Multiple tends in the same system in the same week, e.g., do not count toward the total count for payout.
			However, they <i>do</i> count toward activity counts for medals and such.
		</div>
	</div>
<?php
}	


// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
