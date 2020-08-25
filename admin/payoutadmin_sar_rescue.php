<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$db = new Database();
$user = new Users();
$pgtitle = 'SAR Locate/Rescue Payouts';


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

	<div class="row" id="systable">
		<div class="col-sm-10">
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Rescue Date</th>
						<th class="white">Aided Pilot</th>
						<th class="white">System</th>
						<th class="white">Locator</th>
						<th class="white">Rescuers</th>
					</tr>
				</thead>
				<tbody>
				<?php
				// pull all SAR rescues for specified period
				$db->query("SELECT id, closedate, pilot, `system`, locateagent
							FROM rescuerequest
							WHERE status = 'closed-rescued' AND closedate BETWEEN :start AND :end
							ORDER BY closedate DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					//$ctrtotact++;
					echo '<tr>';
					// add 4 hours to convert to UTC (EVE) for display; wonky during US-DST (should be 5 hours then)
					echo '<td class="white text-nowrap">'. 
							date("Y-m-d H:i:s", strtotime($value['closedate'])+14400) .
						 '</td>';
					echo '<td><a class="payout" target="_blank"
							href="https://evewho.com/pilot/'. $value['pilot'] .'">'.
							Output::htmlEncodeString($value['pilot']) .'</td>';
					echo '<td><a class="payout" href="/esrc/rescueoverview.php?sys='. ucfirst($value['system']) .
							'" target="_blank">'. ucfirst($value['system']) .'</a></td>';
					if (!empty($value['locateagent'])) {
						echo '<td class="text-nowrap">
							<a class="payout" target="_blank" href="/esrc/personal_stats.php?pilot='.
							urlencode($value['locateagent']) .'">'.
							$value['locateagent'] .'</a> - <a class="payout" target="_blank"
							href="https://evewho.com/pilot/'. $value['locateagent'] .'">EG</a></td>';
					} 
					else {
						echo '<td>&nbsp;</td>';
					}
					// list rescuer(s)
					echo '<td class="white">';
						// pull all SAR rescues for specified period
						$db->query("SELECT pilot FROM rescueagents WHERE reqid = :id 
									ORDER BY EntryTime");
						$db->bind(':id', $value['id']);
						$arrRescuers = $db->resultset();
						foreach ($arrRescuers as $valr) {
							echo $valr["pilot"] .'<br />';
						}
					echo '</td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?=gmdate('Y-m-d H:i:s', strtotime("now"))?> EVE<br /><br />
		</div>
	</div>

<?php
}

//show payout data if "Payout" is checked
else {			?>

	<div class="row" id="systable">
		<div class="col-sm-10">
			<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th class="white">System</th>
						<th class="white">Locator</th>
						<th class="white">Rescuer</th>
						<th class="white">Payout</th>
					</tr>
				</thead>
				<tbody>
					<?php
					//summary data
					$db->query("SELECT rr.id, rr.locateagent, rr.system, 
									datediff(rr.LastUpdated, rr.requestdate) AS daystosar, w.Class
								FROM rescuerequest rr, wh_systems w
								WHERE rr.system = w.System
								AND status = 'closed-rescued' 
								AND closedate BETWEEN :start AND :end
								ORDER BY closedate DESC");
					$db->bind(':start', $start);
					$db->bind(':end', $end);
					$rows = $db->resultset();
					$ctr = $totamt = 0;
					$dailyincrease = 10000000; // daily increase is 10 mil ISK
					foreach ($rows as $value) {
						// skip record if no locateagent listed
						if (empty($value['locateagent'])) { continue; }
						$ctr++;
						$daystosar = intval($value['daystosar']);
						// base rate is usually 50 mil ISK; but only 20 mil ISK on same-day rescues
						$basepay = ($daystosar > 0) ? 50000000 : 20000000;
						$strBasepay = ($basepay == 50000000) ? '50mil' : '20mil';
						// get "WH Class multiplier"
						$whclassmult = str_replace('Class ', '', $value['Class']);
						$whclassmult = ($whclassmult > 6) ? 6 : $whclassmult;	// max value of 6
						// max payout equation:
						// (base x WH class multiplier) + (Days until rescued x daily increase amt)
						$payoutmax = ($basepay*$whclassmult)+($daystosar*$dailyincrease);
						echo '<tr>';
						echo '<td><a class="payout" target="_blank"
								href="/esrc/rescueoverview.php?sys='. ucfirst($value['system']) .'">'.
								Output::htmlEncodeString(ucfirst($value['system'])) .'</a>
								<span class="white">('. $strBasepay .' x '. $whclassmult .') + 
									(10mil x '. $daystosar .')
									= '. number_format(intval($payoutmax)) .'</span></td>';
						echo '<td><a class="payout" target="_blank" 
								href="https://evewho.com/pilot/'. $value['locateagent'] .'">'. 
								Output::htmlEncodeString($value['locateagent']) .'</a></td>';
						echo '<td>&nbsp;</td>';
						// Locator gets half of total payout amount
						$payoutloc = intval($payoutmax/2);	
						echo '<td><input type="text" id="amt'.$ctr.'" width="100" 
								value="'. $payoutloc .'" /><i id="copyclip" class="fa fa-clipboard" 
								onClick="SelectAllCopy(\'amt'.$ctr.'\')"></i></td>';
						$totamt = $totamt + $payoutloc;
						echo '</tr>';

						// pull all rescuers for specified system rescue, ordered oldest to newest
						$db->query("SELECT pilot FROM rescueagents WHERE reqid = :id
									ORDER BY EntryTime");
						$db->bind(':id', $value['id']);
						$arrRescuerPayout = $db->resultset();
						$payoutres = 0;
						foreach ($arrRescuerPayout as $val) {
							// do not pay Locator a second time
							if ($value['locateagent'] != $val['pilot']) {
								$ctr++;
								echo '<tr>';
								echo '<td></td><td></td>';
								echo '<td><a class="payout" target="_blank" 
										href="https://evewho.com/pilot/'. $val['pilot'] .'">'. 
										Output::htmlEncodeString($val['pilot']) .'</a></td>';
								// first rescuer gets half of locator pay, then half again for each successive rescuer
								if ($payoutres == 0) {
									$payoutres = intval($payoutloc/2);
								} 
								else {
									$payoutres = intval($payoutres/2);
								}
								echo '<td><input type="text" id="amt'.$ctr.'" width="100" value="'. 
										intval($payoutres) .'" /><i id="copyclip" class="fa 
										fa-clipboard" onClick="SelectAllCopy(\'amt'.$ctr.'\')"></i></td>';
								$totamt = $totamt + $payoutres;
								echo '</tr>';
							}
						}
					}
					?>
					<tr>
						<td class="white" align="right">Participants:</td>
						<td class="white">&nbsp;<?=$ctr;?></td>
						<td class="white" align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL: </td>
						<td class="white"><?=number_format($totamt);?> ISK</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
		</div>
	</div>

<?php
}


// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
