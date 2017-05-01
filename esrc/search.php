<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

function getShortEVEdate($origdate)
{
	$eveyear = intval(date("Y", strtotime($origdate)))-1898;
	
	$result = 'YC'. $eveyear .'-'. date("M-d", strtotime($origdate .'+ 4 hours'));
	
	return $result;
}

?>
<html>

<head>
	<?php 
	$pgtitle = 'Search';
	include_once '../includes/head.php'; 
	?>
	<script>
        $(document).ready(function() {
            $('input.targetsystem').typeahead({
                name: 'targetsystem',
                remote: '../data/typeahead.php?type=system&query=%QUERY'
            });
        })
    </script>
</head>

<?php
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/caches.class.php';
require_once '../class/systems.class.php';

$database = new Database();

// create a cache object instance
$caches = new Caches($database);

if(isset($_REQUEST['targetsystem'])) { 
	$targetsystem = htmlspecialchars($_REQUEST['targetsystem']);
}
elseif (isset($_REQUEST['system'])) {
	$targetsystem = htmlspecialchars($_REQUEST["system"]);
}
?>
<body class="white">
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 black" style="text-align: center;">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-5" style="text-align: left;">
				<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
					<div class="form-group">
						<input type="text" name="targetsystem" size="30" autoFocus="autoFocus" 
							autocomplete="off" class="targetsystem" placeholder="System Name" 
							value="<?php echo isset($targetsystem) ? $targetsystem : '' ?>">
					</div>
					<div class="clearit">
						<button type="submit" class="btn btn-md">Search</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="data_entry.php" class="btn btn-info" role="button">Go to Data Entry</a>
					</div>
				</form>
			</div>
			<div class="col-sm-4"></div>
		</div>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<?php
