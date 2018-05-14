<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

// for debug only
/*
 error_reporting(E_ALL);
 ini_set('display_errors', 'on');
*/

include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/caches.class.php';
require_once '../class/systems.class.php';
require_once '../class/output.class.php';
require_once '../class/rescue.class.php';

// check if the user is alliance member
if (!Users::isAllianceUserSession())
{
	// check if last login was already a non auth user
	if (isset($_SESSION['AUTH_NOALLIANCE']))
	{
		// set redirect to root path
		$_redirect_uri = Config::ROOT_PATH;
	}
	else
	{
		// set redirect to requested path
		$_redirect_uri = $_SERVER['REQUEST_URI'];
	}

	// void the session entries on 'attack'
	session_unset();
	// save the redirect URL to current page
	$_SESSION['auth_redirect']=$_redirect_uri;
	// set a flag for alliance user failure
	$_SESSION['AUTH_NOALLIANCE'] = 1;
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH."auth/login.php");
	// stop processing
	exit;
}

// create object instances
$database = new Database();
$users = new Users($database);
$caches = new Caches($database);
$systems = new Systems($database);
$rescue = new Rescue($database);

// set character name
if (!isset($charname))
{
	// no, set a dummy char name
	$charname = 'charname_not_set';
}

// check for SAR Coordinator login
$isCoord = ($users->isSARCoordinator($charname) || $users->isAdmin($charname));

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }

// if start and end dates are not set, set them to default values
if (!isset($_REQUEST['start'])) {
	$start = gmdate('Y-m-d', strtotime("- 7 day"));
	$startPD = gmdate('Y-M-d', strtotime("- 7 day")); // formatted for Pikaday widget
}
if (!isset($_REQUEST['end'])) {
	$end = gmdate('Y-m-d', strtotime("now"));
	$endPD = gmdate('Y-M-d', strtotime("now")); // formatted for Pikaday widget
}

// set start and end dates to submitted values (GET or POST)
if (isset($_REQUEST['start']) && isset($_REQUEST['end'])) {
	// start date
	$arrStart = explode('-', $_REQUEST['start']);
	$startYear = intval(substr($arrStart[0], -3)) + 1898;
	$startMonth = intval(date('m', strtotime($arrStart[1])));
	$startDay = intval($arrStart[2]);
	$start = gmdate('Y-m-d', strtotime($startYear. '-' . $startMonth. '-' . $startDay));
	
	// end date
	$arrEnd = explode('-', $_REQUEST['end']);
	$endYear = intval(substr($arrEnd[0], -3)) + 1898;
	$endMonth = intval(date('m', strtotime($arrEnd[1])));
	$endDay = intval($arrEnd[2]);
	$end = gmdate('Y-m-d', strtotime($endYear. '-' . $endMonth. '-' . $endDay));
	
	// special string for Pikaday widget
	$startPD = htmlspecialchars_decode(date("Y-M-d", strtotime($startYear. '-' . $startMonth. '-' . $startDay)));
	$endPD = htmlspecialchars_decode(date("Y-M-d", strtotime($endYear. '-' . $endMonth. '-' . $endDay)));
}

// set stats type to display
$stat_type = 'participants';
if (isset($_REQUEST['stat_type'])) {
	$stat_type = $_REQUEST['stat_type'];
}
?>
<html>

<head>
	<?php 
	$pgtitle = 'ESR Stats';
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
	
	<!--Load the AJAX API-->
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript">
		// Load the Visualization API and the piechart package.
		google.charts.load('current', {'packages':['corechart']});
		// Draw charts on page load
		google.charts.setOnLoadCallback(drawEsrcParticipationChart);
		google.charts.setOnLoadCallback(drawSarParticipationChart);
		
		function drawEsrcParticipationChart(type) {
		  if (typeof type === "undefined") { type = 'Overall'; }
		  var jsonData = $.ajax({
		      url: "../stats/stats_data_esrc_participation.php?start=<?=$start?>&end=<?=$end?>&type=" + type, 
		      dataType: "json", async: false }).responseText;		          
		  // Create our data table out of JSON data loaded from server.
		  var data = new google.visualization.DataTable(jsonData);
		  // set chart options
		  var options = { title: 'Most Active ' + type, titleTextStyle: { color: 'white', fontSize: 16 }, 
			  legendTextStyle: { color: 'white' }, backgroundColor: 'black', chartArea: {width: '90%'}};
		  // Instantiate and draw our chart, passing in some options.
		  var chart = new google.visualization.PieChart(document.getElementById('esrcParticipation'));
		  chart.draw(data, options);
		}

		function drawSarParticipationChart(type) {
		  if (typeof type === "undefined") { type = 'Rescuers'; }
		  var jsonData = $.ajax({
		      url: "../stats/stats_data_sar_participation.php?start=<?=$start?>&end=<?=$end?>&type=" + type, 
		      dataType: "json", async: false }).responseText;		          
		  // Create our data table out of JSON data loaded from server.
		  var data = new google.visualization.DataTable(jsonData);
		  // set chart options
		  var options = { title: 'Most Active ' + type, titleTextStyle: { color: 'white', fontSize: 16 }, 
			  legendTextStyle: { color: 'white' }, backgroundColor: 'black', chartArea: {width: '90%'}};
		  // Instantiate and draw our chart, passing in some options.
		  var chart = new google.visualization.PieChart(document.getElementById('sarParticipation'));
		  chart.draw(data, options);
		}
	</script>
