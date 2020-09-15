<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$db = new Database();
$lb = new LeaderboardSAR($db);
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
if (!isset($_POST['payout'])) {	//show detailed records if "Payout" is not checked	?>

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
				$rows = $lb->getSARDispatchPayees($start, $end, false);
				foreach ($rows as $value) {	?>
					
					<tr>
						<td class="white text-nowrap">
							<?=date("Y-m-d", strtotime($value['requestdate']))?></td>
						<td class="text-nowrap">
							<a class="payout" target="_blank" href="/esrc/personal_stats.php?pilot= 
								<?=urlencode($value['startagent'])?>"><?=$value['startagent']?></a> 
							- <a class="payout" target="_blank" 
								href="https://evewho.com/pilot/<?=$value['startagent']?>">EW</a></td>
						<td class="white"><?=ucfirst($value['status'])?></td>
						<td>
							<a class="payout" href="/esrc/rescueoverview.php?sys=<?=$value['system']?>" 
								target="_blank"><?=$value['system']?></a></td>
						<td><a class="payout" target="_blank" 
								href="https://evewho.com/pilot/<?=$value['pilot']?>">
								<?=Output::htmlEncodeString($value['pilot'])?></td>
					</tr>

					<?php
				}	?>

				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<table class="table table-condensed" style="width: auto;">
				<caption>TOTALS</caption>
				<tr>
					<td class="white">Rescued (ESRC)</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-esrc')?></td>
				</tr>
				<tr>
					<td class="white">Rescued (SAR)</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-rescued')?></td>
				</tr>
				<tr>
					<td class="white">Open</td>
					<td class="white text-right"><?=(countStatus($rows, 'pending')) + (countStatus($rows, 'open')) + (countStatus($rows, 'system-located'))?></td>
				</tr>
				<tr>
					<td class="white">Escaped (self)</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-escaped')?></td>
				</tr>
				<tr>
					<td class="white">Escaped (w/ help)</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-escapedlocals')?></td>
				</tr>
				<tr>
					<td class="white">Self-destruct</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-destruct')?></td>
				</tr>
				<tr>
					<td class="white">Destroyed</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-destroyed')?></td>
				</tr>
				<tr>
					<td class="white">No response</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-noresponse')?></td>
				</tr>
				<tr>
					<td class="white">Declined</td>
					<td class="white text-right"><?=countStatus($rows, 'closed-declined')?></td>
				</tr>
				<tr>
					<td class="white">TOTAL</td>
					<td class="white text-right"><?=count($rows)?></td>
				</tr>
			</table>
		</div>
	</div>

<?php
}
else {	// show payout data if "Payout" is checked	?>

	<div class="row" id="systable">
		<div class="col-sm-12">
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
				$rows = $lb->getSARDispatchPayees($start, $end, true);
				$ctr = 0;
				foreach ($rows as $value) {
					$ctr++;	?>

					<tr>
						<td><a class="payout" target="_blank" 
							href="https://evewho.com/pilot/<?=$value['startagent']?>"> 
							<?=Output::htmlEncodeString($value['startagent'])?></a></td>
						<td class="white text-right"><?=$value['cnt']?></td>
						<td>
							<input type="text" id="amt<?=$ctr?>" 
								value="<?=intval($value['cnt'])*1000000?>" />
							<i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy('amt<?=$ctr?>')"></i>
						</td>
					</tr>

					<?php
				}	?>

					<tr>
						<td class="white text-right">
							Participants: <?=count($rows)?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL: </td>
						<td class="white text-right"><?=array_sum(array_column($rows, 'cnt'))?></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

<?php
}

function countStatus($array, $cntValue)
{
	if (in_array($cntValue, array_column($array, 'status'))) {
		return array_count_values(array_column($array, 'status'))[$cntValue];
	}
	else {
		return 0;
	}
}


// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
