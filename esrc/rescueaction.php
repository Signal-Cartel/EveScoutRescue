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
//require_once '../class/output.class.php';

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
	$targetURL = './rescueoverview.php';
	if (isset($data['system']) && $data['system'] != '')
	{
		$targetURL .= '?system=' . Output::htmlEncodeString ( $data ['system'] );
	}
	header('Location: '.$targetURL);
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
	
	// insert rescue note
	if (isset($data['notes']) && $data['notes'] != '')
	{
		$database->query("insert into rescuenote(rescueid, note, agent) values(:rescueid, :note, :agent)");
		$database->bind(":rescueid", $rescueID);
		$database->bind(":agent", $charname);
		$database->bind(":note", $data['notes']);
		
		$database->execute();
	}
	$database->endTransaction();
	
// 	echo "Create action";
	
// 	displayOverview();
	$targetURL = './rescueoverview.php';
	if (isset($data['system']))
	{
		$targetURL .= '?system=' . Output::htmlEncodeString ( $data ['system'] );
	}
	header('Location: '.$targetURL);
}
else if($action === 'Edit')
{
	$targetURL = './rescuemanage.php';
	if (isset($data['request']))
	{
		$targetURL .= '?request=' . Output::htmlEncodeString ( $data ['request'] );
	}
	header('Location: '.$targetURL);
	echo '<a href="'.$targetURL.'">Edit rescue request '.Output::htmlEncodeString($data['request']).'</a>';
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
	
	// display request manage page again
	$targetURL = './rescuemanage.php';
	if (isset($data['request']))
	{
		$targetURL .= '?request=' . Output::htmlEncodeString ( $data ['request'] );
	}
	header('Location: '.$targetURL);
	echo '<a href="'.$targetURL.'">Edit rescue request '.Output::htmlEncodeString($data['request']).'</a>';
}	
else
{
	echo "Unknown action: ".Output::htmlEncodeString($action);
}

?>