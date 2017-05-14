<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

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

// try to get data from request
$data['request'] = $_REQUEST['request'];
$data['system'] = $_REQUEST['system'];
$data['pilot'] = $_REQUEST['pilot'];
$data['canrefit'] = $_REQUEST['canrefit'];
$data['notes'] = $_REQUEST['notes'];

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
	$database = new Database();
	
	// add value checks!
	// system ok
	// all values are set
	// same request is not active (system, pilot)
	
	$database->beginTransaction();
	$database->query("insert into rescuerequest(system, pilot, canrefit, note) values(:system, :pilot, :canrefit, :note)");
	$database->bind(":system", $data['system']);
	$database->bind(":pilot", $data['pilot']);
	$database->bind(":canrefit", $data['canrefit']);
	$database->bind(":note", $data['notes']);
	
	$database->execute();
	
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
	echo "Edit action";
}
else
{
	echo "Unknown action: ".Output::htmlEncodeString($action);
}

?>