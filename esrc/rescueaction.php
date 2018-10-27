<?php

/**
 * Target for all rescue related actions.
 * 
 * The action is checked and the corresponding process is made.
 * 
 * The action redirects to a display page after action is processed.
 */


// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

require_once '../class/output.class.php';
require_once '../class/db.class.php';
require_once '../class/rescue.class.php';
require_once '../class/systems.class.php';
require_once '../class/discord.class.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';

// check if the user is alliance member
if (!Users::isAllianceUserSession())
{
	// void the session entries on 'attack'
	session_unset();
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH);
	// stop processing
	exit;
}

// determine current action
$action = $_REQUEST['action'];
// check if action is set
if (!isset($action)) {
	// no, set default start action
	$action = 'View';
}

// check if a valid character name is set
if (!isset($charname)) {
	// no, set a dummy char name
	$charname = 'charname_not_set';
}

// try to get data from request
$rescueID = $_REQUEST['request'];
$system = $_REQUEST['system'];
$data['canrefit'] = $_REQUEST['canrefit'];
$data['launcher'] = $_REQUEST['launcher'];
$data['notes'] = $_REQUEST['notes'];

$errors = [];
$errmsg = '';


if (!isset($data['launcher'])) { $data['launcher'] = 0; }
if (!isset($data['canrefit'])) { $data['canrefit'] = 0; }

// create a database instance
$database = new Database();
$rescue = new Rescue($database);
$whsystem = new Systems($database);

if ($action === 'View') {
	displayRequestOverview ( $system );
}

// process create action
else if ($action === 'Create')
{
	$errorCount = 0;
	// mark data as ok
	$dataOK= TRUE;
	// add value checks!
	$pilot = $_REQUEST['pilot'];
	// check if pilot is set
	if (isset($pilot)) {
		// remove spaces
		$pilot = trim($pilot);
	}
	
	// check if pilot is set and no empty string
	if (!isset($pilot) || trim($pilot) === '') {
		// pilot information not set
		$error[$errorCount++] = "Pilot name is missing!";
		$dataOK = FALSE;
	}
	else if ($rescue->isRequestActive($pilot) > 0) {
		// already an active SAR request for pilot 
		$error[$errorCount++] = "Pilot has a request open!";
		$dataOK = FALSE;
	}
	// all values are set
	// same request is not active (system, pilot)
	if ($dataOK) {
		$database->beginTransaction();
	
		$newRescueID = $rescue->createRequest($system, $pilot, $data['canrefit'], $data['launcher'], $charname);
	
		// insert rescue note if set
		if (isset($data['notes']) && $data['notes'] != '') {
			$rescue->createRescueNote($newRescueID, $charname, $data['notes']);
		}
		$database->endTransaction();
	
		// switch display to overview with current system
		displayRequestOverview($system);
		
		// send notification to Discord
		// discord webhook with token - channel and token part are part of config XXXX/abcdef
		$webHook = 'https://discordapp.com/api/webhooks/'.Config::DISCORD_SAR_COORD_TOKEN;
		$user = 'SAR System';
		$alert = 1;
		$skip_the_gif = 1;
		// construct the message - URL is based on configuration
		$message = "A new SAR request has just been entered by $charname. [Overview page](".Config::ROOT_PATH."esrc/rescueoverview.php) - [Check Chains for $system](https://evescoutrescue.com/copilot/data/chains.php?system=$system)";

		$result = Discord::sendMessage($webHook, $user, $alert, $message, $skip_the_gif);
	}
	else {
		// data was wrong. Display input mask with wrong data
		foreach ($error as $e) {
			$errmsg = $errmsg. Output::htmlEncodeString($e)."<br />";
		}
		$redirectURL = "rescueoverview.php?sys=". $system ."&errmsg=". urlencode($errmsg);
		?>
		<script>
			window.location.replace("<?=$redirectURL?>")
		</script>
		<?php 
	}
}

else if ($action === 'UpdateRequest')
{
	// update request data
	$rescueID = $_REQUEST['request'];

	$database->beginTransaction();
	// update status
	$rescue->setStatus($rescueID, $_REQUEST['status'], $charname);
	// check and set reminder date
	if (isset($_REQUEST['contacted'])) {
		$rescue->registerContact($rescueID);
	}

	// NOTE: REMINDER STUFF IS NOT VISIBLE in UI currently
	// get the set reminder days
	$reminder = $_REQUEST['reminder'];
	// check if a reminder is set
	if (isset($reminder) && is_numeric(trim($reminder))) {
		$rescue->setReminder($rescueID, trim($reminder));
	}
	// NOTE: REMINDER STUFF IS NOT VISIBLE in UI currently
	
	// insert rescue note
	if (isset($data['notes']) && $data['notes'] != '') {
		$rescue->createRescueNote($rescueID, $charname, $data['notes']);
	}
	$database->endTransaction();
	
	displayRequestOverview ( $_REQUEST['system'] );
}
else
{
	echo "Unknown action: ".Output::htmlEncodeString($action);
}

/**
 * Redirect to the request overview page
 * @param system
 */
function displayRequestOverview($system = NULL) {
	$targetURL = 'rescueoverview.php?';
	if (isset($system) && $system != '') {
		$targetURL .= 'sys=' . Output::htmlEncodeString($system);
	}
	header('Location: '.$targetURL);
}

?>