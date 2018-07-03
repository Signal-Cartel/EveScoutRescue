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

		// refresh page every 10 minutes in order to stay logged in
		var time = new Date().getTime();
	    $(document.body).bind("mousemove keypress", function(e) {
	         time = new Date().getTime();
	    });

	    function refresh() {
	         if(new Date().getTime() - time >= 600000) 
	             window.location.reload(true);
	         else 
	             setTimeout(refresh, 10000);
	    }

	    setTimeout(refresh, 10000);
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
				ESRC &gt;&gt; 
				<a href="payoutadmin_sar.php">SAR Dispatch</a> &gt;&gt; 
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
						<th class="white" style="width: 35%;">Note</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$ctrtotact = $ctrsow = $ctrtend = $ctradj = 0;
				$db->query("SELECT * FROM activity 
							WHERE ActivityDate BETWEEN :start AND :end 
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
						case 'agent':
							$actioncellformat= ' style="background-color:#fffacd;color:black;"';
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
							<a target="_blank" href="personal_stats.php?pilot='. urlencode($value['Pilot']) .'">'. 
							$value['Pilot'] .'</a> - <a target="_blank" 
							href="https://evewho.com/pilot/'. $value['Pilot'] .'">EW</a></td>';
					echo '<td class="white" '. $actioncellformat .'>'. ucfirst($value['EntryType']) .'</td>';
					switch ($value['EntryType']) {
						case 'sower':
							$ctrsow++;
							break;
						case 'tender':
							$ctrtend++;
							break;
						case 'agent':
							$ctradj++;
							break;
					}
					echo '<td><a href="search.php?sys='. $value['System'] .'" target="_blank">'. 
							$value['System'] .'</a></td>';
					echo '<td><a target="_blank" 
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
			Sowed: <?php echo $ctrsow; ?><br />
			Tended: <?php echo $ctrtend; ?><br />
			Agent: <?php echo $ctradj; ?><br /><br />
			Total caches in space:<br />
			<?php echo $ctrtot; ?> of 2603 (<?php echo round((intval($ctrtot)/2603)*100,1); ?>%)
		</div>
	</div>
	<?php
	}
	//show payout data if "Payout" is checked
	else {	
		//count of all actions performed in the specified period
		$db->query("SELECT COUNT(*) as cnt FROM activity WHERE ActivityDate BETWEEN :start AND :end");
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
					$db->query("SELECT Pilot, COUNT(*) as cnt FROM activity WHERE ActivityDate 
									BETWEEN :start AND :end GROUP BY Pilot");
					$db->bind(':start', $start);
					$db->bind(':end', $end);
					$rows = $db->resultset();
					$ctr = 0;
					foreach ($rows as $value) {
						$ctr++;
						echo '<tr>';
						echo '<td><a target="_blank" href="personal_stats.php?pilot='. urlencode($value['Pilot']) .'">'. 
							$value['Pilot'] .'</a> - <a target="_blank" 
							href="https://evewho.com/pilot/'. $value['Pilot'] .'">EW</a></td>';
						echo '<td class="white" align="right">'. $value['cnt'] .'</td>';
						echo '<td><input type="text" id="amt'.$ctr.'" value="'. 
									round((intval($value['cnt'])/intval($ctrtot))*500000000,2) .'" />
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