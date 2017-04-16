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
		$db->query("SELECT System,Location,Status,ExpiresOn,InitialSeedDate FROM cache WHERE System LIKE :system AND Status <> 'Expired' ORDER BY System");
		$db->bind(':system', $cache);
		$row = $db->single();
	}
	else {
		throw new Exception("invalid");
		
	}

	if (count($row) < 1) {
		throw new Exception("none");
	}
	echo json_encode($row);
}
catch (Exception $e) {
	$errorMsg = $e->getMessage();
	if (($errorMsg<>"invalid") && ($errorMsg<>"none")){
		echo "error";
	}
	else{
		echo $errorMsg;
	}
}

?>