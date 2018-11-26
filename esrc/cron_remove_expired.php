<?php
// CALLED EACH DAY AT 7:05 ET VIA CRONTAB ON PRODUCTION SERVER

include_once '../class/db.class.php';

$db = new Database();

// get all expired rows
$db->query("SELECT * FROM cache WHERE ExpiresOn < CURDATE() AND Status <> 'Expired' ORDER BY LastUpdated DESC");
$rows = $db->resultset();
$db->closeQuery();

// for each expired record, update Status in [cache] table
$errmsg = '';
$cacheid = '';
foreach ($rows as $val) {
	$db->query("UPDATE cache SET Status = 'Expired' WHERE CacheID = :cacheID");
	$db->bind(':cacheID', $val['CacheID']);
	$db->execute();
	$cacheid = $cacheid . $val['CacheID'] . ', ';
}

if (!empty($errmsg)) {
	echo $errmsg;
} 
else {
	if (empty($cacheid)) {
		echo 'No unexpired caches had expiration dates on ' . date("Y-m-d", strtotime("now")) . ', so no caches were expired today. All good!';
	}
	else {
		echo 'The following caches were successfully expired on ' . date("Y-m-d", strtotime("now")) . ': ';
		echo $cacheid;
	}
}
?>