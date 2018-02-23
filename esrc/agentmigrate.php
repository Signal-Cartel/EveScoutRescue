<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);


include_once '../includes/auth-inc.php';
require_once '../class/output.class.php';
require_once '../class/db.class.php';
?>
<html>

<head>
	<?php
	$pgtitle = 'ESRC Agent Data Migration';
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
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">ESRC Agent Data 
				Migration Tool</span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	$db = new Database();	
	?>
	<div class="row">
		<div class="col-sm-10">
			<?php
			$ctr = 0;
			$db->query("SELECT * FROM activity WHERE EntryType = 'adjunct' OR EntryType = 'agent'
						ORDER BY ActivityDate DESC");
			$rows = $db->resultset();
			?>
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
			foreach ($rows as $value) {
				$ctr++;		
				// display rows from [activity] to end user
				echo '<tr>';
				echo '<td class="white text-nowrap">'. 
						date("Y-m-d H:i:s", strtotime($value['ActivityDate'])) .
					 '</td>';
				echo '<td class="text-nowrap">
						<a target="_blank" href="personal_stats.php?pilot='. urlencode($value['Pilot']) .'">'. 
						$value['Pilot'] .'</a> - <a target="_blank" 
						href="https://evewho.com/pilot/'. $value['Pilot'] .'">EG</a></td>';
				echo '<td class="white" '. $actioncellformat .'>'. ucfirst($value['EntryType']) .'</td>';
				echo '<td><a href="search.php?sys='. $value['System'] .'" target="_blank">'. 
						$value['System'] .'</a></td>';
				echo '<td><a target="_blank" 
						href="https://evewho.com/pilot/'. $value['AidedPilot'] .'">'. 
						Output::htmlEncodeString($value['AidedPilot']) .'</td>';
				echo '</tr>';
				
				// build SQL insert to [resceurequest]
				$db->beginTransaction();
				$db->query("INSERT INTO rescuerequest (system, pilot, requestdate, lastcontact, 
					finished, startagent, closeagent, status) VALUES (:system, :pilot, :requestdate,
					:lastcontact, 1, :startagent, 'Thrice Hapus', 'closed-esrc')");
				$db->bind(':system', $value['System']);
				$db->bind(':pilot', $value['AidedPilot']);
				$db->bind(':requestdate', date('Y-m-d', strtotime($value['ActivityDate'])));
				$db->bind(':lastcontact', date('Y-m-d', strtotime($value['ActivityDate'])));
				$db->bind(':startagent', $value['Pilot']);
				$db->execute();
				$db->endTransaction();
			}
			?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
			Rows: <?php echo $ctr; ?>
		</div>
	</div>
</div>

</body>
</html>