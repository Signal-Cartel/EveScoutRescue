<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$db = new Database();
$pgtitle = 'SAR Dispatch Payouts';


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
						<th class="white">Date</th>
						<th class="white">Pilot</th>
						<th class="white">Type</th>
						<th class="white">System</th>
						<th class="white">Aided&nbsp;Pilot</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$ctrtotact = $ctropen = $ctrrescued = $ctrescaped = $ctrescapedlocals = 0;
				$ctrdestruct = $ctrdestroyed = $ctrnoresponse = $ctrdeclined = $ctresrc = 0;
				// pull all start agents aside from ESRC Agents and duplicates
				$db->query("SELECT requestdate, startagent, status, system, pilot
							FROM rescuerequest
							WHERE status <> 'closed-dup' AND requestdate BETWEEN :start AND :end
							ORDER BY requestdate DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					$ctrtotact++;
					// calculate action cell format
					$actioncellformat= '';
					echo '<tr>';
					// add 4 hours to convert to UTC (EVE) for display
					echo '<td class="white text-nowrap">'. 
							date("Y-m-d", strtotime($value['requestdate'])) .
						 '</td>';
					echo '<td class="text-nowrap">
							<a class="payout" target="_blank" href="/esrc/personal_stats.php?pilot='. 
								urlencode($value['startagent']) .'">'. 
								$value['startagent'] .'</a> - <a class="payout" target="_blank" 
								href="https://evewho.com/pilot/'. $value['startagent'] .'">EW</a></td>';
					echo '<td class="white" '. $actioncellformat .'>'. ucfirst($value['status']) .'</td>';
					switch ($value['status']) {
						case 'pending':
						case 'open':
						case 'system-located':
							$ctropen++;
							break;
						case 'closed-rescued':
							$ctrrescued++;
							break;
						case 'closed-escaped':
							$ctrescaped++;
							break;
						case 'closed-esrc':
							$ctresrc++;
							break;
						case 'closed-escapedlocals':
							$ctrescapedlocals++;
							break;
						case 'closed-destruct':
							$ctrdestruct++;
							break;
						case 'closed-destroyed':
							$ctrdestroyed++;
							break;
						case 'closed-noresponse':
							$ctrnoresponse++;
							break;
						case 'closed-declined':
							$ctrdeclined++;
							break;
					}
					echo '<td><a class="payout" href="/esrc/rescueoverview.php?sys='. $value['system'] .'" target="_blank">'. 
							$value['system'] .'</a></td>';
					echo '<td><a class="payout" target="_blank" 
							href="https://evewho.com/pilot/'. $value['pilot'] .'">'. 
							Output::htmlEncodeString($value['pilot']) .'</td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?=gmdate('Y-m-d H:i:s', strtotime("now"))?> EVE<br /><br />
			<strong>Total this period: <?=$ctrtotact?></strong><br />
			Open: <?=$ctropen?><br />
			Rescued (ESRC): <?=$ctresrc?><br />
			Rescued (SAR): <?=$ctrrescued?><br />
			Escaped by self: <?=$ctrescaped?><br />
			Escaped by locals: <?=$ctrescapedlocals?><br />
			Self-destruct: <?=$ctrdestruct?><br />
			Destroyed by others: <?=$ctrdestroyed?><br />
			No response: <?=$ctrnoresponse?><br />
			Declined/illegitimate: <?=$ctrdeclined?>
		</div>
	</div>
<?php
}

//show payout data if "Payout" is checked
else {	
	//count of all actions performed in the specified period
	$db->query("SELECT COUNT(*) as cnt FROM rescuerequest 
				WHERE status <> 'closed-dup' AND requestdate BETWEEN :start AND :end");
	$db->bind(':start', $start);
	$db->bind(':end', $end);
	$row = $db->single();

	$ctrtot = $row['cnt'];	?>

	<div class="row" id="systable">
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
					$db->query("SELECT startagent, COUNT(*) as cnt FROM rescuerequest 
								WHERE status <> 'closed-dup'
								AND requestdate BETWEEN :start AND :end 
								GROUP BY startagent");
					$db->bind(':start', $start);
					$db->bind(':end', $end);
					$rows = $db->resultset();
					$ctr = 0;
					foreach ($rows as $value) {
						$ctr++;
						echo '<tr>';
						echo '<td><a class="payout" target="_blank" 
								href="https://evewho.com/pilot/'. $value['startagent'] .'">'. 
								Output::htmlEncodeString($value['startagent']) .'</td>';
						echo '<td class="white" align="right">'. $value['cnt'] .'</td>';
						echo '<td><input type="text" id="amt'.$ctr.'" value="'. 
									intval($value['cnt'])*1000000 .'" />
									<i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy(\'amt'.$ctr.'\')"></i>
							  </td>';
						echo '</tr>';
					}
					?>
					<tr>
						<td class="white" align="right">Participants: <?php echo $ctr; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL: </td>
						<td class="white" align="right"><?php echo $ctrtot; ?></td>
						<td></td>
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
