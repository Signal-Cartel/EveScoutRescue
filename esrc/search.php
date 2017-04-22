<?php include_once '../includes/auth-inc.php'; ?>
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
                remote: 'typeahead.php?type=system&query=%QUERY'
            });
        })
    </script>
</head>

<?php
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/caches.class.php';
require_once '../class/systems.class.php';

// create a cache object instance
$caches = new Caches();

if(isset($_REQUEST['targetsystem'])) { 
	$targetsystem = htmlspecialchars($_REQUEST['targetsystem']);
}
elseif (isset($_REQUEST['system'])) {
	$targetsystem = htmlspecialchars($_REQUEST["system"]);
}
?>
<body>
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8  black" style="text-align: center; height: 100px; vertical-align: middle;">
		<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<div class="form-group">
				<input type="text" name="targetsystem" size="30" autoFocus="autoFocus" class="targetsystem" placeholder="System Name" value="<?php echo isset($targetsystem) ? $targetsystem : '' ?>">
			</div>
			<button type="submit" class="btn btn-lg">Search</button>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="data_entry.php" class="btn btn-info" role="button">Go to Data Entry</a>
		</form>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<?php
// check if a system is supplied
if (isset($targetsystem)):
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
			$statuscellformat = ' style="background-color:yellow;"';
		}
		// save notes as separate var
		$strNotes = $row['Note'];
		?>
		<div class="row" id="systableheader">
		<div class="col-sm-12">
		<div style="padding-left: 10px;">
		<!-- TEND button -->
		<a href="data_entry.php?tendsys=<?=$targetsystem?>" class="btn btn-success" role="button">Tend</a>&nbsp;&nbsp;&nbsp;
		<!-- ADJUNCT button -->
		<a href="data_entry.php?adjsys=<?=$targetsystem?>" class="btn btn-warning" role="button">Adjunct</a>&nbsp;&nbsp;&nbsp;
		<!-- TW button -->
		<a href="https://tripwire.eve-apps.com/?system=<?=$targetsystem?>" class="btn btn-info" role="button" target="_blank">Tripwire</a>&nbsp;&nbsp;&nbsp;
		<!-- ww.pasta.gg button -->
		<a href="http://wh.pasta.gg/<?=$targetsystem?>" class="btn btn-info" role="button" target="_blank">ww.pasta.gg</a>&nbsp;&nbsp;&nbsp;
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
					<tr>
					<td class="white"><?=date("Y-M-d", strtotime($row['InitialSeedDate']))?></td>
					<td class="white"><?=$row['Location']?></td>
					<td class="white"><?=$row['AlignedWith']?></td>
					<td class="white"><?=htmlspecialchars_decode($row['Distance'])?></td>
					<td class="white"><?=htmlspecialchars_decode($row['Password'])?></td>
					<td<?=$statuscellformat ?>><?=$row['Status']?></td>
					<td class="white"><?=date("Y-M-d", strtotime($row['ExpiresOn']))?></td>
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
							<th class="white">Notes</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white"><?= htmlspecialchars_decode($strNotes) ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php } //if (!empty($strNotes))
	}
	else
	{
		$systems = new Systems();
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
			<span class="white">No cache exists for this system.</span>&nbsp;&nbsp;&nbsp;
			<a href="data_entry.php?sowsys=<?=$targetsystem?>" class="btn btn-success" role="button">Sow one now</a>&nbsp;&nbsp;&nbsp;
	
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
			<!-- SOW button  -->
			<span class="white">The system '<?=$targetsystem?>' is locked until <?=date("Y-M-d", strtotime($lockedDate))?>. Do not sow a cache in this system.&nbsp;&nbsp;&nbsp;</span>
	
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
			<span class="white">No cache exists for this system.&nbsp;
			'<?=$targetsystem?>' is not a valid system name.&nbsp;&nbsp;&nbsp;</span>
	
			<a href="?" class="btn btn-link" role="button">clear result</a>
			</div></div></div>
		<?php
		}
	} //(!empty($row))

// no system selected, so show summary stats
else:
?>
<div class="row" id="allsystable">
	
	<?php 
		$leaderBoard = new LeaderBoard();
	?>
	
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
				$rows = $leaderBoard->getTopPilotsWeek(3);				
	
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td align="right" class="white">'. $value['cnt'] .'</td>';
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
					<th class="white">Pilot</th>
					<th class="white">Total Actions</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$rows = $leaderBoard->getTopLastDays(5, 30);

				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td align="right" class="white">'. $value['cnt'] .'</td>';
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
					<th class="white">Pilot</th>
					<th class="white">Total Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$rows = $leaderBoard->getAllHigh(10);
				
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td align="right" class="white">'. $value['cnt'] .'</td>';
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
					<th class="white">Pilot</th>
					<th class="white">Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$rows = $leaderBoard->getActivePilots(30);
				foreach ($rows as $value) {
					//display records for only the last 30 days
					echo '<tr>';
					echo '<td class="white">'. $value['Pilot'] .'</td>';
					echo '<td class="white">'. date("Y-M-d", strtotime($value['maxdate'])) .'</td>';
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
		?>
		<span class="sechead" style="font-weight: bold; color: gold;">Confirmed Rescues: <?php echo $ctrrescues; ?></span><br />
		<br />
		<span class="sechead"><span style="font-weight: bold;">Total Active Caches:</span><br />
		<?php echo $ctractive; ?> of 2603 (<?php echo round((intval($ctractive)/2603)*100,1); ?>%)</span><br />
		<br />
		<span class="sechead" style="font-weight: bold;">All actions: <?php echo $ctrtot; ?></span><br />
		<span class="sechead">Sown: <?php echo $ctrsown; ?></span><br />
		<span class="sechead">Tended: <?php echo $ctrtended; ?></span><br />
		<br />
		(all figures as of 2017-Mar-18)
	</div>
</div>
<?php endif; //if (isset($targetsystem))?>
</div>
</body>
</html>