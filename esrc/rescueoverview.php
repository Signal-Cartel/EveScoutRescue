<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';
include_once '../class/mmmr.class.php';

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

// check if a valid character name is set
if (!isset($charname)) {
	// no, set a dummy char name
	$charname = 'charname_not_set';
}
?>
<html>

<head>
	<?php 
	$pgtitle = 'SAR Requests';
	include_once '../includes/head.php'; 
	?>
	
	<style>
		.sartable th, td {
		    padding: 4px;
		    vertical-align: text-top;
		}
		.request a {
			color: aqua;
		}
		.request a:link {
			color: aqua;
		}
		.request a:visited {
			color: aqua;
		}
		.request a:hover {
			color: aqua;
		}
	</style>
	
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#tblClosed').DataTable( {
		        "order": [[ 1, "desc" ]],
		        "pagingType": "full_numbers",
		        "dom": 'lfprtip'
		    } );
		} );
	</script>
</head>

<?php
require_once '../class/db.class.php';
require_once '../class/rescue.class.php';
require_once '../class/systems.class.php';
require_once '../class/output.class.php';
require_once '../class/caches.class.php';

// create a new database connection
$database = new Database();

// create object instances
$users = new Users($database);
$rescue = new Rescue($database);

// check for SAR Coordinator login
$isCoord = ($users->isSARCoordinator($charname) || $users->isAdmin($charname));

$system = '';
if (isset($_REQUEST['sys'])) {
	$system = ucfirst(htmlspecialchars_decode($_REQUEST["sys"]));
}

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }

?>
<body class="white" style="background-color: black;">
	<div class="container">
	
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<?php include_once 'top-middle.php'; ?>
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

// display rescue requests for a specific system
if (!empty($system)) { 
	$systems = new Systems($database);
	if ($systems->validatename($system) === 0) {
	?>
	
	<div>
		<!-- System Name display -->
		<span style="font-weight: bold; font-size: 200%;"><?=$system?></span>&nbsp;&nbsp;&nbsp;&nbsp;
		<!-- SAR New button -->
		<a type="button" class="btn btn-danger"	role="button" data-toggle="modal"
			data-target="#ModalSARNew">New SAR</a>&nbsp;&nbsp;&nbsp;
		<!-- TW button -->
		<a href="https://tripwire.eve-apps.com/?system=<?=$system?>" class="btn btn-info" 
			role="button" target="_blank">Tripwire</a>&nbsp;&nbsp;&nbsp;
		<!-- anoik.is button -->
		<a href="http://anoik.is/systems/<?=$system?>" class="btn btn-info" role="button" 
			target="_blank">anoik.is</a>&nbsp;&nbsp;&nbsp;
		<!-- web evemail client button -->
		<a href="/copilot/mail/" class="btn btn-info" role="button" 
			target="_blank">EVEMail</a>&nbsp;&nbsp;&nbsp;
		<!-- Chains button, if relevant -->
		<?php
		// "chains" button is Coord-only
		if ($isCoord) {
			echo '<a href="/copilot/data/chains?system='. $system .'" class="btn btn-info" 
				role="button" target="_blank">Chains</a>&nbsp;&nbsp;&nbsp;';
		}
		?>
	</div>
	<div class="ws"></div>
	
	<?php 
	// get active requests from database
	$data = $rescue->getSystemRequests($system, 0, $isCoord);
	displayTable($data, $charname, 0, $system, 1, $isCoord, 0);
	
	// get finished requests from database
	$data = $rescue->getSystemRequests($system, 1, $isCoord);
	displayTable($data, $charname, 1, $system, 0, $isCoord, 0);
	}
	// invalid system name
	else { ?>
	<div class="row">
		<div class="col-sm-12">
			<div style="padding-left: 10px;">
				<span class="sechead white"><?=$system?> not a valid system name.
					Please correct name and resubmit.&nbsp;&nbsp;&nbsp;</span>
			</div>
		</div>
	</div>
<?php }
}
// no system selected, so display all requests
else {
	//active requests
	$data = $rescue->getRequests();
	displayTable($data, $charname, 0, $system, 0, $isCoord, 1);
}
?>

<!-- MODAL includes -->
<?php
include 'modal_sar_new.php';
include 'modal_sar_manage.php';
?>

<script type="text/javascript">
	// auto-display edit modal when "req" parameter provided in querystring
	var url = window.location.href;
	if(url.indexOf('req=') != -1) {
	    $('#ModalSAREdit').modal('show');
	}

	// auto-display new modal when "new" parameter provided in querystring
	var url = window.location.href;
	if(url.indexOf('new=') != -1) {
	    $('#ModalSARNew').modal('show');
	}

	// initialize tooltip display
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip({container: 'body'}); 
	});
</script>

</div>
</body>
</html>

<?php 
/**
 * Translates the internal status value to a readable form.
 * 
 * Note: May be move to a central place later if used more than once.
 * 
 * @param unknown $status
 * @return string|unknown
 */
