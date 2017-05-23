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
$whsystem = new Systems($database);

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
	$errorCount = 0;
	// mark data as ok
	$dataOK= TRUE;
	// add value checks!
	$pilot = $_REQUEST['pilot'];
	// check if pilot is set
	if (isset($pilot))
	{
		// remove spaces
		$pilot = trim($pilot);
	}
	
	// check if pilot is set and no empty string
	if (!isset($pilot) || trim($pilot) === '')
	{
		// pilot information not set
		$error[$errorCount++] = "Pilot name is missing!";
		$dataOK = FALSE;
	}
	else if ($rescue->isRequestActive($pilot) > 0)
	{
		// already an active SAR request for pilot 
		$error[$errorCount++] = "Pilot has a request open!";
		$dataOK = FALSE;
		}
	// system ok
	if (!isset($system) || trim($system) === '')
	{
		// system information not set
		$error[$errorCount++] = "System name is missing!";
		$dataOK = FALSE;
	}
	else
	{
		if ($whsystem->validatename($system) == 1)
		{
			// system information is wrong
			$error[$errorCount++] = "System name is no WH system!";
			$dataOK = FALSE;
		}
	}
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
// 		echo "Wrong data entered! Need fix";
		require_once './rescue.php';
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
	$rescueID = $_REQUEST['request'];

	$database->beginTransaction();
	// update status
	$rescue->setStatus($rescueID, $_REQUEST['status']);
	// check and set reminder date
	if (isset($_REQUEST['contacted']))
	{
		$rescue->registerContact($rescueID);
	}

	// get the set reminder days
	$reminder = $_REQUEST['reminder'];
	// check if a reminder is set
	if (isset($reminder) && is_numeric(trim($reminder)))
	{
		$rescue->setReminder($rescueID, trim($reminder));
	}
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
	$targetURL = './rescueoverview.php?';
	$finished = $_REQUEST['finished'];
	if (isset($finished) && $finished != '')
	{
		$targetURL .= 'finished=' . Output::htmlEncodeString ( $finished  ).'&';
	}
	if (isset($system) && $system != '')
	{
		$targetURL .= 'system=' . Output::htmlEncodeString ( $system  );
	}
	header('Location: '.$targetURL);
	echo '<a href="'.$targetURL.'">Rescue request overview</a>';
}

?>