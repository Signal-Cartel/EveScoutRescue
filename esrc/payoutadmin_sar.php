<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);


include_once '../includes/auth-inc.php';
require_once '../class/output.class.php';

if (!isset($_POST['start'])) {
	if (gmdate('w', strtotime("now")) == 0) {
		$start = gmdate('Y-m-d', strtotime("now"));
	}
	elseif (gmdate('w', strtotime("now")) == 1) {
		$start= gmdate('Y-m-d', strtotime("- 1 day"));
	}
	else {
		$start = gmdate('Y-m-d', gmdate(strtotime('last Sunday')));
	}
}

if (!isset($_POST['end'])) {
	$end = gmdate('Y-m-d', strtotime("+ 1 day"));
}

?>
<html>

<head>
	<?php
	$pgtitle = 'Payout Admin';
	include_once '../includes/head.php'; 
	?>
	<style>
	<!--
		table {
			table-layout: fixed;
			word-wrap: break-word;
		}
		a,
		a:visited,
		a:hover {
			color: aqua;
		}
	-->
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#example').DataTable( {
		        "order": [[ 0, "desc" ]],
		        "pagingType": "full_numbers",
		        "pageLength": 15
		    } );
		} );
	</script>
</head>

<?php
require_once '../class/db.class.php';
if (isset($_POST['start']) && isset($_POST['end'])) { 
	$start = htmlspecialchars_decode(date("Y-m-d", strtotime($_POST['start'])));
	$end = htmlspecialchars_decode(date("Y-m-d", strtotime($_POST['end'])));
}
?>
<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Payouts: 
				<a href="payoutadmin.php">ESRC</a> &gt;&gt; 
				SAR Dispatch &gt;&gt; 
				<a href="payoutadmin_sar_rescue.php">SAR Locate/Rescue</a></span><br />
			<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				<div class="input-daterange input-group" id="datepicker">
					<input type="text" class="input-sm form-control" name="start" value="<?php echo isset($start) ? $start : '' ?>" />
					<span class="input-group-addon">to</span>
					<input type="text" class="input-sm form-control" name="end" value="<?php echo isset($end) ? $end : '' ?>" />
				</div>
				<div class="checkbox">
					<label class="white"><input type="checkbox" name="details" value="yes"> Payout</label>
				</div>
				&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-sm">Search</button>
			</form>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	// display results for the selected date range
	$db = new Database();
		
	//show detailed records if "Payout" is not checked
	if (!isset($_POST['details']) && $_POST['details'] != 'yes') {	
	?>
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
						<!-- <th class="white">Status</th> -->
					</tr>
				</thead>
				<tbody>
				<?php
				$ctrtotact = $ctropen = $ctrrescued = $ctrescaped = $ctrescapedlocals = 0;
				$ctrdestruct = $ctrdestroyed = $ctrnoresponse = $ctrdeclined = 0;
				// pull all start agents aside from ESRC Agents and duplicates
				$db->query("SELECT requestdate, startagent, status, system, pilot
							FROM rescuerequest
							WHERE status NOT IN ('closed-esrc', 'closed-dup') 
								AND requestdate BETWEEN :start AND :end
							ORDER BY requestdate DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					$ctrtotact++;
					// calculate action cell format
					$actioncellformat= '';
					/*
					switch ($value['EntryType']) {
						case 'sower':
							$actioncellformat = ' style="background-color:#ccffcc;color:black;"';
							break;
						case 'tender':
							$actioncellformat= ' style="background-color:#d1dffa;color:black;"';
							break;
						case 'agent':
							$actioncellformat= ' style="background-color:#fffacd;color:black;"';
							break;
						default:
							// ??
					}
					*/
					echo '<tr>';
					// add 4 hours to convert to UTC (EVE) for display
					echo '<td class="white text-nowrap">'. 
							date("Y-m-d", strtotime($value['requestdate'])) .
						 '</td>';
					echo '<td class="text-nowrap">
							<a target="_blank" href="personal_stats.php?pilot='. 
								urlencode($value['startagent']) .'">'. 
								$value['startagent'] .'</a> - <a target="_blank" 
								href="https://evewho.com/pilot/'. $value['startagent'] .'">EG</a></td>';
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
					echo '<td><a href="rescueoverview.php?sys='. $value['system'] .'" target="_blank">'. 
							$value['system'] .'</a></td>';
					echo '<td><a target="_blank" 
							href="https://evewho.com/pilot/'. $value['pilot'] .'">'. 
							Output::htmlEncodeString($value['pilot']) .'</td>';
					//echo '<td class="white">'. Output::htmlEncodeString($value['Note']) .'</td>';
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
			Rescued: <?=$ctrrescued?><br />
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
					WHERE status NOT IN ('closed-esrc', 'closed-dup')
					AND requestdate BETWEEN :start AND :end");
		$db->bind(':start', $start);
		$db->bind(':end', $end);
		$row = $db->single();

		$ctrtot = $row['cnt'];
	?>
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
								WHERE status NOT IN ('closed-esrc', 'closed-dup')
								AND requestdate BETWEEN :start AND :end GROUP BY startagent");
					$db->bind(':start', $start);
					$db->bind(':end', $end);
					$rows = $db->resultset();
					$ctr = 0;
					foreach ($rows as $value) {
						$ctr++;
						echo '<tr>';
						echo '<td><a target="_blank" 
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
?>
</div>

<script type="text/javascript">
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}
</script>

</body>
</html>