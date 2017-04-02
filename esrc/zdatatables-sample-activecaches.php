<?php
// Check if logged in.
session_start();
if (isset($_SESSION['auth_characterid'])) {
	// Check if user is a member of the Enclave.
	if (!$_SESSION['auth_characteralliance'] == 'EvE-Scout Enclave') {
		echo "{\"data\":[],\"errorType\":\"AuthException\",\"errorMsg\":\"You must be a member of Eve-Scout Enclave to view this list!\"}";
		exit;
	}
}
else {
	echo "{\"data\":[],\"errorType\":\"AuthException\",\"errorMsg\":\"You must be logged-in to view this list!\"}";
	exit;
}

// Proceed if everything is fine.
include $_SERVER['DOCUMENT_ROOT'].'/class/class.db.php';
try {
	$db = new Db();
	$q = $db->prepare("SELECT System,Location,AlignedWith,Distance,Password,Status FROM cache WHERE System LIKE ? AND Status <> 'Expired' ORDER BY System");
	$q->execute(["J%"]); // obsolete, old
	$r = $q->fetchAll(PDO::FETCH_ASSOC);
	echo "{\"data\":".json_encode($r)."}";
}
// Pointless, could be caught by Exception.
catch (PDOException $e) {
	echo "{\"data\":[],\"errorType\":\"PDOException\",\"errorMsg\":\"".$e->getMessage()."\"}";
	exit;
}
catch (Exception $e) {
	echo "{\"data\":[],\"errorType\":\"Exception\",\"errorMsg\":\"".$e->getMessage()."\"}";
	exit;
}