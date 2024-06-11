<?php 
// start <ul>
echo '<ul class="nav nav-tabs">';

$navtabsSystem = isset($system) ? $system : "";
$navtabsActiveSAR = isset($activeSAR) ? $activeSAR : "";

// ESRC TAB
if (strpos($_SERVER['PHP_SELF'], 'search.php') === false) {
	// inactive
	echo '<li><a href="search.php?sys=' . $navtabsSystem . '">CACHE</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">CACHE</a></li>';
}

// SAR TAB
if (strpos($_SERVER['PHP_SELF'], 'rescueoverview.php') === false) {
	// inactive
	echo '<li><a href="rescueoverview.php?sys=' . $navtabsSystem . '">SAR' . $navtabsActiveSAR . '</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">SAR</a></li>';
}

// STATS TAB
if (strpos($_SERVER['PHP_SELF'], 'stats.php') === false) {
	// inactive
	echo '<li><a href="stats.php">STATS</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">STATS</a></li>';
}

// THERA TAB - Removed Dec 1, 2023
/*
if (strpos($_SERVER['PHP_SELF'], 'theraoverview.php') === false) {
	// inactive
	echo '<li><a href="theraoverview.php">THERA SCAN</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">THERA SCAN</a></li>';
}
*/

// PVE SHARER TAB - Removed Feb 2023
/*
if (strpos($_SERVER['PHP_SELF'], 'pve_sharer.php') === false) {
	// inactive
	echo '<li><a href="pve_sharer.php">SITE TRACKER</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">SITE TRACKER</a></li>';
}
*/

// STORM TRACKER TAB
if (strpos($_SERVER['PHP_SELF'], 'storm_tracker.php') === false) {
	// inactive
	echo '<li><a href="storm_tracker.php">STORM TRACKER</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">STORM TRACKER</a></li>';
}

// ESR COORDINATOR TAB - only visible to ESR Coordinators
if ($isCoord == 1) {
	if (strpos($_SERVER['PHP_SELF'], 'esrcoordadmin.php') === false) {
		// inactive
		echo '<li><a href="esrcoordadmin.php">ESR ADMIN</a></li>';
	}
	else {
		// active
		echo '<li class="active"><a href="#" data-toggle="tab">ESR ADMIN</a></li>';
	}
}
	
// end <ul>
echo '</ul>';
?>