<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

// if no pilot parameter, send to home page
if (!isset($_REQUEST['pilot'])) {
	header("Location: /");
}

if (isset($_REQUEST['pilot']) && !empty($_REQUEST['pilot'])) {
	$pilot = $_REQUEST['pilot'];
	if ($pilot != $charname) {
		//echo $pilot .':'. $charname;
		//leave this if statement commented out on localhost for testing
		
		if (array_search($charname, $admins) === false) {
			header("Location: /");
		}
		
	}
}

function getPilotStats($start, $end, $pilot)
{
	$database = new Database();
	$database->query("SELECT COUNT(*) AS cnt, Pilot, max(ActivityDate) as act
					FROM activity
					WHERE ActivityDate BETWEEN :start AND :end
					AND Pilot = :pilot");
	$database->bind(':start', $start);
	$database->bind(':end', $end);
	$database->bind(':pilot', $pilot);
	
	$result = $database->single();
	
	$database->closeQuery();
	
	return $result;
}

function getTypeCounts($pilot, $type)
{
	$database = new Database();
	$database->query("SELECT COUNT(*) AS cnt
					FROM activity
					WHERE Pilot = :pilot AND EntryType = :type");
	$database->bind(':pilot', $pilot);
	$database->bind(':type', $type);
	
	$result = $database->single();
	
	$database->closeQuery();
	
	return $result;
}

?>
<html>

<head>
	<?php 
	$pgtitle = 'Personal Stats';
	include_once '../includes/head.php'; 
	?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
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
				$start = date('Y-m-d', strtotime('last Sunday', strtotime("now")));
				$end = date('Y-m-d', strtotime("tomorrow"));
				
				$row = getPilotStats($start, $end, $pilot);
				?>
				<tr>
					<td>Current Week (Sun-Sat)</td>
					<td align="right"><?php echo $row['cnt']; ?></td>
				</tr>
				<!-- LAST 30 DAYS -->
				<?php
				$start = date('Y-m-d', strtotime('-30 days', strtotime("now")));
				$end = date('Y-m-d', strtotime("tomorrow"));
				
				$row = getPilotStats($start, $end, $pilot);
				?>
				<tr>
					<td>Last 30 days</td>
					<td align="right"><?php echo $row['cnt']; ?></td>
				</tr>
				<!-- ALL TIME -->
				<?php
				$database->query("SELECT COUNT(*) AS cnt, Pilot, max(ActivityDate) as act
					FROM activity WHERE Pilot = :pilot");
				$database->bind(':pilot', $pilot);
				
				$row = $database->single();
				
				$database->closeQuery();
				?>
				<tr>
					<td>All Time</td>
					<td align="right"><?php echo $row['cnt']; ?></td>
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
				$row = getTypeCounts($pilot, 'sower');
				?>
				<tr>
					<td style="background-color:#ccffcc;color:black;">Sower</td>
					<td align="right"><?php echo $row['cnt']; ?></td>
				</tr>
				<?php
				$row = getTypeCounts($pilot, 'tender');
				?>
				<tr>
					<td style="background-color:#d1dffa;color:black;">Tender</td>
					<td align="right"><?php echo $row['cnt']; ?></td>
				</tr>
				<?php
				$row = getTypeCounts($pilot, 'adjunct');
				?>
				<tr>
					<td style="background-color:#fffacd;color:black;">Adjunct</td>
					<td align="right"><?php echo $row['cnt']; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-6 white">
		<!-- FULL LISTING -->
		<span class="sechead">Detail</span>
		<table class="table table-striped black" style="width: auto; background: white;">
			<thead>
				<tr>
					<th>Timestamp</th>
					<th>Action</th>
					<th>System</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$database->query("SELECT ActivityDate, EntryType, System
									FROM activity
									WHERE Pilot = :pilot
									ORDER BY ActivityDate DESC");
				$database->bind(':pilot', $pilot);
				
				$rows = $database->resultset();
				
				$database->closeQuery();
				
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
						case 'adjunct':
							$actioncellformat= ' style="background-color:#fffacd;color:black;"';
							break;
						default:
							// ??
					}
					//display records for only the last 30 days
					echo '<tr>';
					echo '<td>'. date("Y-M-d H:i:s", strtotime($value['ActivityDate'])) .'</td>';
					echo '<td'. $actioncellformat .'>'. $value['EntryType'] .'</td>';
					echo '<td><a href="search.php?system='. $value['System'].'">'. $value['System'] .'</a></td>';
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