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
$data['request'] = $_REQUEST['request'];
$data['system'] = $_REQUEST['system'];
$data['pilot'] = $_REQUEST['pilot'];
$data['canrefit'] = $_REQUEST['canrefit'];
$data['launcher'] = $_REQUEST['launcher'];
$data['notes'] = $_REQUEST['notes'];

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

if ($action === 'View')
{
	displayRequestOverview ( $data['system'] );
}
else if ($action === 'New')
{
	$targetURL = './rescue.php';
	if (isset($data['system']))
	{
		$targetURL .= '?system=' . Output::htmlEncodeString ( $data ['system'] );
	}
	header('Location: '.$targetURL);
	echo '<a href="'.$targetURL.'">Create a new rescue request</a>';
}
// process create action
else if ($action === 'Create')
{
	// add value checks!
	// system ok
	// all values are set
	// same request is not active (system, pilot)
	
	$database->beginTransaction();
	$database->query("insert into rescuerequest(system, pilot, canrefit, launcher, startagent) values(:system, :pilot, :canrefit, :launcher, :agent)");
	$database->bind(":system", $data['system']);
	$database->bind(":pilot", $data['pilot']);
	$database->bind(":canrefit", $data['canrefit']);
	$database->bind(":launcher", $data['launcher']);
	$database->bind(":agent", $charname);

	// execute insert query
	$database->execute();
	// get new rescue ID
	$rescueID = $database->lastInsertId();
// 	echo "<br> Creaded request: ".$rescueID.": Note:".$data['notes']."<br>";
	// insert rescue note
	if (isset($data['notes']) && $data['notes'] != '')
	{
		$database->query("insert into rescuenote(rescueid, note, agent) values(:rescueid, :note, :agent)");
		$database->bind(":rescueid", $rescueID);
		$database->bind(":agent", $charname);
		$database->bind(":note", $data['notes']);
		
		// execute the insert query
		$database->execute();
	}
	$database->endTransaction();
	
// 	echo "Create action";

	displayRequestOverview($data ['system'] );
	
// 	displayOverview();
// 	$targetURL = './rescueoverview.php';
// 	if (isset($data['system']))
// 	{
// 		$targetURL .= '?system=' . Output::htmlEncodeString ( );
// 	}
// 	header('Location: '.$targetURL);
}
else if($action === 'Edit')
{
	displayManageRequest ( $data['request'] );
}
else if($action === 'AddNote')
{
	// insert rescue note
	if (isset($data['notes']) && $data['notes'] != '')
	{
		$database->beginTransaction();
		// get new rescue ID
		$rescueID = $_REQUEST['request'];
	
		$database->query("insert into rescuenote(rescueid, note, agent) values(:rescueid, :note, :agent)");
		$database->bind(":rescueid", $rescueID);
		$database->bind(":agent", $charname);
		$database->bind(":note", $data['notes']);
	
		$database->execute();
		
		$database->endTransaction();
	}
	
	displayManageRequest ( $data['request'] );
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
		$database->query("update rescuerequest set lastcontact = current_timestamp() where id = :rescueid");
		$database->bind(":rescueid", $rescueID);
// 		$database->debugDumpParams();
		$database->execute();
	}

	// check if a reminder is set
	if (isset($_REQUEST['reminder']) && is_numeric($_REQUEST['reminder']))
	{
		$database->query("update rescuerequest set reminderdate = date_add(current_timestamp(), INTERVAL :reminder day) where id = :rescueid");
		$database->bind(":reminder", $_REQUEST['reminder']);
		$database->bind(":rescueid", $rescueID);
// 		$database->debugDumpParams();
		$database->execute();
	}
// 	print_r($_REQUEST);
	$database->endTransaction();
	
	displayManageRequest ( $data['request'] );
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