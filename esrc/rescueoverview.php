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
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH."auth/login.php");
	// stop processing
	exit;
}

?>

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

// check for user roles
if (!isset($_SESSION['isAdmin'])){
	$isAdmin = $_SESSION['isAdmin'] = $users->isAdmin($charname);
	if ($isAdmin) $_SESSION['is911'] = $_SESSION['isCoord'] = $isAdmin;
}
else{
	$isAdmin = $_SESSION['isAdmin'];
}

// check for SAR Coordinator login
if (!isset($_SESSION['isCoord'])){
	$isCoord = $_SESSION['isCoord'] = ($isAdmin or $users->isSARCoordinator($charname) );	
}
else{
	$isCoord = $_SESSION['isCoord'];
}

// check for 911 Operator login
if (!isset($_SESSION['is911'])){
	$is911 = $_SESSION['is911'] = ($isCoord or $isAdmin or $users->is911($charname));	
}
else{
	$is911 = $_SESSION['is911'];
}


if(isset($_REQUEST['r']) and $_SERVER['HTTP_HOST'] == 'dev.evescoutrescue.com' ){
	$is911 = $_SESSION['is911'] = $isAdmin = $_SESSION['isAdmin'] = $isCoord = $_SESSION['isCoord'] = 0;
	if ($_REQUEST['r'] == 'a') $isAdmin = $_SESSION['isAdmin'] = 1;
	if ($_REQUEST['r'] == 'c' or $isAdmin) $is911 = $_SESSION['is911'] = $isCoord = $_SESSION['isCoord'] = 1;
	if ($_REQUEST['r'] == '9' or $isAdmin or $isCoord) $is911 = $_SESSION['is911'] = 1;	
}



$system = '';
if(isset($_REQUEST['sys'])) {
	if (ucfirst(htmlspecialchars_decode($_REQUEST['sys'])) != 'Thera'){
		$systems = new Systems($database);		
		$trysys = ucfirst(htmlspecialchars_decode($_REQUEST['sys']));
		if (! (substr ( $trysys, 0, 1 ) === 'J')) $trysys = 'J'. $trysys;	
		if ($systems->validatename($trysys) === 0) {
			$system = $trysys;
		}			
	}
	else{
		$system = 'Thera';
	}
}



if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }

?>
<body class="white" style="background-color: black;">
	<div class="container">
	
	<div class="row" id="header" style="padding-top: 20px;">
		<?php 
		include_once '../includes/top-right.php'; 
		include_once '../includes/top-left.php';
		include_once 'top-middle.php'; 
		?>
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
		<div class="col-md-12 message">
			<?php echo nl2br($errmsg); ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
}

// display rescue requests for a specific system
	if (!empty($system)) { 
	
			//  get active requests from database
			$data_active = $rescue->getSystemRequests22($system, 0, $isCoord);
			// get finished requests from database
			$data_finished = $rescue->getSystemRequests22($system, 1, $isCoord);
			
			$activeSARtitle = '';
			// check for active SAR request
			if (count($data_active) > 0) {
				$activeSARtitle = '&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #ff6464;"> ACTIVE SAR SYSTEM!</span>';
			}

			?>
			<div>
				<!-- System Name display -->
				<p class="systemName"><?=$system . $activeSARtitle ?></p>

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
			//  displayTable($data, $charname, $finished = 0, $system = NULL, $notes = 0, $isCoord = 0, $summary = 1, $noUpdate = 0)
			//  display active requests from database
			displayTable($data_active, $charname, 0, $system, 1, $isCoord, 0);		
			// display finished requests from database
			displayTable($data_finished, $charname, 1, $system, 1, $isCoord, 0);

	}	
// no system selected, so display all active requests
	else {
		//  displayTable($data, $charname, $finished = 0, $system = NULL, $notes = 0, $isCoord = 0, $summary = 1, $noUpdate = 0)
		//active requests
		$data = $rescue->getRequests23();
		displayTable($data, $charname, 0, $system, 0, $isCoord, 1);
	}
?>

<!-- MODAL includes -->
<?php
include 'modal_sar_new.php';
include 'modal_sar_manage.php';
include 'modal_sar_add-rescue-pilot.php';
?>

