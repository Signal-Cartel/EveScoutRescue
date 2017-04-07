<?php include_once '../includes/auth-alliance-req.php'; ?>
<html>

<head>
	<?php 
	include_once '../includes/bg.php';
	$pgtitle = 'Search';
	include_once '../includes/head.php'; 
	?>
	<script>
        $(document).ready(function() {
            $('input.targetsystem').typeahead({
                name: 'targetsystem',
                remote: 'activecaches.php?query=%QUERY'
            });
        })
    </script>
</head>

<?php
require_once '../class/db.class.php';

if(isset($_POST['targetsystem'])) { 
	$targetsystem = filter_var($_POST['targetsystem'], FILTER_SANITIZE_STRING);
}
elseif (isset($_GET['system'])) {
	$targetsystem = htmlspecialchars($_GET["system"]);
}
?>
<body>
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
		<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<div class="form-group">
				<input type="text" name="targetsystem" size="30" autoFocus="autoFocus" class="targetsystem" placeholder="System Name" value="<?php echo isset($targetsystem) ? $targetsystem : '' ?>">
			</div>
			<button type="submit" class="btn btn-lg">Search</button>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="data_entry.php" class="btn btn-info" role="button">Go to Data Entry</a>
		</form>
		<div class="clearit">
			<span class="white">If a system is not listed, no active cache is present.</span>
		</div>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<?php
// display result for the selected system
if (isset($targetsystem)):
	$db = new Database();
	$db->query("SELECT * FROM cache WHERE System = :system AND Status <> 'Expired'");
	$db->bind(':system', $targetsystem);
	$row = $db->single();
	
	//only display the following if we got some results back
	if (!empty($row)):	
		echo '<div class="row" id="systableheader">';
		echo '<div class="col-sm-12">';
		echo '<div style="padding-left: 10px;">';
		//TEND button
		echo '<a href="data_entry.php?tendsys='.$targetsystem.'" class="btn btn-success" role="button">Tend</a>&nbsp;&nbsp;&nbsp;';
		//ADJUNCT button
		echo '<a href="data_entry.php?adjsys='.$targetsystem.'" class="btn btn-warning" role="button">Adjunct</a>&nbsp;&nbsp;&nbsp;';
		//TW button
		echo '<a href="https://tripwire.eve-apps.com/?system=' . $targetsystem . '" class="btn btn-info" role="button" target="_blank">Tripwire</a>&nbsp;&nbsp;&nbsp;';
		//ww.pasta.gg button
		echo '<a href="http://wh.pasta.gg/' . $targetsystem . '" class="btn btn-info" role="button" target="_blank">ww.pasta.gg</a>&nbsp;&nbsp;&nbsp;';
		//"clear result" link
		echo '<a href="?" class="btn btn-link" role="button">clear result</a>';
		echo '</div></div></div>'; ?>
		
		<div class="row" id="systable">
			<div class="col-sm-12">
				<!-- DETAIL RECORD -->
				<table class="table" style="width: auto;">
					<thead>
						<tr>
							<th class="white">Sown On</th>
							<th class="white">Location</th>
							<th class="white">Aligned With</th>
							<th class="white">Distance</th>
							<th class="white">Password</th>
							<th class="white">Status</th>
							<th class="white">Expires On</th>
						</tr>
					</thead>
					<tbody>
					<?php
					echo '<tr>';
					echo '<td class="white">'. date("Y-M-d", strtotime($row['InitialSeedDate'])) .'</td>';
					echo '<td class="white">'. $row['Location'] .'</td>';
					echo '<td class="white">'. $row['AlignedWith'] .'</td>';
					echo '<td class="white">'. $row['Distance'] .'</td>';
					echo '<td class="white">'. $row['Password'] .'</td>';
					$statuscellformat = '';
					if ($row['Status'] == 'Healthy') { $statuscellformat = ' style="background-color:green;color:white;"'; }
					if ($row['Status'] == 'Upkeep Required') { $statuscellformat = ' style="background-color:yellow;"'; }
					echo '<td'.$statuscellformat.'>'. $row['Status'] .'</td>';
					echo '<td class="white">'. date("Y-M-d", strtotime($row['ExpiresOn'])) .'</td>';
					echo '</tr>';
					$strNotes = $row['Note'];
					?>
					</tbody>
				</table>
			</div>
		</div>
		<?php if (!empty($strNotes)): ?>
		<div class="ws"></div>
		<div class="row" id="sysnotes">
			<div class="col-sm-12">
				<!-- DETAIL RECORD NOTE(S) -->
				<table class="table" style="width: auto;">
					<thead>
						<tr>
							<th class="white">Notes</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white"><?php echo $strNotes; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php endif; //if (!empty($strNotes))
	//no results returned, so give an option to sow a new cache in this system
	else:
		echo '<div class="row" id="systableheader">';
		echo '<div class="col-sm-12">';
		echo '<div style="padding-left: 10px;">';
		//SOW button
		echo '<span class="white">No cache exists for this system.</span>&nbsp;&nbsp;&nbsp;';
		echo '<a href="data_entry.php?sowsys='.$targetsystem.'" class="btn btn-success" role="button">Sow one now</a>&nbsp;&nbsp;&nbsp;';
		//"clear result" link
		echo '<a href="?" class="btn btn-link" role="button">clear result</a>';
		echo '</div></div></div>';
	endif; //(!empty($row))

