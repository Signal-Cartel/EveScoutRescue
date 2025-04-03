<?php
require_once '../class/users.class.php';
require_once '../class/config.class.php';
require_once '../class/mmmr.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/leaderboard_sar.class.php';
require_once '../class/caches.class.php';
require_once '../class/systems.class.php';

if (!isset($database)) { $database = new Database();}
if (!isset($rescue)) {$rescue = new Rescue($database);}
if (!isset($caches)) {$caches = new Caches($database);}
if (!isset($systems)) {$systems = new Systems($database);}
//if (!isset($sarleaderBoard)) {$sarleaderBoard = new SARLeaderboard($database);}
//if (!isset($leaderBoard)) {$leaderBoard = new Leaderboard($database);}



$cacheFile = 'hourly_data.json'; // Cache file
$cacheTime = 3600; // 1 hour (in seconds)
$expireDays = 5;
// Check if cache exists and is valid
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    // Load from cache
    $data = json_decode(file_get_contents($cacheFile), true);
    
    // If JSON decode fails, invalidate cache and refetch data
    if (!is_array($data)) {
        unlink($cacheFile); // Delete corrupt cache file
        $data = null;
    }
} else {
    $data = null; // Force fresh data fetch if cache expired
}

if (!$data) {
    // Cache expired or missing, fetch fresh data

    $data = [
        'ctrrescues' => $rescue->getRescueCount('closed-esrc'),
		'ctrSARrescues' => $rescue->getRescueCount('closed-rescued'),
        'ctrsown' => $caches->getSownTotalCount(),
        'ctrtended' => $caches->getTendTotalCount(),
        'ctractive' => $caches->getActiveCount(),
        'lockedSys' => $systems->getLockedCount(),        
        'toexpire' => $caches->expireInDays($expireDays)
    ];

    // Save the new data to cache with LOCK_EX for safe writing
    file_put_contents($cacheFile, json_encode($data), LOCK_EX);
}

// Assign values to variables for use in the main script
$ctrrescues = $data['ctrrescues'];
$ctrsown = $data['ctrsown'];
$ctrtended = $data['ctrtended'];
$ctractive = $data['ctractive'];
$lockedSys = $data['lockedSys'];    
$toexpire = $data['toexpire'];

$ctrESRCrescues = $data['ctrrescues'];
$ctrSARrescues = $data['ctrSARrescues'];
$ctrAllRescues = intval($ctrESRCrescues) + intval($ctrSARrescues);

?>


