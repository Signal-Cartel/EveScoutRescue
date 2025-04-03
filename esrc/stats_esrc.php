<?php 

$show_leader_board = true;
if ($charname == 'Igaze') {$show_leader_board = true;}
require_once '../includes/auth-inc.php';
require_once '../class/users.class.php';
require_once '../class/config.class.php';
require_once '../class/mmmr.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/leaderboard_sar.class.php';

// initialize objects
if (!isset($database)) { $database = new Database();}
if (!isset($rescue)) {$rescue = new Rescue($database);}
if (!isset($sarleaderBoard)) {$sarleaderBoard = new SARLeaderboard($database);}
if (!isset($leaderBoard)) {$leaderBoard = new Leaderboard($database);}

require_once 'hourly_data.php';	

?>

<div class="row" id="allsystable">
<!-- LEADERBOARDS -->
	<div class="col-sm-4 white">
		<!-- CURRENT WEEK SOW/TEND LEADERBOARD -->
		<?php 
			$daysrangeLB = isset($daysrangeLB) ? $daysrangeLB: '0';
			$numberLB= isset($numberLB) ? $numberLB: '10';
			if (isset($_REQUEST['daysrangeLB'])) { 
				$daysrangeLB= htmlspecialchars_decode($_REQUEST['daysrangeLB']);
			}
			if (isset($_REQUEST['numberLB'])) {
				$numberLB = htmlspecialchars_decode($_REQUEST['numberLB']);
			}
			?>
			<!-- date range and number selection form -->
			<p><span class="subhead">LEADERBOARD</span></p>
			<form id="LBform" name="LBform" method="get" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				<p>Top <input type="text" name="numberLB" size="1" autocomplete="off" class="black"
					value="<?=$numberLB?>"> 
				over the last <input type="text" name="daysrangeLB" size="1" autocomplete="off" class="black" 
					value="<?=$daysrangeLB?>"> days
					<br><small>* days begin/end at shutdown 11:00 UTC</small></p>
					<input type="submit" style="display: none;">
				</form>

						<?php 
			
			if ($show_leader_board){
							echo '<table class="table" style="width: 80%;"><thead><tr>';
							echo '<th>Pilot</th>';							
							echo '<th>Sows/Tends</th>';
							echo '</tr></thead><tbody>';
							$rows = $leaderBoard->getTop($numberLB, $daysrangeLB);	
							foreach ($rows as $value) {					
								echo '<tr>';
								echo '<td>'. Output::htmlEncodeString($value['Pilot']) .'</td>';
								echo '<td align="right">'. $value['cnt'] .'</td>';
								echo '</tr>';
							}
							echo'</tbody></table>';
						}
						else{
									echo '<div class="text-center">';
									echo 'Get out there, Pilot! ';
									echo 'the <b>Tender Games</b> ';
									echo 'are on!';
									echo '<img src="../img/tender-games.jpg"/>';	
									echo '</div>';
							}
					?>

	</div>
	<div class="col-sm-5 white">
		<!-- HALL OF HELP -->
		<p><span class="subhead">HALL OF HELP</p>
		<p>All participants, last 30 days<br/>Most recent first</p>
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
	<div class="col-sm-3 white">
	
	

		
		<p class="subhead">CURRENT STATS</p>	
		<p>
			ESRC Rescues: <span class="gold"><?php echo $ctrrescues; ?></span>
		</p>
		<p>
				Active Caches:<span class="gold"><?php echo $ctractive; ?> </span>
				<br/>
				&nbsp;<?php echo round((intval($ctractive)/2603)*100,1); ?>% of 2603
				<br />
				&nbsp;<small>(as of last Downtime)</small>
		</p>
		
		<p>No Sow systems: <span class="gold"><?php echo $lockedSys ?></span>
			<br/>
			Expiring in <?=$expireDays?> days: <span class="gold"><?php echo $toexpire; ?></span>
		</p>
		<hr/>
		<p class="subhead">ALL TIME</p>	
		
		<p>Sown: <span class="gold"><?php echo $ctrsown; ?></span><br />
		Tended: <span class="gold"><?php echo $ctrtended; ?></span>
		</p>
		<p><small>(since YC119-Mar-18)</small>
		</p>
	</div>
</div> 