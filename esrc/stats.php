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
	$pgtitle = 'ESR Stats';
	include_once '../includes/head.php'; 
	?>
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

$system = '';
if(isset($_REQUEST['sys'])) { 
	$system = htmlspecialchars_decode($_REQUEST['sys']);
}

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }

// if start and end dates are not set, set them to default values
if (!isset($_REQUEST['start'])) {
	$start = gmdate('Y-M-d', strtotime("- 7 day"));
}
if (!isset($_REQUEST['end'])) {
	$end = gmdate('Y-M-d', strtotime("now"));
}

// set start and end dates to submitted values (GET or POST)
if (isset($_REQUEST['start']) && isset($_REQUEST['end'])) {
	// start date
	$arrStart = explode('-', $_REQUEST['start']);
	$startYear = intval(substr($arrStart[0], -3)) + 1898;
	$startMonth = intval(date('m', strtotime($arrStart[1])));
	$startDay = intval($arrStart[2]);
	$start = htmlspecialchars_decode(date("Y-M-d", strtotime($startYear. '-' . $startMonth. '-' . $startDay)));
	// end date
	$arrEnd = explode('-', $_REQUEST['end']);
	$endYear = intval(substr($arrEnd[0], -3)) + 1898;
	$endMonth = intval(date('m', strtotime($arrEnd[1])));
	$endDay = intval($arrEnd[2]);
	$end = htmlspecialchars_decode(date("Y-M-d", strtotime($endYear. '-' . $endMonth. '-' . $endDay)));
}

// get rescue counts
$ctrESRCrescues = $rescue->getRescueCount('closed-esrc', date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end)));
$ctrSARrescues = $rescue->getRescueCount('closed-rescued', date('Y-m-d', strtotime($start)), date('Y-m-d', strtotime($end)));
$ctrAllRescues = intval($ctrESRCrescues) + intval($ctrSARrescues);
?>

<body class="white" style="background-color: black;">
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 black" style="text-align: center;">
		<div class="row">
			<div class="col-sm-12" style="text-align: center;">
				<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
					<div class="input-daterange input-group" id="datepicker">
						<input type="text" class="input-sm form-control" name="start" id="start" 
							value="<?php echo isset($start) ? $start : '' ?>" />
						<span class="input-group-addon">to</span>
						<input type="text" class="input-sm form-control" name="end" id="end" 
							value="<?php echo isset($end) ? $end : '' ?>" />
					</div>
					<div class="ws"></div>
					<div class="checkbox">
						<label class="white"><input type="checkbox" name="personal" value="yes"> Personal Stats</label>
					</div>
					&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-sm">Get Stats</button>
				</form>
			</div>
		</div>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>

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

	<div class="row">
		<div class="col-sm-12 text-center">
			<span class="sechead white" style="font-weight: bold;">Total Rescues:&nbsp; 
				<span style="color: gold;"><?php echo $ctrAllRescues; ?></span>
			</span>
		</div>
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

</body>
</html>