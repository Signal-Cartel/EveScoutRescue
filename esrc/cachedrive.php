<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
require_once '../class/output.class.php';
require_once '../class/users.class.php';

if (!isset($_POST['start'])) {
	if (gmdate('w', strtotime("now")) == 0) {
		$start = gmdate('Y-m-d', strtotime("now"));
	}
	elseif (gmdate('w', strtotime("now")) == 1) {
		$start= gmdate('Y-m-d', strtotime("- 1 day"));
	}
	else {
		$start = gmdate('Y-m-d', gmdate(strtotime('last Sunday')));
	}
}

if (!isset($_POST['end'])) {
	$end = gmdate('Y-m-d', strtotime("+ 1 day"));
}
// create database connection
$db = new Database();
// instanciate a users check instance
$users = new Users($database);

// check character name is set
if (!isset($charname))
{
	// no, set a dummy char name
	$charname = 'charname_not_set';
}

// check if user is Admin role
if (!$users->isAdmin($charname))
{
    // no, display an error
    echo 'You are not allowed to view this page: '.$charname;
    exit();
}
?>
<html>

<head>
	<?php
	$pgtitle = 'CrinkleQuest ESRC Contest';
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
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#example').DataTable( {
		        "order": [[ 0, "desc" ]],
		        "pagingType": "full_numbers",
		        "pageLength": 10
		    } );
		} );
		$(document).ready(function() {
		    $('#example2').DataTable( {
		        "order": [[ 0, "desc" ]],
		        "pagingType": "full_numbers",
		        "pageLength": 10
		    } );
		} );
	</script>
</head>

<?php
require_once '../class/db.class.php';
if (isset($_POST['start']) && isset($_POST['end'])) { 
	$start = htmlspecialchars_decode(date("Y-m-d", strtotime($_POST['start'])));
	$end = htmlspecialchars_decode(date("Y-m-d", strtotime($_POST['end'])));
}
?>
<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">CrinkleQuest ESRC Contest</span><br />
			<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				<div class="input-daterange input-group" id="datepicker">
					<input type="text" class="input-sm form-control" name="start" value="<?php echo isset($start) ? $start : '' ?>" />
					<span class="input-group-addon">to</span>
					<input type="text" class="input-sm form-control" name="end" value="<?php echo isset($end) ? $end : '' ?>" />
				</div>
				&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-sm">Search</button>
			</form>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	// display results for the selected date range
	?>	

	<div class="row" id="systable">
		<div class="col-sm-6">
			<p class="sechead white">GROUPED BY PILOT</p>
			<table id="example2" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Date</th>
						<th class="white">Pilot</th>
						<!--<th class="white">Type</th>-->
						<th class="white">Count</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$ctrtotact = $ctrsow = $ctrtend = $ctradj = 0;
				$db->query("SELECT DATE(ActivityDate) AS ActionDate, Pilot, COUNT(Pilot) AS Count FROM activity 
							WHERE DATE(ActivityDate) BETWEEN :start AND :end And EntryType IN ('sower', 'tender')
                            GROUP BY Pilot");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					$ctrtotact++;
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
					// add 4 hours to convert to UTC (EVE) for display
					echo '<td class="white text-nowrap">'. 
							date("Y-m-d", strtotime($value['ActionDate'])) .
						 '</td>';
					echo '<td class="text-nowrap">
							<a target="_blank" href="personal_stats.php?pilot='. urlencode($value['Pilot']) .'">'. 
							$value['Pilot'] .'</a> - <a target="_blank" 
							href="https://evewho.com/pilot/'. $value['Pilot'] .'">EW</a></td>';
					/*echo '<td class="white" '. $actioncellformat .'>'. ucfirst($value['EntryType']) .'</td>';
					switch ($value['EntryType']) {
						case 'sower':
							$ctrsow++;
							break;
						case 'tender':
							$ctrtend++;
							break;
						case 'agent':
							$ctradj++;
							break;
					}
					*/
					echo '<td align="right" class="white">'. $value['Count'] .'</td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-6">
			<p class="sechead white">UNGROUPED</p>
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Date</th>
						<th class="white">Pilot</th>
						<th class="white">Type</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$ctrtotact = $ctrsow = $ctrtend = $ctradj = 0;
				$db->query("SELECT ActivityDate, Pilot, EntryType FROM activity 
							WHERE ActivityDate BETWEEN :start AND :end And EntryType IN ('sower', 'tender')");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					$ctrtotact++;
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
					// add 4 hours to convert to UTC (EVE) for display
					echo '<td class="white text-nowrap">'. 
							date("Y-m-d H:i:s", strtotime($value['ActivityDate'])) .
						 '</td>';
					echo '<td class="text-nowrap">
							<a target="_blank" href="personal_stats.php?pilot='. urlencode($value['Pilot']) .'">'. 
							$value['Pilot'] .'</a> - <a target="_blank" 
							href="https://evewho.com/pilot/'. $value['Pilot'] .'">EW</a></td>';
					echo '<td class="white" '. $actioncellformat .'>'. ucfirst($value['EntryType']) .'</td>';
					switch ($value['EntryType']) {
						case 'sower':
							$ctrsow++;
							break;
						case 'tender':
							$ctrtend++;
							break;
						case 'agent':
							$ctradj++;
							break;
					}
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}
</script>

</body>
</html>