</head>

<?php
// get rescue counts
$ctrESRCrescues = $rescue->getRescueCount('closed-esrc', $start, $end);
$ctrSARrescues = $rescue->getRescueCount('closed-rescued', $start, $end);
$ctrAllRescues = intval($ctrESRCrescues) + intval($ctrSARrescues);
?>

<body class="white" style="background-color: black;">
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 black" style="text-align: center;">
		<div class="row">
			<div class="col-sm-8" style="text-align: center;">
				<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
					<div class="input-daterange input-group" id="datepicker" style="margin-bottom: 5px;">
						<input type="text" class="input-sm form-control" name="start" id="start" 
							value="<?php echo isset($startPD) ? $startPD : '' ?>" />
						<span class="input-group-addon">to</span>
						<input type="text" class="input-sm form-control" name="end" id="end" 
							value="<?php echo isset($endPD) ? $endPD : '' ?>" />
					</div>
					<label class="radio-inline white"><input type="radio" name="stat_type" 
						value="caches"<?php echo ($stat_type == 'caches' ? 'checked="checked"' : '') ?>
						disabled="disabled">Caches</label>
					<label class="radio-inline white"><input type="radio" name="stat_type" 
						value="participants"<?php echo ($stat_type == 'participants' ? 'checked="checked"' : '') ?>>
						Participants</label>
					<label class="radio-inline white"><input type="radio" name="stat_type" 
						value="records"<?php echo ($stat_type == 'records' ? 'checked="checked"' : '') ?>
						disabled="disabled">Records</label>
					<label class="radio-inline white"><input type="radio" name="stat_type" 
						value="personal"<?php echo ($stat_type == 'personal' ? 'checked="checked"' : '') ?>
						disabled="disabled">Personal</label>
					<div style="margin-top: 5px;">
						<button type="submit" class="btn btn-sm">Get Stats</button>
					</div>
				</form>
			</div>
			<div class="col-sm-4" style="text-align: center;">
				<div class="sechead white" style="font-weight: bold;">RESCUES:&nbsp; 
					<span style="color: gold;"><?php echo $ctrAllRescues; ?></span>
				</div>
				<div class="white text-center" style="font-weight: bold;">ESRC:&nbsp; 
					<span style="color: gold;"><?php echo $ctrESRCrescues; ?></span>
					&nbsp;&nbsp;&nbsp;&nbsp; SAR:&nbsp; 
					<span style="color: gold;"><?php echo $ctrSARrescues; ?></span>
				</div>
			</div>
		</div>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<!-- NAVIGATION TABS -->
<?php include_once 'navtabs.php'; ?>
<div class="ws"></div>
 
