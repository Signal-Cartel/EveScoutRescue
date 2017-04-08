<?php include_once '../includes/auth-inc.php'; ?>
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
	-->
	</style>
</head>

<?php
require_once '../class/db.class.php';
if (isset($_POST['start']) && isset($_POST['end'])) { 
	$start = htmlspecialchars(date("Y-m-d", strtotime($_POST['start'])));
	$end = htmlspecialchars(date("Y-m-d", strtotime($_POST['end'])));
}
if (isset($_POST['details']) && $_POST['details'] == 'yes') {
	$checked = ' checked="checked"';
}
?>
<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">ESRC Payout Admin</span>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="data_entry.php" class="btn btn-info" role="button">Go to Data Entry</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="search.php" class="btn btn-info" role="button">Go to Search</a><br /><br />
			<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				<div class="input-daterange input-group" id="datepicker">
					<input type="text" class="input-sm form-control" name="start" value="<?php echo isset($start) ? $start : '' ?>" />
					<span class="input-group-addon">to</span>
					<input type="text" class="input-sm form-control" name="end" value="<?php echo isset($end) ? $end : '' ?>" />
				</div>
				<div class="checkbox">
					<label class="white"><input type="checkbox" name="details" value="yes"<?php echo (isset($checked)) ? $checked : '' ?>> Details</label>
				</div>
				&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-sm">Search</button>
			</form>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	// display results for the selected date range
	if (isset($_POST['start']) && isset($_POST['end'])) { 
		$db = new Database();
		//show detailed records if "Details" is checked
		if (isset($_POST['details']) && $_POST['details'] == 'yes') {	
	?>
	<div class="row" id="systable">
		<div class="col-sm-10">
			<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th class="white" style="width: 12%;">Date</th>
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
				$db->query("SELECT * FROM activity WHERE ActivityDate BETWEEN :start AND :end ORDER By ActivityDate DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					$ctrtotact++;
					echo '<tr>';
					echo '<td class="white">'. date("Y-M-d", strtotime($value['ActivityDate'])) .'</td>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td class="white">'. $value['EntryType'] .'</td>';
					switch ($value['EntryType']) {
						case 'sower':
							$ctrsow++;
							break;
						case 'tender':
							$ctrtend++;
							break;
						case 'adjunct':
							$ctradj++;
							break;
					}
					echo '<td style="background-color: #cccccc;"><a href="search.php?system='. $value['System'] .'" target="_blank">'. $value['System'] .'</a></td>';
					echo '<td class="white">'. htmlspecialchars($value['AidedPilot']) .'</td>';
					echo '<td class="white">'. htmlspecialchars($value['Note']) .'</td>';
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
			Actions this period: <?php echo $ctrtotact; ?><br />
			Sowed: <?php echo $ctrsow; ?><br />
			Tended: <?php echo $ctrtend; ?><br />
			Adjunct: <?php echo $ctradj; ?><br /><br />
			Total caches in space:<br />
			<?php echo $ctrtot; ?> of 2603 (<?php echo round((intval($ctrtot)/2603)*100,1); ?>%)
		</div>
	</div>
	<?php
	}
	//show summary data if "Details" is NOT checked
	else {	
		//count of all actions performed in the specified period
		$db->query("SELECT COUNT(*) as cnt FROM activity WHERE ActivityDate BETWEEN :start AND :end");
		$db->bind(':start', $start);
		$db->bind(':end', $end);
		$row = $db->single();

		$ctrtot = $row['cnt'];
	?>
	<div class="row" id="systable">
		<div class="col-sm-12">
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
		$db->query("SELECT Pilot, COUNT(*) as cnt FROM activity WHERE ActivityDate BETWEEN :start AND :end GROUP BY Pilot");
		$db->bind(':start', $start);
		$db->bind(':end', $end);
		$rows = $db->resultset();
		$ctr = 0;
		foreach ($rows as $value) {
			$ctr++;
			echo '<tr>';
			echo '<td><input type="text" value="'. $value['Pilot'] .'" /></td>';
			echo '<td class="white" align="right">'. $value['cnt'] .'</td>';
			echo '<td><input type="text" value="'. round((intval($value['cnt'])/intval($ctrtot))*500000000,2) .'" /></td>';
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
	</div>
<?php
	}
}
?>
	</div>
</body>
</html>