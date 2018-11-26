<?php
// CALLED EACH DAY AT 6:01 ET BY CRONTAB ON PRODUCTION SERVER
//
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../class/db.class.php';
require_once '../class/caches.class.php';

$db = new Database();
$caches = new Caches($database);

// get count of active caches
$cnt = $caches->getActiveCount();

// INSERT current cnt into [activecaches] table
$errmsg = '';
$db->query("INSERT INTO cache_activity (ActiveCaches) VALUES (:cnt)");
$db->bind(':cnt', $cnt);
$db->execute();

// UPDATE yesterday's record with sow/tend counts
// get sown and tended counts for yesterday
$yesterday = date('Y-m-d', strtotime("-1 days"));
$db->query("SELECT DATE(`ActivityDate`) AS `day`,
				SUM(CASE WHEN `EntryType` = 'sower' THEN 1 ELSE 0 END) AS `Sown`,
				SUM(CASE WHEN `EntryType` = 'tender' THEN 1 ELSE 0 END) AS `Tended`
			FROM   `activity` `Activity`
			WHERE  DATE(ActivityDate) = :yesterday
			GROUP  BY DATE(`ActivityDate`)");
$db->bind(':yesterday', $yesterday);
$result = $db->single();
$db->closeQuery();
// run update
$db->query("UPDATE cache_activity SET Sown = :sown, Tended = :tended
			WHERE DATE(ActivityDate) = :yesterday");
$db->bind(':sown', $result['Sown']);
$db->bind(':tended', $result['Tended']);
$db->bind(':yesterday', $result['day']);
$db->execute();

if (!empty($errmsg)) {
	echo $errmsg;
} 
else {
	echo 'There are '. $cnt .' active caches as of ' . date("Y-m-d", strtotime("now")) . '. Sweet!';
	echo '<br />';
	echo 'On ' . $yesterday. ', ' . $result['Sown'] . ' caches were sown and ' . 
		$result['Tended'] . ' were tended. Nice!';
}
?>