function translateStatus($status)
{
	$result = "unknown";
	switch ($status) {
		case 'system-located' : $result = "Located";
		break;
		case 'closed-rescued' : $result = "Rescued - SAR";
		break;
		case 'closed-esrc' : $result = "Rescued - ESRC";
		break;
		case 'closed-escaped' : $result = "Escaped by own action";
		break;
		case 'closed-escapedlocals' : $result = "Escaped via locals";
		break;
		case 'closed-destruct' : $result = "Self-destruct";
		break;
		case 'closed-destroyed' : $result = "Destroyed by locals";
		break;
		case 'closed-noresponse' : $result = "No response";
		break;
		case 'closed-declined' : $result = "Declined - illegitimate";
		break;
		case 'closed-dup' : $result = "Declined - duplicate";
		break;
		
		default:
			$result = ucfirst($status);
		break;
	}
	return $result;
}

/**
 * Format input as HTML table in output
 * @param unknown $data - array of row details
 * @param number $finished - bit to filter on active/closed requests
 * @param unknown $system - system to show info for (NULL shows all)
 * @param number $notes - bit to toggle display of notes field
 * @param number $isCoord - bit to indicate if logged-in user is coordinator
 * @param number summary - bit to toggle summary/detailed table listing
 * @param number noUpdate - bit to toggle Update button
 */
function displayTable($data, $charname, $finished = 0, $system = NULL, $notes = 0, $isCoord = 0, 
	$summary = 1, $noUpdate = 0)
{
	$strStatus = ($finished == 0) ? 'Active' : 'Closed';
	$strcols = ($finished == 0 && empty($system)) ? 'col-sm-13 ' : '';
	
	echo '<div class="row">';
	echo '<div class="'. $strcols .'request">';
	echo '<span class="sechead">'. $strStatus .' Requests</span>';
	if (empty($data)) {
		echo '<p>None for this system.</p>';
	} 
	else { 
		//echo print_r($data); // for testing
		echo '<table id="tbl'. $strStatus .'" class="table display" style="width: auto;">';
		echo '	<thead>';
		echo '		<tr>';
		// display Update button for finished requests only when coord logged in
		if (($isCoord == 1 && $finished == 1) || ($finished == 0 && $noUpdate == 0)) {
			echo '		<th></th>';
		}
		echo '			<th>Opened</th>';
		echo (!empty($system)) ? '' : '<th>System</th>';
		echo (!empty($system)) ? '' : '<th>Class</th>';
		echo (!empty($system)) ? '' : '<th>Statics</th>';
		echo '			<th>Pilot</th>';
		echo '			<th>Status</th>';
		echo '			<th>Last&nbsp;Contact</th>';
		// display this column only on "summary" table
		echo ($summary == 0) ? '' : '<th>Bounty (ISK)</th>';
		// display these three columns only on "detail" table
		echo ($summary == 1) ? '' : '<th>Dispatcher</th>';
		echo ($summary == 1) ? '' : '<th>Locator</th>';
		echo ($summary == 1) ? '' : '<th>Rescue Pilot(s)</th>';
		echo '		</tr>';
		echo '	</thead>';
		echo '	<tbody>';
		foreach ($data as $row) {
			displayLine($row, $charname, $finished, $system, $notes, $isCoord, $summary, $noUpdate);
		}
		echo '	</tbody>';
		echo '</table>';
	}
	echo '</div>';
	// close row
	echo '</div>';
	// display stats under the "overview" active requests table
	if ($finished == 0 && empty($system)) { include_once 'stats_sar.php'; }
}

/**
 * Format input as HTML table data row in output
 * @param unknown $data - array of row details
 * @param number $finished - bit to filter on active/closed requests
 * @param unknown $system - system to show info for (NULL shows all)
 * @param number $notes - bit to toggle display of notes field
 * @param number $isCoord - bit to indicate if logged-in user is coordinator
 * @param number summary - bit to toggle summary/detailed table listing
 * @param number noUpdate - bit to toggle Update button
 */
