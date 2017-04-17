<?php
// API for copilot
// once we release, we may want to devise some security on this so the API is not open
// place this file in the ESRC folder and name it copilot.php
// request format then will be
// http://www.evescoutrescue.com/esrc/copilot.php?cache=J123456


include_once '../class/db.class.php';
$cache = strtoupper($_GET["cache"]);

try {
	$db = new Database();
	
	if ($cache && strlen($cache) == 7) {
	//check for "No Sow" system
	$db->query("SELECT System, DoNotSowUntil FROM wh_systems 
				WHERE System = :system AND DoNotSowUntil > CURDATE()");
	$db->bind(':system', $cache);
	$row = $db->single();
		if (!empty($row)) {
			echo json_encode($row);
		}
		//we can sow here
		else {
			//check for presence of a cache in system
			$db->query("SELECT System, Location, Status, ExpiresOn, InitialSeedDate 
						FROM cache WHERE System LIKE :system AND Status <> 'Expired'");
			$db->bind(':system', $cache);
			$row = $db->single();
			if (count($row) < 1) {
				throw new Exception("none");
			}
			echo json_encode($row);
		}
	}
	//$cache not present or wrong format
	else {
		throw new Exception("invalid");
	}
}
catch (Exception $e) {
	$errorMsg = $e->getMessage();
	if (($errorMsg<>"invalid") && ($errorMsg<>"none")) {
		echo "error";
	}
	else {
		echo $errorMsg;
	}
}

?>