<?php
// display error message if there is one
if (!empty($errmsg)) {
?>
	<div class="row" id="errormessage" style="background-color: #ff9999;">
		<div class="col-sm-12 message">
			<?php echo nl2br($errmsg); ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
}
?>
	<!-- STATS TITLE -->
	<div class="row">
		<div class="col-sm-12">
			<div class="sechead white text-center" style="font-weight: bold;">
				<?php echo strtoupper($stat_type);?>
			</div>
		</div>
	</div>
	<div class="ws"></div>
	<div class="row">
	<!-- STATS DISPLAY BEGINS -->
	<?php 
	switch ($stat_type) {
		case 'caches':
			// do stuff
			break;
		case 'participants':
	?>
		<div class="col-sm-6">
			<div class="sechead white text-center" style="font-weight: bold;">ESRC</div>
			<!-- ESRC PARTICIPANT COUNTS BEGIN -->
			<?php 
			// get unique participants from db
			$database->query("SELECT DISTINCT(Pilot) FROM activity 
								WHERE ActivityDate BETWEEN :start AND :end");
			$database->bind(":start", $start);
			$database->bind(":end", $end);
			$arrEsrcUniqueParticipants = $database->resultset();
			$database->closeQuery();
			// set participant counter
			$ctrEsrcUniqueParticipants = count($arrEsrcUniqueParticipants);
			?>
			
			<div class="white text-center"><strong>
				<span style="color: gold;"><?=$ctrEsrcUniqueParticipants?></span> Participants</strong>
				<br />
				<a href="javascript:drawEsrcParticipationChart('Overall')">Overall</a> &nbsp;&nbsp;&nbsp; 
				<a href="javascript:drawEsrcParticipationChart('Agents')">Agents</a> &nbsp;&nbsp;&nbsp; 
				<a href="javascript:drawEsrcParticipationChart('Sowers')">Sowers</a> &nbsp;&nbsp;&nbsp; 
				<a href="javascript:drawEsrcParticipationChart('Tenders')">Tenders</a>
			</div>
			<div class="ws"></div>
			<div id="esrcParticipation" class="text-center" style="width: 450px; height:300px;"></div>
			<!-- ESRC PARTICIPANT COUNTS END -->
		</div>
		<div class="col-sm-6">
			<div class="sechead white text-center" style="font-weight: bold;">SAR</div>
			<!-- SAR PARTICIPANT COUNTS BEGIN -->
			<?php 
			// get unique participants from db
			// Dispatchers
			$database->query("SELECT startagent FROM rescuerequest 
								WHERE requestdate BETWEEN :start AND :end
								GROUP BY startagent ORDER BY COUNT(startagent) DESC");
			$database->bind(":start", $start);
			$database->bind(":end", $end);
			$arrSarUniqueDispatchers = $database->resultset();
			$database->closeQuery();
			$i = 0;
			foreach ($arrSarUniqueDispatchers as $row) {
				$arrSarUD[$i] = $row['startagent'];
				$i++;
			}
			
			// Locators
			$database->query("SELECT locateagent FROM rescuerequest
								WHERE lastcontact BETWEEN :start AND :end
								GROUP BY locateagent  ORDER BY COUNT(locateagent) DESC");
			$database->bind(":start", $start);
			$database->bind(":end", $end);
			$arrSarUniqueLocators = $database->resultset();
			$database->closeQuery();
			$i = 0;
			foreach ($arrSarUniqueLocators as $row) {
				$arrSarUL[$i] = $row['locateagent'];
				$i++;
			}
			
			// Rescuers
			$database->query("SELECT pilot FROM rescueagents  
								WHERE entrytime BETWEEN :start AND :end
								GROUP BY pilot ORDER BY COUNT(pilot) DESC");
			$database->bind(":start", $start);
			$database->bind(":end", $end);
			$arrSarUniqueRescuers = $database->resultset();
			$database->closeQuery();
			$i = 0;
			foreach ($arrSarUniqueRescuers as $row) {
				$arrSarUR[$i] = $row['pilot'];
				$i++;
			}
			
			// merge arrays of SAR participants and de-dupe list to get uniques
			$arrSarParticipants = array_merge($arrSarUD, $arrSarUL, $arrSarUR);
			$arrSarUniqueParticipants = array_unique($arrSarParticipants);
			// set unique SAR participant counter
			$ctrSarUniqueParticipants = count($arrSarUniqueParticipants);
			
			?>
			
			<div class="white text-center"><strong>
				<span style="color: gold;"><?=$ctrSarUniqueParticipants?></span> Participants</strong>
				<br /> 
				<a href="javascript:drawSarParticipationChart('Dispatchers')">Dispatchers</a> &nbsp;&nbsp;&nbsp; 
				<a href="javascript:drawSarParticipationChart('Locators')">Locators</a> &nbsp;&nbsp;&nbsp; 
				<a href="javascript:drawSarParticipationChart('Rescuers')">Rescuers</a>
			</div>
			<div class="ws"></div>
			<div id="sarParticipation" style="width: 450px; height:300px;"></div>
			<!-- SAR PARTICIPANT COUNTS END -->
		</div>
	<?php 
			break;
		case 'records':
			// do stuff
			break;
		case 'personal':
			// do stuff
			break;
	}
	?>
	<!-- SAR STATS END -->
<!-- STATS DISPLAY END -->
	</div>
	<div class="ws"></div>
</div>

<script type="text/javascript">
	// datepicker
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    var startDate,
    endDate,
    updateStartDate = function() {
        startPicker.setStartRange(startDate);
        endPicker.setStartRange(startDate);
        endPicker.setMinDate(startDate);
    },
    updateEndDate = function() {
        startPicker.setEndRange(endDate);
        startPicker.setMaxDate(endDate);
        endPicker.setEndRange(endDate);
    },
    startPicker = new Pikaday({
        field: document.getElementById('start'),
        minDate: new Date('03/18/2017'),
        showMonthAfterYear: true,
        format: 'YYYY-MMM-DD',
        toString(date, format) {
            const day = ("0" + date.getDate()).slice(-2);
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear() - 1898;
            return `YC${year}-${month}-${day}`;
        },
        onSelect: function() {
            startDate = this.getDate();
            updateStartDate();
        }
    }),
    endPicker = new Pikaday({
        field: document.getElementById('end'),
        minDate: new Date('03/18/2017'),
        showMonthAfterYear: true,
        format: 'YYYY-MMM-DD',
        toString(date, format) {
            const day = ("0" + date.getDate()).slice(-2);
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear() - 1898;
            return `YC${year}-${month}-${day}`;
        },
        onSelect: function() {
            endDate = this.getDate();
            updateEndDate();
        }
    }),
    _startDate = startPicker.getDate(),
    _endDate = endPicker.getDate();

    if (_startDate) {
        startDate = _startDate;
        updateStartDate();
    }

    if (_endDate) {
        endDate = _endDate;
        updateEndDate();
    }
</script>

<?php 
function debug($variable){
	if(is_array($variable)){
		echo "<pre>";
		print_r($variable);
		echo "</pre>";
		exit();
	}
	else{
		echo ($variable);
		exit();
	}
}
?>

</body>
</html>