// no system selected, so show active cache list and summary stats
else: ?>
<div class="row" id="allsystable">
	<div class="col-sm-2">
		<!-- ACTIVE CACHE LIST -->
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">System</th>
					<th class="white">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$db = new Database();
				$db->query("SELECT System, Status FROM cache WHERE Status <> 'Expired' ORDER BY System");
				$rows = $db->resultset();
				
				$strNotes = '';
			
				foreach ($rows as $value) {
				  echo '<tr>';
				  echo '<td style="background-color: #cccccc;"><a href="?system='. $value['System'] .'">'. $value['System'] .'</a></td>';
				  $statuscellformat = '';
				  if ($value['Status'] == 'Healthy') { $statuscellformat = ' style="background-color:green;color:white;"'; }
				  if ($value['Status'] == 'Upkeep Required') { $statuscellformat = ' style="background-color:yellow;"'; }
				  echo '<td'.$statuscellformat.'>'. $value['Status'] .'</td>';
				  echo '</tr>';
				} 
				?>
			</tbody>
		</table>
	</div>
	<!-- LEADER BOARDS -->
	<div class="col-sm-4 white">
		<span class="sechead"><span style="font-weight: bold;">LEADER BOARD</span><br /><br />
		Current Week (Sunday through Saturday)</span>
		<!-- CURRENT WEEK LEADERBOARD -->
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">Pilot</th>
					<th class="white">Total Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$start = date('Y-m-d', strtotime('last Sunday', strtotime("now")));
				$end = date('Y-m-d', strtotime("tomorrow"));
			
				$db->query("SELECT COUNT(*) AS cnt, Pilot 
					FROM activity 
					WHERE ActivityDate BETWEEN :start AND :end
					GROUP BY Pilot
					ORDER BY cnt DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				
				$ctr = 0;
	
				foreach ($rows as $value) {
					$ctr++;
					echo '<tr>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td class="white" align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
					//display only top 3 records
					if (intval($ctr) == 3) { break; }
				}
				?>
			</tbody>
		</table>
		<br />
		<span class="sechead">Last 30 days</span>
		<!-- LAST 30 DAYS LEADERBOARD -->
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">Pilot</th>
					<th class="white">Total Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$start = date('Y-m-d', strtotime('-30 days'));
				$end = date('Y-m-d', strtotime("tomorrow"));
				$db->query("SELECT COUNT(*) AS cnt, Pilot
					FROM activity
					WHERE ActivityDate BETWEEN :start AND :end
					GROUP BY Pilot
					ORDER BY cnt DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				
				$ctr = 0;
				foreach ($rows as $value) {
					$ctr++;
					echo '<tr>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td class="white" align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
					//display only top 3 records
					if (intval($ctr) == 3) { break; }
				}
			?>
			</tbody>
		</table>
		<br />
		<span class="sechead">All Time (as of 2017-Mar-18)</span>
		<!-- ALL TIME LEADERBOARD -->
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">Pilot</th>
					<th class="white">Total Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$db->query("SELECT COUNT(*) AS cnt, Pilot
					FROM activity
					GROUP BY Pilot
					ORDER BY cnt DESC");
				$rows = $db->resultset();
				
				$ctr = 0;
				foreach ($rows as $value) {
					$ctr++;
					echo '<tr>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td class="white" align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
					//display only top 3 records
					if (intval($ctr) == 3) { break; }
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4 white">
		<!-- HALL OF HELP -->
		<span class="sechead"><span style="font-weight: bold;">HALL OF HELP</span><br /><br />
		All participants, last 60 days<br />Most recent first</span>
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">Pilot</th>
					<th class="white">Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$start = date('Y-m-d', strtotime('-30 days'));
				$end = date('Y-m-d', strtotime("tomorrow"));
				$db->query("SELECT Pilot, max(ActivityDate) as maxdate FROM activity 
					WHERE ActivityDate BETWEEN :start AND :end
					GROUP BY Pilot ORDER BY maxdate DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				
				foreach ($rows as $value) {
					//display records for only the last 60 days
					if (strtotime($value['maxdate']) > strtotime('-60 day')) {
						echo '<tr>';
						echo '<td class="white">'. $value['Pilot'] .'</td>';
						echo '<td class="white">'. date("Y-M-d", strtotime($value['maxdate'])) .'</td>';
						echo '</tr>';
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- TOTAL ACTIVE CACHES & ALL ACTIONS -->
	<div class="col-sm-2 white">
		<?php
		$db->query("SELECT COUNT(*) as cnt FROM cache WHERE Status <> 'Expired'");
		$row = $db->single();
		$ctractive = $row['cnt'];
		
		$db->query("SELECT COUNT(*) as cnt FROM activity");
		$row = $db->single();
		$ctrtot = $row['cnt'];
		?>
		<span class="sechead"><span style="font-weight: bold;">Active caches:</span><br />
		<?php echo $ctractive; ?> of 2603 (<?php echo round((intval($ctractive)/2603)*100,1); ?>%)</span>
		<br /><br />
		<span class="sechead" style="font-weight: bold;">All actions: <?php echo $ctrtot; ?></span><br />
		(as of 2017-Mar-18)
	</div>
</div>
<?php endif; //if (isset($targetsystem))?>
</div>
</body>
</html>