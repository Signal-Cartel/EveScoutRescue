<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

/**
 * Separate data entry code from data entry page content. 
 * 
 * This file contains all the logic to check data, update database and prepare error logging
 * 
 */

include_once '../class/db.class.php';
include_once '../class/caches.class.php';
include_once '../class/output.class.php';
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

 /**
  * Test provided input data to be valid.
 * @param unknown $data data to check
 * @return string processed and cleaned data
 */

function test_input($data) 
{
	$data = trim($data);
	//$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// check if the request is made by a POST request
if (isset($_POST['sys_note'])) {
	// yes, process the request
	$db = new Database();
	// create a new cache class
	$caches = new Caches($db);
	
	$cacheid = $activitydate = $pilot = $system = $status = $aidedpilot = ''; 
	$errmsg = $entrytype = $noteDate = '';

	$cacheid = test_input($_POST["CacheID"]);
	$activitydate = gmdate("Y-m-d H:i:s", strtotime("now"));
	$pilot = test_input($_POST["pilot"]);
	$system = test_input($_POST["sys_note"]);
	$status= test_input($_POST["status"]);
	$notes = trim($_POST["notes"]);
	$notes = substr($notes,0,70);
	$hasfil = ((isset($_POST['hasfil']) and $_POST['hasfil'] == 1) ? true : false);

	// check the system
	if (isset($system))
	{
		// make the system uppercase
		$system = strtoupper($system);
	}
	else
	{
		$errmsg = $errmsg . "No system name is set.";
	}
	
	//FORM VALIDATION
	$entrytype = 'note';
	if (empty($status)) { 
		$errmsg = $errmsg . "You must indicate the status of the cache you are tending.\n"; 
	}

	// check if an error is already set
	if (empty($errmsg))
	{
		// no, get cache info
		$cacheInfo = $caches->getCacheInfo($system, TRUE);
		
		// check if a cache exists
		if (empty($cacheInfo))
		{
			// no cache exists
			$errmsg = $errmsg . "No cache exists. It must have just expired!\n";
		}
	}
	//END FORM VALIDATION

	//display error message if there is one
	if (!empty($errmsg)) {
		$redirectURL = "search.php?sys=". $system ."&errmsg=". urlencode($errmsg);
	} 
	// otherwise, perform DB UPDATES
	else {
		// add new note
			if (!empty($notes)) {
				// add 'Note' Activity
				$caches->addActivity($cacheid, $system, $pilot, $entrytype, $activitydate, $notes, $aidedpilot, $status);
				//add note to cache
				$caches->appendNoteToCache($cacheid, $notes);	
		
		
				include_once '../class/discord.class.php';
				$discord = new Discord();
				// esrc coordinators channel on prod, and dev-test channel on dev
				$webhook = 'https://discordapp.com/api/webhooks/'.Config::DISCORD_SAR_COORD_TOKEN;
				$user = 'ESRC Notes';
				$alert = 0;
				$skip_the_gif = 1;
				// construct the message - URL is based on configuration
				$message = "$pilot (new note) in $system wrote:\n```$notes```";
				$dresponse = $discord->sendMessage($webhook, $user, $alert, $message, $skip_the_gif);	
			}	

			
	
	
		//redirect back to search page to show updated info
		$redirectURL = "search.php?sys=". $system;
	}
	//END DB UPDATES
	?>
	<script>
		window.location.replace("<?=$redirectURL?>")
	</script>
	<?php 
}

?>