// check if a system is supplied
if (isset($targetsystem)) {
	// display result for the selected system
	// get cache information from database
	$row = $caches->getCacheInfo($targetsystem);
	//only display the following if we got some results back
	if (!empty($row))
	{
		// calculate status cell format
		$statuscellformat = '';
		if ($row ['Status'] == 'Healthy') {
			$statuscellformat = ' style="background-color:green;color:white;"';
		}
		if ($row ['Status'] == 'Upkeep Required') {
			$statuscellformat = ' style="background-color:yellow;color:black;"';
		}
		// save notes as separate var
		$strNotes = $row['Note'];
		?>
		<div class="row" id="systableheader">
		<div class="col-sm-12">
		<div style="padding-left: 10px;">
		<!-- TEND button -->
		<?php if ($caches->isTendingAllowed($targetsystem)) { ?>
		<a href="data_entry.php?tendsys=<?=$targetsystem?>" class="btn btn-success" role="button">Tend</a>&nbsp;&nbsp;&nbsp;
		<?php } else { ?>
		<span class="white"><b>No tending needed</b></span>&nbsp;&nbsp;&nbsp;
		<?php  } ?>
		<!-- ADJUNCT button -->
		<a href="data_entry.php?adjsys=<?=$targetsystem?>" class="btn btn-warning" role="button">Adjunct</a>&nbsp;&nbsp;&nbsp;
		<!-- TW button -->
		<a href="https://tripwire.eve-apps.com/?system=<?=$targetsystem?>" class="btn btn-info" role="button" target="_blank">Tripwire</a>&nbsp;&nbsp;&nbsp;
		<!-- anoik.is button -->
		<a href="http://anoik.is/systems/<?=$targetsystem?>" class="btn btn-info" role="button" target="_blank">anoik.is</a>&nbsp;&nbsp;&nbsp;
		<!-- clear result" link -->
		<a href="?" class="btn btn-link" role="button">clear result</a>
		</div>
		</div>
		</div>
		
		<div class="row" id="systable">
			<div class="col-sm-12">
				<!-- DETAIL RECORD -->
				<table class="table" style="width: auto;">
					<thead>
						<tr>
							<th>Sown On</th>
							<th>Location</th>
							<th>Aligned With</th>
							<th>Distance</th>
							<th>Password</th>
							<th>Status</th>
							<th>Expires On</th>
						</tr>
					</thead>
					<tbody>
					<tr>
					<td><?=getShortEVEdate($row['InitialSeedDate'])?></td>
					<td><?=$row['Location']?></td>
					<td><?=$row['AlignedWith']?></td>
					<td><?=htmlspecialchars_decode($row['Distance'])?></td>
					<td><?=htmlspecialchars_decode($row['Password'])?></td>
					<td<?=$statuscellformat ?>><?=$row['Status']?></td>
					<td><?=getShortEVEdate($row['ExpiresOn'])?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php if (!empty($strNotes)) { ?>
		<div class="ws"></div>
		<div class="row" id="sysnotes">
			<div class="col-sm-12">
				<!-- DETAIL RECORD NOTE(S) -->
				<table class="table" style="width: auto;">
					<thead>
						<tr>
							<th>Notes</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?= htmlspecialchars_decode($strNotes) ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php } //if (!empty($strNotes))
	}
	else
	{
		$systems = new Systems($database);
		//no results returned, so give an option to sow a new cache in this system
		// check if the length of the string matches the worm hole names
		// 		if (strlen($targetsystem) === 7)
		if ($systems->validatename($targetsystem) === 0)
		{
			$lockedDate = $systems->locked($targetsystem);
			
			if (!isset($lockedDate))
			{
				// yes, create a link to the data entry page
				?>
			<div class="row" id="systableheader">
			<div class="col-sm-12">
			<div style="padding-left: 10px;">
			<!-- SOW button  -->
			<span class="sechead white">No cache exists for this system.</span>&nbsp;&nbsp;&nbsp;
			<a href="data_entry.php?sowsys=<?=$targetsystem?>" class="btn btn-success btn-lg" role="button">Sow one now</a>&nbsp;&nbsp;&nbsp;
	
			<a href="?" class="btn btn-link" role="button">clear result</a>
			</div></div></div>
		<?php
			}
			else
			{
				?>	
			<div class="row" id="systableheader">
			<div class="col-sm-12">
			<div style="padding-left: 10px;">
				<span class="sechead white">Upon request of the current wormhole residents, 
					caches are not to be sown in <?=$targetsystem?> until 
					<?=date("Y-M-d", strtotime($lockedDate))?>.
				</span>
				<a href="?" class="btn btn-link" role="button">clear result</a>
			</div></div></div>
				<?php
			}
		}
		else 
		{
			// wrong system name length
			?>
			<div class="row" id="systableheader">
			<div class="col-sm-12">
			<div style="padding-left: 10px;">
			<!-- SOW button  -->
			<span class="sechead white">'<?=$targetsystem?>' is not a valid system name. 
				Please correct name and resubmit.&nbsp;&nbsp;&nbsp;</span>
				<a href="?" class="btn btn-link" role="button">clear result</a>
			</div></div></div>
		<?php
		}
	} //(!empty($row))
	
	// see if there is historical data to display for this system
	$database->query("SELECT * FROM activity
						WHERE System = :system
						ORDER By ActivityDate DESC");
	$database->bind(':system', $targetsystem);
	$rows = $database->resultset();
	$database->closeQuery();
	if (!empty($rows)) {
		echo '<div class="row" id="historytable">';
		echo '<div class="col-sm-12">';
		echo '<div style="padding-left: 10px;">';
		echo '<br /><span class="sechead">HISTORY</span><br />';
		echo '<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Sown/Tended</th>
						<th class="white">Pilot</th>
						<th class="white">Type</th>
						<th class="white">Location</th>
						<th class="white">Aligned With</th>
						<th class="white">Distance</th>
						<th class="white">Expires On</th>
						<th class="white">Note</th>
					</tr>
				</thead>
				<tbody>';
		foreach ($rows as $value) {
			//get cache table data for sower records
			$sowrow = '';
			if ($value['EntryType'] == 'sower') {
				$database->query("SELECT * FROM cache WHERE CacheID = :id");
				$database->bind(':id', $value['ID']);
				$sowrow = $database->single();
				$database->closeQuery();
			}
			echo '<tr>';
			switch ($value['EntryType']) {
				case 'sower':
					$actioncellformat = ' style="background-color:#ccffcc;color:black;"';
					break;
				case 'tender':
					$actioncellformat= ' style="background-color:#d1dffa;color:black;"';
					break;
				case 'adjunct':
					$actioncellformat= ' style="background-color:#fffacd;color:black;"';
					break;
				default:
					// ??
			}
			echo '<tr>';
			// add 4 hours to convert to UTC (EVE) for display
			$rowdate = (!empty($sowrow)) ? $sowrow['InitialSeedDate'] : $value['ActivityDate'];
			echo '<td class="white text-nowrap">'. getShortEVEdate($rowdate) .'</td>';
			echo '<td class="text-nowrap">'. $value['Pilot'] .'</td>';
			echo '<td class="white" '. $actioncellformat .'>'. $value['EntryType'] .'</td>';
			$rowLoc = (!empty($sowrow)) ? $sowrow['Location'] : '';
			echo '<td class="text-nowrap">'. $rowLoc .'</td>';
			$rowAW = (!empty($sowrow)) ? $sowrow['AlignedWith'] : '';
			echo '<td class="text-nowrap">'. $rowAW .'</td>';
			$rowDist = (!empty($sowrow)) ? $sowrow['Distance'] : '';
			echo '<td class="text-nowrap">'. $rowDist.'</td>';
			$rowExp = (!empty($sowrow)) ? getShortEVEdate($sowrow['ExpiresOn']) : '';
			echo '<td class="text-nowrap">'. $rowExp.'</td>';
			echo '<td class="white">'. htmlspecialchars_decode($value['Note']) .'</td>';
			echo '</tr>';
			echo '</tr>';
		}
		echo '</tbody>
			</table>';
		echo '</div></div></div>';
	}
	
// no system selected, so show summary stats
}
else {
?>
<div class="row" id="allsystable">
	
	<?php 
		$leaderBoard = new Leaderboard($database);
		$systems = new Systems($database);
	?>
	
	<!-- LEADER BOARDS -->
	<div class="col-sm-4 white">
		<span class="sechead"><span style="font-weight: bold;">LEADER BOARD</span><br /><br />
		Current Week (Sunday through Saturday)</span>
		<!-- CURRENT WEEK LEADERBOARD -->
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th>Pilot</th>
					<th>Total Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$rows = $leaderBoard->getTopPilotsWeek(3);				
	
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td>'. $value['Pilot'] .'</td>';
					echo '<td align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
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
					<th>Pilot</th>
					<th>Total Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$rows = $leaderBoard->getTopLastDays(5, 30);

				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td>'. $value['Pilot'] .'</td>';
					echo '<td align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
		<br />
		<span class="sechead">All Time</span>
		<!-- ALL TIME LEADERBOARD -->
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th>Pilot</th>
					<th>Total Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$rows = $leaderBoard->getAllHigh(10);
				
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td>'. $value['Pilot'] .'</td>';
					echo '<td align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-4 white">
		<!-- HALL OF HELP -->
		<span class="sechead"><span style="font-weight: bold;">HALL OF HELP</span><br /><br />
		All participants, last 30 days<br />Most recent first</span>
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th>Pilot</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$rows = $leaderBoard->getActivePilots(30);
				foreach ($rows as $value) {
					//prepare stats link
					$pilot = $value['Pilot'];
					$ptxt = $pilot;
					$pformat = '';
					if ($pilot == $charname) {
						$ptxt = '<a target="_blank" href="personal_stats.php?pilot='. 
									urlencode($pilot) .'">'. $pilot .'</a>';
						$pformat = ' style="background-color: #cccccc;"';
					}
					//display records for only the last 30 days
					echo '<tr>';
					echo '<td'. $pformat .'>'. $ptxt .'</td>';
					echo '<td>'. date("M-d", strtotime($value['maxdate'])) .'</td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- TOTAL ACTIVE CACHES & ALL ACTIONS -->
	<div class="col-sm-4 white">
		<?php
		$ctrrescues = $caches->getRescueTotalCount();

		$ctrsown = $caches->getSownTotalCount();

		$ctrtended = $caches->getTendTotalCount();

		$ctrtot = $caches->getActionTotalCount();
		
		$ctractive = $caches->getActiveCount();
		
		$lockedSys = $systems->getLockedCount();
		
		$expireDays = 5;
		$toexpire = $caches->expireInDays($expireDays);
		?>
		<span class="sechead" style="font-weight: bold; color: gold;">
			Confirmed Rescues: <?php echo $ctrrescues; ?></span><br />
		<br />
		<span class="sechead" style="font-weight: bold;">Total Active Caches:</span><br />
		<span class="sechead"><?php echo $ctractive; ?> of 2603 
			(<?php echo round((intval($ctractive)/2603)*100,1); ?>%)</span><br />
		<br />
		<span class="sechead">"No Sow" systems: <?php echo $lockedSys ?></span><br />
		<span class="sechead">Expiring in <?=$expireDays?> days: <?php echo $toexpire; ?></span><br />
		<br />
		<span class="sechead" style="font-weight: bold;">All actions: 
			<?php echo $ctrtot; ?></span><br />
		<span class="sechead">Sown: <?php echo $ctrsown; ?></span><br />
		<span class="sechead">Tended: <?php echo $ctrtended; ?></span><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(as of YC119-Mar-18)
	</div>
</div>
<?php 
} //if (isset($targetsystem))?>
</div>
</body>
</html>