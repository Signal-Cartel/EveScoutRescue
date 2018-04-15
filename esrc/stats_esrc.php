<?php 
include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';
include_once '../class/mmmr.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/leaderboard_sar.class.php';

// initialize objects
$database = new Database();
$rescue = new Rescue($database);
$sarleaderBoard = new SARLeaderboard($database);
$leaderBoard = new Leaderboard($database);

;
?>

<div class="row" id="allsystable">
<!-- LEADERBOARDS -->
	<div class="col-sm-4 white">
		<!-- CURRENT WEEK SOW/TEND LEADERBOARD -->
		<?php 
		$daysrangeLB = isset($daysrangeLB) ? $daysrangeLB: '30';
		$numberLB= isset($numberLB) ? $numberLB: '10';
		if (isset($_REQUEST['daysrangeLB'])) { 
			$daysrangeLB= htmlspecialchars_decode($_REQUEST['daysrangeLB']);
		}
		if (isset($_REQUEST['numberLB'])) {
			$numberLB = htmlspecialchars_decode($_REQUEST['numberLB']);
		}
		?>
		<!-- date range and number selection form -->
		<span class="sechead" style="font-weight: bold;">SOW / TEND LEADERS</span><br />
		<form id="LBform" name="LBform" method="get" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			Top <input type="text" name="numberLB" size="1" autocomplete="off" class="black"
				value="<?=$numberLB?>"> over the last <input type="text" name="daysrangeLB" 
				size="1" autocomplete="off" class="black" value="<?=$daysrangeLB?>"> days
			<input type="submit" style="display: none;">
			<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th>Pilot</th>
						<th>Total Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$rows = $leaderBoard->getTop($numberLB, $daysrangeLB);
		
					foreach ($rows as $value) {
						echo '<tr>';
						echo '<td>'. Output::htmlEncodeString($value['Pilot']) .'</td>';
						echo '<td align="right">'. $value['cnt'] .'</td>';
						echo '</tr>';
					}
				?>
				</tbody>
			</table>
			<input type="submit" style="visibility: hidden;" /> 
		</form>
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
					//prepare personal stats link for logged-in pilot
					$pilot = $value['Pilot'];
					$ptxt = Output::htmlEncodeString($pilot);
					$pformat = '';
					if (isset($charname) && $pilot == $charname) {
						$ptxt = '<a target="_blank" href="personal_stats.php?pilot='. 
									urlencode($pilot) .'">'. Output::htmlEncodeString($pilot) .'</a>';
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
		$ctrrescues = $rescue->getRescueCount('closed-esrc');
		$ctrsown = $caches->getSownTotalCount();
		$ctrtended = $caches->getTendTotalCount();
		$ctractive = $caches->getActiveCount();
		$lockedSys = $systems->getLockedCount();
		
		$expireDays = 5;
		$toexpire = $caches->expireInDays($expireDays);
		?>
		<span class="sechead" style="font-weight: bold; color: gold;">
			ESRC Rescues: <span style="color: white;"><?php echo $ctrrescues; ?></span></span><br />
		<br />
		<span class="sechead" style="font-weight: bold;">Total Active Caches:</span><br />
		<span class="sechead"><?php echo $ctractive; ?> of 2603 
			(<?php echo round((intval($ctractive)/2603)*100,1); ?>%)</span><br />
			<?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . gmdate('Y-m-d H:i:s', strtotime("now"));?><br />
		<br />
		<span class="sechead">"No Sow" systems: <?php echo $lockedSys ?></span><br />
		<span class="sechead">Expiring in <?=$expireDays?> days: <?php echo $toexpire; ?></span><br />
		<br />
		<span class="sechead" style="font-weight: bold; color: gold;">All Time</span><br />
		<span class="sechead">Sown: <?php echo $ctrsown; ?></span><br />
		<span class="sechead">Tended: <?php echo $ctrtended; ?></span><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(as of YC119-Mar-18)
	</div>
</div> 