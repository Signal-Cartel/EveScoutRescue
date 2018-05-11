<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

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

?>
<html>

<head>
	<?php 
	$pgtitle = 'ESR Stats - Rescues Tabular Detail';
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

<?php
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

// get rescue type from request and handle for bad input
$rescuetype = '';
if(isset($_REQUEST['type'])) { 
	$rescuetype= htmlspecialchars_decode($_REQUEST['type']);
}
if ($rescuetype != 'ESRC' && $rescuetype != 'SAR') {
	$rescuetype = '';
}

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

// get rescue counts
$ctrESRCrescues = $rescue->getRescueCount('closed-esrc', $start, $end);
$ctrSARrescues = $rescue->getRescueCount('closed-rescued', $start, $end);
$ctrAllRescues = intval($ctrESRCrescues) + intval($ctrSARrescues);

// get all successful rescues for time period
$arrRescueDetail = $rescue->getRequests(1, $start, $end);
?>

<body class="white" style="background-color: black;">
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<!-- DATEPICKER -->
	<div class="col-sm-8 black" style="text-align: center;">
		<div class="row">
			<div class="col-sm-12" style="text-align: center;">
				<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
					<div class="input-daterange input-group" id="datepicker">
						<input type="text" class="input-sm form-control" name="start" id="start" 
							value="<?php echo isset($startPD) ? $startPD : '' ?>" />
						<span class="input-group-addon">to</span>
						<input type="text" class="input-sm form-control" name="end" id="end" 
							value="<?php echo isset($endPD) ? $endPD : '' ?>" />
					</div>
					<div class="ws"></div>
					<div class="checkbox">
						<label class="white"><input type="checkbox" name="personal" value="yes"> Personal Stats</label>
					</div>
					<input type="hidden" name="type" id="type" value="<?=$rescuetype?>">
					&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-sm">Get Stats</button>
				</form>
			</div>
		</div>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>

<!-- NAVIGATION TABS -->
<ul  class="nav nav-tabs">
	<li><a href="search.php?sys=<?=$system?>">Rescue Cache</a></li>
	<li><a href="rescueoverview.php?sys=<?=$system?>">Search &amp; Rescue</a></li>
	<li class="active"><a href="#" data-toggle="tab">Statistics</a></li>
	<?php 
		if ($isCoord == 1) {
			echo '<li><a href="esrcoordadmin.php">ESR Coordinator Admin</a></li>';
		}
	?>
</ul>
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
<!-- STATS BEGIN -->
	<div class="row">
		<div class="col-sm-12 text-center">
			<h3>
				<span class="sechead white" style="font-weight: bold;"><?=$rescuetype?> Rescues:&nbsp; 
					<span style="color: gold;"><?=${'ctr'.$rescuetype.'rescues'}?></span>
				</span>
			</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<!-- ESRC RESCUES DETAIL TABLE BEGIN -->
			<?php 
			// display ESRC Rescue detail table
			displayTable($arrRescueDetail, 'closed-esrc');
			// FIX NEEDED: Table sorts dates alphabetically, probably due to EVE formatting
			// Need to fix this so that EVE format is displayed but sorting happens on natural date
			?>
			<!-- ESRC RESCUES DETAIL TABLE END -->
		</div>
	</div>
	<div class="ws"></div>
<!-- STATS END -->
</div>

<?php 
/**
 * Format input as HTML table in output
 * @param array $data - array of row details
 * @param string $type - ESRC ("closed-esrc") or SAR ("closed-rescued")
 */
 function displayTable($data, $type)
 {
 	$database = new Database();
 	$rescue = new Rescue($database);
 	
 	echo '<div class="row">';
 	echo '<div class="col-sm-12 request">';
 	if (empty($data)) {
 		echo '<p>None for the selected date range.</p>';
 	}
 	else {
 		echo '<table id="tblRescueDetail'. $type .'" class="table" style="width: auto;">';
 		echo '	<thead>';
 		echo '		<tr>';
 		echo '			<th>Date</th>';
 		echo '			<th>System</th>';
 		echo ($type == 'closed-esrc') ? '<th>Agent</th>' : '<th>Dispatcher</th>';
 		echo ($type == 'closed-rescued') ? '<th>Locator</th>' : ''; // SAR-only column
 		echo ($type == 'closed-rescued') ? '<th>Rescuer(s)</th>' : ''; // SAR-only column
 		echo '		</tr>';
 		echo '	</thead>';
 		echo '	<tbody>';
 		foreach ($data as $row) {
 			if ($row['status'] == $type) {
	 			echo '<tr>';
	 			
	 			// Date when rescue occurred
	 			echo '	<td style="text-nowrap">'. Output::getEveDate($row['lastcontact']) .'</td>';
	 			
	 			// System
	 			echo '	<td><a href="rescueoverview.php?sys='.$row['system'].'" target="_blank">'.
	 	 						Output::htmlEncodeString($row['system']).'</a></td>';
	 			
	 	 		// Dispatcher/Agent - name of Signaleer who opened request
	 	 		echo '	<td>' . Output::htmlEncodeString($row['startagent']) . '</td>';
	 			
	 	 		// SAR-only columns
	 	 		if ($type == 'closed-rescued') { 	 				
	 	 			// Locator - display name of Signaleer who located system (if any)
	 	 			echo (empty($row['locateagent'])) ? '<td>&nbsp;</td>' : '<td>' .
	 	 				Output::htmlEncodeString($row['locateagent']) . '</td>';
	 	 				
	 	 			// Rescue Pilot(s) - display name(s) of Signaleer who participated in live rescue (if any)
	 	 			$arrRescueAgents = $rescue->getRescueAgents($row['id']);
	 	 			echo '<td>';
	 	 			foreach ($arrRescueAgents as $value) {
	 	 				echo $value['pilot'] .'<br />';
	 	 			}
	 	 			echo '</td>';
	 	 		}
	 	 		echo '</tr>';
 			}
 		}
 		echo '	</tbody>';
 		echo '</table>';
 	}
 	echo '</div>';
 	// close row
 	echo '</div>';
 }
?>

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

    // ESRC datatable
    $(document).ready(function() {
	    $('#tblRescueDetailclosed-esrc').DataTable( {
	        "order": [[ 0, "desc" ]],
	        "pagingType": "full_numbers"
	    } );
	} );

 	// SAR datatable
    $(document).ready(function() {
	    $('#tblRescueDetailclosed-rescued').DataTable( {
	        "order": [[ 0, "desc" ]],
	        "pagingType": "full_numbers"
	    } );
	} );
</script>

</body>
</html>