function displayLine($row, $charname, $finished, $system, $notes, $isCoord, $summary, $noUpdate)
{
	// create a new database connection
	$database = new Database();
	// create object instances
	$users = new Users($database);
	$rescue = new Rescue($database);
	$systems = new Systems($database);
	
	$status = $row['status'];
	$colspan = 0;
	
	echo "<tr>";
	
	// "Update" button - display only for finished requests if coord logged in
	if (($isCoord == 1 && $finished == 1) || ($finished == 0 && $noUpdate == 0)) {
		echo '<td><a type="button" class="btn btn-danger" role="button" href="?sys='.
				$row['system'].'&amp;req='.$row['id'].'">Update</a></td>';
	}
	
	// Opened - date request was created
	$colspan++;
	echo '<td style="text-nowrap">'. date("M-d", strtotime($row['requestdate'])) .'</td>';
	
	// System - name of J-space system
	if (empty($system)) {
		$colspan++;
		echo '<td>';
		echo '<a href="?sys='.ucfirst($row['system']).'">'.
				Output::htmlEncodeString(ucfirst($row['system'])).'</a>';
		echo '</td>';
	}
	
	// Class - class of J-space system
	if (empty($system)) {
		$colspan++;
		echo '<td>'. Output::htmlEncodeString($row['Class']).'</td>';
	}
			
	// Statics
	if (empty($system)) {
		$colspan++;
		// get current system
		$staticSystem = ucfirst($row['system']);
		// get connections of system
		$systemData = $systems->getWHInfo($staticSystem);
		
		$staticData = '';
		foreach (explode(',', $systemData['StaticWhInfo']) as $staticWhInfo) {
				
			$staticConnection = explode('/', $staticWhInfo);
		
			$dest = $staticConnection[1];
			// check if already data is added
			if (strlen($staticData) > 0)
			{
				// yes, add delimeter
				$staticData .= ', ';
			}
			// add destination 
			$staticData .= strtoupper($dest);
		}
		echo '<td>'. Output::htmlEncodeString($staticData).'</td>';
	}
		
	// Pilot - display stranded pilot's name only to coords and relevant agents
	// check for related SAR Agent
	$colspan++;
	$isSARAgent = $users->isSARAgent($charname, $row['id']);
	$isRescueAgent = $users->isRescueAgent($charname, $row['id']);
	if ($isCoord == 0 && $isSARAgent == 0 && $isRescueAgent == 0) {
		echo '<td><b>PROTECTED</b></td>';
	}
	else {
		echo '<td><a target="_blank" href="https://evewho.com/pilot/'. 
				$row['pilot'] .'">'.Output::htmlEncodeString($row['pilot']).'</a></td>';
	}
	
	// Status 
	// set status color: green = new, yellow = pending, orange = check-in needed
	switch ($status) {
		case 'new':
			$statuscellformat = ' style="background-color:green;color:white;"';
		break;
		case 'pending':
			$statuscellformat = ' style="background-color:yellow;color:black;"';
		break;
		default:
			$statuscellformat = '';
		break;
	}
	if ($finished == 0 && strtotime($row['lastcontact']) < strtotime('-7 day')) {
		$statuscellformat = ' style="background-color:orange;color:white;"';
	}
	$colspan++;
	echo '<td'. $statuscellformat .'>'.
			Output::htmlEncodeString(translateStatus($row['status'])).'</td>';
	
	// Last Contact - display date of last contact with stranded pilot
	$colspan++;
	echo '<td style="text-nowrap">'. date("M-d", strtotime($row['lastcontact'])) .'</td>';
	
	// Bounty - display max payout available for a successful locate/rescue in this system
	// display only in "summary" tables
	if ($summary == 1) {
		$colspan++;
		$basepay = 50000000; // base rate is 50 mil ISK
		$dailyincrease = 10000000; // daily increase is 10 mil ISK
		// get right-most number from WH class string for "WH Class multiplier"
		$whclassmult = intval(substr($row['Class'], -1));
		// (base x WH class multiplier) + (Days until rescued x daily increase amt)
		$payoutmax = ($basepay*$whclassmult)+(intval($row['daysopen'])*$dailyincrease);
		echo '<td style="text-nowrap">'. number_format(intval($payoutmax/2)) .'</td>';
	}
	
	// DETAIL ONLY COLUMNS BELOW [Dispatcher, Locator, Rescue Pilot(s), Notes]
	if ($summary == 0) {
		// Dispatcher - display name of Signaleer who opened request
		$colspan++;
		echo "<td>".Output::htmlEncodeString($row['startagent'])."</td>";
		
		// Locator - display name of Signaleer who located system (if any)
		$colspan++;
		echo '<td>';
		echo (empty($row['locateagent'])) ? 'N/A' : Output::htmlEncodeString($row['locateagent']);
		echo '</td>';
		
		// Rescue Pilot(s) - display name(s) of Signaleer who participated in live rescue (if any)
		$colspan++;
		$arrRescueAgents = $rescue->getRescueAgents($row['id']);
		echo '<td>';
		foreach ($arrRescueAgents as $value) {
			echo $value['pilot'] .'<br />';
		}
		echo '</td>';
		
		// NOTES
		if ($notes == 1 && ($isCoord == 1 || $isSARAgent == 1 || $isRescueAgent == 1)) {
			echo '</tr><tr>';
			if (($isCoord == 1 && $finished == 1) || ($finished == 0 && $noUpdate == 0)) {
				echo '<td>&nbsp;</td>';
			}
			echo '<td colspan="'.$colspan.'">';
			displayNotes($row, $isCoord, $isSARAgent);
			echo '</td>';
		}
	}
	echo "</tr>";
}

/**
 * Format notes as HTML within request table
 * @param unknown $row
 */
function displayNotes($row, $isCoord = 0, $isSARAgent = 0)
{
	$database = new Database();
	$rescue = new Rescue($database);
	$notes = $rescue->getNotes($row['id']);
	if (count($notes) > 0) {
		foreach($notes as $note) {
			echo '<div style="padding-left: 2em; text-indent: -2em;">';
			echo '['. Output::getEveDatetime($note['notedate']) .' // ';
			echo Output::htmlEncodeString($note['agent']) .']<br />';
			echo Output::htmlEncodeString($note['note']) .'<br />';
			echo '</div><br />';
		}
	}
}
?>