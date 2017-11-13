<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; ?>
<html>

<head>
	<?php
	$pgtitle = 'One Year Anniversary Super Happy Fun Time ESRC Contest!';
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
?>
<body class="white">
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Cache Drive Totals</span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<div class="row" id="systable">
		<!-- OVERALL TABLE -->
		<div class="col-sm-3">
			<span class="sechead">OVERALL</span><br /><br />
			<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th>Pilot</th>
						<th>Count</th>
					</tr>
				</thead>
				<tbody>
					<?php
					//summary data
					$db = new Database();
					$start = date("Y-m-d H:i:s", strtotime('2017-11-10 11:00:00'));
					$end = date("Y-m-d H:i:s", strtotime('2017-11-13 11:00:00'));
					
					//count of all actions performed in the specified period
					$db->query("SELECT COUNT(*) as cnt
								FROM activity
								WHERE ActivityDate BETWEEN :start AND :end");
					$db->bind(':start', $start);
					$db->bind(':end', $end);
					$row = $db->single();
					
					$ctrtot = $row['cnt'];
					
					//overall count, by pilot, desc order on total count
					$db->query("SELECT Pilot, COUNT(*) as cnt 
								FROM activity WHERE ActivityDate BETWEEN :start AND :end 
								GROUP BY Pilot
								ORDER BY cnt DESC");
					$db->bind(':start', $start);
					$db->bind(':end', $end);
					$rows = $db->resultset();
					$ctr = 0;
					foreach ($rows as $value) {
						$ctr++;
						echo '<tr>';
						echo '<td>'. $value['Pilot'] .'</td>';
						echo '<td align="right">'. $value['cnt'] .'</td>';
						echo '</tr>';
					} ?>
					<tr>
						<td>Participants: <?php echo $ctr; ?></td>
						<td align="right"><?php echo $ctrtot; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<!-- WORMHOLE CLASSES TABLE -->
		<div class="col-sm-3">
			<?php 
			//loop through six times, one for each WH class
			for ($i = 1; $i <= 6; $i++) { ?>
			<!-- C1 -->
			<span class="sechead" style="color: gold;">C<?php echo $i; ?> Wormholes</span><br /><br />
			<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th>Pilot</th>
						<th>Count</th>
					</tr>
				</thead>
				<tbody>
				<?php
				//overall count, by pilot, by wormhole class, desc order on total count
				$db->query("SELECT Pilot, COUNT(*) as cnt 
							FROM activity a, wh_systems wh
							WHERE ActivityDate BETWEEN :start AND :end
							AND wh.System = a.System AND wh.Class = 'Class ". $i ."'
							GROUP BY Pilot
							ORDER BY cnt DESC
							LIMIT 1");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				//$db->bind(':i', $i);
				$rows = $db->resultset();
				$ctr = 0;
				foreach ($rows as $value) {
					$ctr++;
					echo '<tr>';
					echo '<td>'. $value['Pilot'] .'</td>';
					echo '<td align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
				} ?>
				</tbody>
			</table>
			<?php } ?>
		</div>
		
	</div>
	</div>
</body>
</html>