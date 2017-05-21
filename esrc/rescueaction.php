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

// determin current action
$action = $_REQUEST['action'];
// check if action is set
if (!isset($action))
{
	// no, set default start action
	$action = 'View';
}

// check if a valid character name is set
if (!isset($charname))
{
	// no, set a dummy char name
	$charname = 'charname_not_set';
}

// try to get data from request
// $rescueID = $_REQUEST['request'];
$rescueID = $_REQUEST['request'];

$system = $_REQUEST['system'];
$data['canrefit'] = $_REQUEST['canrefit'];
$data['launcher'] = $_REQUEST['launcher'];
$data['notes'] = $_REQUEST['notes'];

$errors = [];


if (!isset($data['launcher']))
{
	$data['launcher'] = 0;
}
if (!isset($data['canrefit']))
{
	$data['canrefit'] = 0;
}

// create a database instance
$database = new Database();
$rescue = new Rescue($database);

if ($action === 'View')
{
	displayRequestOverview ( $system );
}
else if ($action === 'New')
{
	// display the new request page with (optional) preset system
	$targetURL = './rescue.php';
	if (isset($system))
	{
		$targetURL .= '?system=' . Output::htmlEncodeString ( $data ['system'] );
	}
	header('Location: '.$targetURL);
	echo '<a href="'.$targetURL.'">Create a new rescue request</a>';
}
// process create action
else if ($action === 'Create')
{
	// mark data as ok
	$dataOK= TRUE;
	// add value checks!
	$pilot = $_REQUEST['pilot'];
	// check if pilot is set
	if (!isset($pilot) || trim($pilot) === '')
	{
		// pilot information not set
		$dataOK = FALSE;
	}
	// system ok
	// all values are set
	// same request is not active (system, pilot)
	if ($dataOK)
	{
		$database->beginTransaction();
	
		$newRescueID = $rescue->createRequest($system, $pilot, $data['canrefit'], $data['launcher'], $charname);
	
		// insert rescue note if set
		if (isset($data['notes']) && $data['notes'] != '')
		{
			$rescue->createRescueNote($newRescueID, $charname, $data['notes']);
		}
		$database->endTransaction();
	
		// switch display to overview with current system
		displayRequestOverview($data ['system'] );
	}
	else 
	{
		// data was wrong. Display input mask with wrong data
		echo "Wrong data entered! Need fix";
	}
}
else if($action === 'Edit')
{
	// display an existing request edit form
	displayManageRequest ( $rescueID );
}
else if($action === 'AddNote')
{
	// insert rescue note
	if (isset($data['notes']) && $data['notes'] != '')
	{
		$database->beginTransaction();
		// get new rescue ID
	
		$rescue->createRescueNote($rescueID, $charname, $data['notes']);	
		$database->endTransaction();
	}
	
	displayManageRequest ( $rescueID );
}
else if ($action === 'UpdateRequest')
{
	// update request data
// 	print_r($_REQUEST);
	$rescueID = $_REQUEST['request'];

	// check for special status and values if request is closed
	$closeAgent = NULL;
	$finished = 0; 
	if($_REQUEST['status'] === 'closed')
	{
		$closeAgent = $charname;
		$finished = 1;
	}
	
	$database->beginTransaction();
	// update status
	$database->query("update rescuerequest set status = :status, closeagent = :closeagent, finished = :finished where id = :rescueid");
	$database->bind(":status", $_REQUEST['status']);
	$database->bind(":closeagent", $closeAgent);
	$database->bind(":finished", $finished);
	$database->bind(":rescueid", $rescueID);
	$database->execute();
	
	// check and set reminder date
	if (isset($_REQUEST['contacted']))
	{
// 		$database->query("update rescuerequest set lastcontact = current_timestamp() where id = :rescueid");
// 		$database->bind(":rescueid", $rescueID);
// // 		$database->debugDumpParams();
// 		$database->execute();
		$rescue->registerContact($rescueID);
	}

	// get the set reminder days
	$reminder = $_REQUEST['reminder'];
	// check if a reminder is set
	if (isset($reminder) && is_numeric(trim($reminder)))
	{
// 		$database->query("update rescuerequest set reminderdate = date_add(current_timestamp(), INTERVAL :reminder day) where id = :rescueid");
// 		$database->bind(":reminder", $_REQUEST['reminder']);
// 		$database->bind(":rescueid", $rescueID);
// // 		$database->debugDumpParams();
// 		$database->execute();
		$rescue->setReminder($rescueID, trim($reminder));
	}
// 	print_r($_REQUEST);
	$database->endTransaction();
	
	displayManageRequest ( $rescueID );
}
else
{
	echo "Unknown action: ".Output::htmlEncodeString($action);
}


/**
 * @param requestID
 */
function displayManageRequest($requestID = NULL) {
	// display request manage page again
	$targetURL = './rescuemanage.php';
	if (isset($requestID))
	{
		$targetURL .= '?request=' . Output::htmlEncodeString ( $requestID );
	}
	header('Location: '.$targetURL);
	echo '<a href="'.$targetURL.'">Edit rescue request '.Output::htmlEncodeString($requestID).'</a>';
}


/**
 * Redirect to the request overview page
 * @param system
 */
function displayRequestOverview($system = NULL) {
	$targetURL = './rescueoverview.php';
	if (isset($system) && $system != '')
	{
		$targetURL .= '?system=' . Output::htmlEncodeString ( $system  );
	}
	header('Location: '.$targetURL);
	echo '<a href="'.$targetURL.'">Rescue request overview</a>';
}

?>