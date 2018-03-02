<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
require_once '../class/db.class.php';
require_once '../class/pilot.class.php';
require_once '../class/users.class.php';

// if no pilot parameter, send to home page
if (!isset($_REQUEST['pilot'])) {
	header("Location: ".Config::ROOT_PATH);
}
// create a database object
$database = new Database();
$users = new Users($database);

// make sure pilot name is set
if (isset($_REQUEST['pilot']) && !empty($_REQUEST['pilot'])) {
	$pilot = $_REQUEST['pilot'];
	
	// logged in pilot can see only their own stats, unless they are an Admin
	// !!!comment out on localhost for testing!!!
	if ($pilot != $charname) {
		if ($users->isAdmin($charname) === false) {
			header("Location: ".Config::ROOT_PATH);
		}
	}
}
?>
<html>

<head>
	<?php 
	$pgtitle = 'Personal Stats';
	include_once '../includes/head.php'; 
	?>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#example').DataTable( {
		        "order": [[ 0, "desc" ]],
		        "pagingType": "full_numbers"
		    } );
		} );
	</script>
	<style>
	<!--
		a,
		a:visited,
		a:hover {
			color: aqua;
		}
	-->
	</style>
</head>

<?php
// create a pilot object instance
$pilots = new Pilot($database);

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
	<div class="col-sm-8 sechead" style="text-align: center;">
		<br /><?php echo $pgtitle; ?><br/>
		<?php echo $pilot; ?>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>

<div class="row" id="allsystable">
	
	<!-- COUNTERS -->
	<div class="col-sm-4 white">
		<span class="sechead">Activity Count</span><br /><br />
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th>Period</th>
					<th>Count</th>
				</tr>
			</thead>
			<tbody>
				<!-- CURRENT WEEK -->
				<?php
				if(gmdate('w', strtotime("now")) == 0) {
					$start = gmdate('Y-m-d', strtotime("now"));
				}
				else {
					$start = gmdate('Y-m-d', strtotime('last Sunday'));
				}
				$end = gmdate('Y-m-d', strtotime("+ 1 day"));
				
				$count = $pilots->getActivityCount($pilot, $start, $end);
				?>
				<tr>
					<td>Current Week (Sun-Sat)</td>
					<td align="right"><?php echo $count; ?></td>
				</tr>
				<!-- LAST 30 DAYS -->
				<?php
				$start = date('Y-m-d', strtotime('-30 days', strtotime("now")));
				$end = date('Y-m-d', strtotime("+ 1 day"));
				
				$count= $pilots->getActivityCount($pilot, $start, $end);
				?>
				<tr>
					<td>Last 30 days</td>
					<td align="right"><?php echo $count; ?></td>
				</tr>
				<!-- ALL TIME -->
				<?php
				// get all activities (complete time frame to use same method)
				$start = date('Y-m-d', strtotime("1 Januar 2000"));
				// tomorrow
				$end = date('Y-m-d', strtotime("+ 1 day"));
				$count = $pilots->getActivityCount($pilot, $start, $end);
				?>
				<tr>
					<td>All Time</td>
					<td align="right"><?php echo $count; ?></td>
				</tr>
			</tbody>
		</table>
		<!-- BY TYPE -->
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th>Action Type</th>
					<th>Count</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$typeCounts = $pilots->getActivityTypeCount($pilot, 'sower')
				?>
				<tr>
					<td style="background-color:#ccffcc;color:black;">Sower</td>
					<td align="right"><?php echo $typeCounts; ?></td>
				</tr>
				<?php
				$typeCounts = $pilots->getActivityTypeCount($pilot, 'tender')
				?>
				<tr>
					<td style="background-color:#d1dffa;color:black;">Tender</td>
					<td align="right"><?php echo $typeCounts; ?></td>
				</tr>
				<?php
				$typeCounts = $pilots->getActivityTypeCount($pilot, 'agent')
				?>
				<tr>
					<td style="background-color:#fffacd;color:black;">Agent</td>
					<td align="right"><?php echo $typeCounts; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-6 white">
		<!-- FULL LISTING -->
		<table id="example" class="table">
			<thead>
				<tr>
					<th>Timestamp</th>
					<th>Action</th>
					<th>System</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$rows = $pilots->getActivities($pilot);				
				foreach ($rows as $value) {
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
					echo '<td>'. date("Y-m-d H:i:s", strtotime($value['ActivityDate'])).'</td>';
					echo '<td'. $actioncellformat .'>'. ucfirst($value['EntryType']) .'</td>';
					echo '<td><a href="search.php?sys='. $value['System'].'">'. $value['System'] .'</a></td>';
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- reserved for future use -->
	<div class="col-sm-2 white">
		
	</div>
</div>

</div>
</body>
</html>