<script type="text/javascript">
	// auto-display edit modal when "req" parameter provided in querystring
	var url = window.location.href;
	if(url.indexOf('req=') != -1) {
	    $('#ModalSAREdit').modal('show');
	}

	// auto-display Add Pilot modal when "reqp" parameter provided in querystring
	var url = window.location.href;
	if(url.indexOf('reqp=') != -1) {
	    $('#ModalSARAddPilot').modal('show');
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
		case 'closed-zkill' : $result = "zKill Activity";
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
function displayTable($data, $charname, $finished = 0, $system = NULL, $notes = 0, $isCoord = 0, $summary = 1, $noUpdate = 0) {

	$strStatus = ($finished == 0) ? 'ACTIVE' : 'CLOSED';
	$strcols = ($finished == 0 && empty($system)) ? 'col-md-12 ' : '';
	
	echo '<div class="row">';
	echo '<div class="'. $strcols .'request">';
	echo '<span class="subhead">'. $strStatus .' REQUESTS</span>';
	if (empty($data)) {
		echo '<p>None for this system.</p>';
	} 
	else { 
		//echo print_r($data); // for testing
		echo '<table id="tbl'. $strStatus .'" class="table display" style="width: 90%;">';
		echo '	<thead>';
		echo '		<tr>';

		// display col for Update button (finished requests = coords) (opened requests = coords and involved pilots)
		echo '<th></th>';
		
		echo '			<th>Opened</th>';
		echo (empty($system)) ? '<th>System</th>' : '' ;
		echo (empty($system)) ? '<th>Class</th>' : '' ;
		echo (empty($system)) ? '<th>Statics</th>' : '' ;
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
	echo "<tr>";
	// create a new database connection
	//$database = new Database();
	// create object instances
	//$rescue = new Rescue($database);

	$status = $row['status'];
	$colspan = 0;
	
	$agents = array_key_exists('RescueAgents', $row) ? explode(',', $row['RescueAgents']) : Array();
	
	// data display different for coordinators and pilots involved in rescue
	$isSARAgent = (
		($charname == $row['startagent']) 
		or (($charname == $row['locateagent'])and($_SESSION['is911']==1)) 
		or (in_array($charname, $agents) and ($_SESSION['is911']==1)) 
		or $isCoord
	) ? 1 :0;
	
	$isRescueAgent = (($charname == $row['locateagent']) or $isCoord) ? 1 : 0;

	// "Update" button - display only for finished requests if coord logged in

		if (($finished and $isCoord) || ($finished == 0 && ($isSARAgent or $isRescueAgent) && $noUpdate == 0)) {
			echo '<td><a type="button" class="btn btn-danger adminbut" role="button" href="?sys='.
					$row['system'].'&amp;req='.$row['id'].'" style="color: white;">UPDATE</a></td>';
		}
		else{
			echo '<td>&nbsp;</td>';			
		}


		
	// Opened - date request was created
		$colspan++;
		echo '<td style="text-nowrap"><p class="admint">'. date("Y-m-d H:i", strtotime($row['requestdate'])) .'</p></td>';
	
	// System - name of J-space system to coords and involved pilots
		
		
		if (empty($system) and ($isCoord or $isSARAgent or $isRescueAgent)) {	
			$colspan++;		
			echo '<td><p class="admint"><a href="?sys='.ucfirst($row['system']).'">'.
					Output::htmlEncodeString(ucfirst($row['system'])).'</a></p></td>';	
		}
		else if(empty($system)){
			$colspan++;
			echo '<td><p class="admint"><em style="color:#999999">JXXXXX</em></p></td>';
		}

		
	// Class - class of J-space system
		if (empty($system)) {
			$colspan++;
			echo '<td><p class="admint">'. Output::htmlEncodeString($row['Class']).'</p></td>';
		}
			
	// Statics
		if (empty($system)) {
			$colspan++;
			
			$staticData = '';
			foreach (explode(',', $row['StaticWhInfo']) as $staticWhInfo) {
					
				$staticConnection = explode('/', $staticWhInfo);
				if (count($staticConnection) > 1){				
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
			}
			echo '<td><p class="admint">'. Output::htmlEncodeString($staticData).'</p></td>';
		}
		
	// Pilot - display stranded pilot's name only to coords and SAR agent
	// check for related SAR Agent
		$colspan++;

		if ($isCoord == 0 && $isSARAgent == 0) {
			echo '<td><p class="admint"><em style="color:#999999">PROTECTED</em></p></td>';
		}
		else {
			echo '<td><p class="admint"><a target="_blank" href="https://evewho.com/pilot/'. 
					$row['pilot'] .'">'.Output::htmlEncodeString($row['pilot']).'</a></p></td>';
		}
	
	// Status 
		// set status color: green = new, yellow = pending, orange = check-in needed
		switch ($status) {
			case 'new':
				$statuscellformat = ' style="color:lightgreen;"';
			break;
			case 'pending':
				$statuscellformat = ' style="color:yellow;"';
			break;
			default:
				$statuscellformat = '';
			break;
		}
		if ($finished == 0 && strtotime($row['lastcontact']) < strtotime('-7 day')) {
			$statuscellformat = ' style="color:orange;"';
		}
		$colspan++;
		echo '<td'. $statuscellformat .'><p class="admint">'.
				Output::htmlEncodeString(translateStatus($row['status'])).'</p></td>';
	
	// Last Contact - display date of last contact with stranded pilot
	$colspan++;
	echo '<td style="text-nowrap"><p class="admint">'. date("Y-m-d H:i", strtotime($row['lastcontact'])) .'</p></td>';
	
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
			echo '<td style="text-nowrap"><p class="admint">'. number_format(intval($payoutmax/2)) .'</p></td>';
		}
	
	// DETAIL ONLY COLUMNS BELOW [Dispatcher, Locator, Rescue Pilot(s), Notes]
		if ($summary == 0) {
			// Dispatcher - display name of Signaleer who opened request
			$colspan++;
			echo "<td><p class='admint'>" . Output::htmlEncodeString($row['startagent']) . "</p></td>";
			
			// Locator - display name of Signaleer who located system (if any)
			$colspan++;
			echo "<td><p class='admint'>";
			
			echo (empty($row['locateagent'])) ? 'N/A' : Output::htmlEncodeString($row['locateagent']);
			if ($_SESSION['isAdmin']) {
				echo '&nbsp;<a role="button" href="?sys='.
					$row['system'].'&reqp='.$row['id'].'&agent=loc"><span class="fa fa-pencil-square-o" style="color: #FFC107;" aria-hidden="true"></span></a></td>';
			}
			echo '</p></td>';
			
			// Rescue Pilot(s) - display name(s) of Signaleer who participated in live rescue (if any)
			$colspan++;
			$arrRescueAgents = $agents;
			echo '<td style="text-align: right;">';
			foreach ($arrRescueAgents as $pilot) {
				
				if ($_SESSION['isAdmin']) {
					echo '<form method="post" class="form-inline adminfrm" id="user_role_del'. $row['id'] .'" 
						action="rescueaction.php">';
					echo '<input type="hidden" name="rowid" value="'. $row['id'] .'">';
					echo '<input type="hidden" name="pilot" value="'. $pilot .'">';
					echo '<input type="hidden" name="action" value="RemovePilot">';
					
					echo '<input type="hidden" name="system" value="'. $system .'">';
					
					echo '<p class="admint">' . $pilot;
					echo '&nbsp;<button type="submit" class="btn btn-xs btn-dark" style="margin: 0px; padding: 0px;"><span class="fa fa-trash" style="color: #FFC107;" aria-hidden="true"></span></button></p>';
					echo '</form>';					
				}
				else{
					echo '<p class="admint" >' . $pilot . '</p>';
				}
			}
			if ($_SESSION['isAdmin']) {
				echo '<p class="admint" ><a role="button" href="?sys='.
					$row['system'].'&amp;reqp='.$row['id'].'&agent=res"><span class="fa fa-plus" style="color: #FFC107;" aria-hidden="true"></span></a></p>';
			}
			echo '</td>';
			
			// NOTES
			if ( $notes && ($isCoord or $isSARAgent) ) {
				displayNotes($row, $isCoord, $isSARAgent);
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
		
		echo '</tr><tr>';
		echo '<td colspan="5" style="border-top: none;">';
	
		foreach($notes as $note) {
			echo '<div style="padding-left: 8em; text-indent: -2em;">';
			echo '<p style = "font-size: 1em; line-height: 1.4em; opacity: 0.7;"><span style="font-size: 0.9em; opacity: .7; font-style: oblique;">';
			echo date("Y-m-d H:i", strtotime($note['notedate'])) .' - '; 
			echo Output::htmlEncodeString($note['agent']) .'</span><br />';
			echo Output::htmlEncodeString($note['note']) .'</p>';
			echo '</div>';
		}
		echo '</td>';
	}
}
?>