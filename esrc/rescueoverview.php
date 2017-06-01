<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

?>
<html>

<head>
	<?php 
	$pgtitle = 'SAR request overview';
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
</head>

<?php
require_once '../class/db.class.php';
require_once '../class/rescue.class.php';
require_once '../class/systems.class.php';
require_once '../class/output.class.php';

$database = new Database();

// create a rescue object instance
$rescue = new Rescue($database);

$system = '';
if (isset($_REQUEST['sys'])) {
	$system = htmlspecialchars_decode($_REQUEST["sys"]);
}

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }

// create an update action

?>
<body class="white">
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<?php include_once 'top-middle.php'; ?>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>

<ul class="nav nav-tabs">
	<li><a href="search.php?sys=<?=$system?>">Rescue Cache</a></li>
	<li class="active"><a href="#">Search &amp; Rescue</a></li>
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
	switch ($status)
	{
		case 'closed-rescued' : $result = "Pilot rescued by SAR";
		break;
		case 'closed-escaped' : $result = "Pilot escaped by own action";
		break;
		case 'closed-escapedlocals' : $result = "Pilot rescued by locals";
		break;
		case 'closed-destruct' : $result = "Pilot used self destruct";
		break;
		case 'closed-noresponse' : $result = "Pilot did not respond";
		break;
		
		default:
			$result = $status;
			break;
	}
	return $result;
}

/**
 * Format a request as HTML table in output
 * @param unknown $row
 * @param number $finished
 * @param unknown $system
 * @param number $notes
 */
function displayTable($data, $finished = 0, $system = NULL, $notes = 0)
{
	$strStatus = ($finished == 0) ? 'Active' : 'Finished';
	
	echo '<div class="request">';
	echo '<span class="sechead">'. $strStatus .' Requests</span>';
	if (empty($data)) {
		echo '<p>None for this system.</p>';
	} 
	else { 
		echo '<table class="table" style="width: auto;">';
		echo '	<thead>';
		echo '		<tr>';
		echo '			<th></th>';
		echo '			<th>Created</th>';
		echo (!empty($system)) ? '' : '<th>System</th>';
		echo '			<th>Pilot</th>';
		echo '			<th>Status</th>';
		echo '			<th>Agent</th>';
		echo '			<th>Last&nbsp;Contact</th>';
		echo ($notes == 0) ? '' : '<th>Notes</th>';
		echo '		</tr>';
		echo '	</thead>';
		echo '	<tbody>';
		foreach ($data as $row) {
			displayLine($row, $finished, $system, $notes);
		}
		echo '	</tbody>';
		echo '</table>';
	}
	echo '</div>';
}

/**
 * Format a request as HTML table row in output
 * @param unknown $row
 * @param number $finished
 * @param unknown $system
 * @param number $notes
 */
function displayLine($row, $finished = 0, $system = NULL, $notes = 0)
{
	echo "<tr>";
	echo '<td><a type="button" class="btn btn-danger" role="button" href="?sys='.$system.'&amp;req='.$row['id'].'">Update</a></td>';
	echo "<td>".Output::getEveDate($row['requestdate'])."</td>";
	echo (!empty($system)) ? '' : '<td><a href="?sys='.$row['system'].'">'.Output::htmlEncodeString($row['system']).'</a></td>';
	echo '<td><a target="_blank" href="https://gate.eveonline.com/Profile/'. $row['pilot'] .'">'.Output::htmlEncodeString($row['pilot'])."</a></td>";
	echo "<td>".Output::htmlEncodeString(translateStatus($row['status']))."</td>";
	echo "<td>".Output::htmlEncodeString($row['startagent'])."</td>";
	echo "<td>".Output::getEveDate($row['lastcontact'])."</td>";
	// NOTES
	if ($notes == 1) {
		echo '<td>';
		displayNotes($row);
		echo '</td>';
	}
	echo "</tr>";
}

/**
 * Format notes as HTML within request table
 * @param unknown $row
 */
function displayNotes($row)
{
	$database = new Database();
	$rescue = new Rescue($database);
	$notes = $rescue->getNotes($row['id']);
	if (count($notes) > 0) {
		foreach($notes as $note) {
			echo '['. date("M-d", strtotime($note['notedate'])) .' // ';
			echo Output::htmlEncodeString($note['agent']) .']<br />';
			echo '&nbsp;&nbsp;&nbsp;'. Output::htmlEncodeString($note['note']) .'<br />';
		}
	}
}

if (!empty($system)) { 
	$systems = new Systems($database);
	if ($systems->validatename($system) === 0) {
	?>
	
	<div>
		<a type="button" class="btn btn-danger"	role="button" data-toggle="modal"
			data-target="#ModalSARNew">New SAR</a>
	</div>
	<div class="ws"></div>
	
	<?php // get active requests from database
	$data = $rescue->getSystemRequests($system, 0);
	displayTable($data, 0, $system, 1);
	
	// get finished requests from database
	$data = $rescue->getSystemRequests($system, 1);
	displayTable($data, 1, $system);
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
else {
	$data = $rescue->getRequests();
	displayTable($data, 0, $system);
}
?>

<!-- MODAL includes -->
<?php
include 'modal_sar_new.php';
include 'modal_sar_manage.php';
?>

<!-- auto-display edit modal when "req" parameter provided in querystring -->
<script type="text/javascript">
	var url = window.location.href;
	if(url.indexOf('req=') != -1) {
	    $('#ModalSAREdit').modal('show');
	}
</script>

</div>
</body>
</html>