<?php 
// start <ul>
echo '<ul class="nav nav-tabs">';

$navtabsSystem = isset($system) ? $system : "";
$navtabsActiveSAR = isset($activeSAR) ? $activeSAR : "";

// ESRC TAB
if (strpos($_SERVER['PHP_SELF'], 'search.php') === false) {
	// inactive
	echo '<li><a href="search.php?sys=' . $navtabsSystem . '">Rescue Cache</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">Rescue Cache</a></li>';
}

// SAR TAB
if (strpos($_SERVER['PHP_SELF'], 'rescueoverview.php') === false) {
	// inactive
	echo '<li><a href="rescueoverview.php?sys=' . $navtabsSystem . '">Search &amp; Rescue' . $navtabsActiveSAR . '</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">Search &amp; Rescue</a></li>';
}

// STATS TAB
if (strpos($_SERVER['PHP_SELF'], 'stats.php') === false) {
	// inactive
	echo '<li><a href="stats.php">Statistics</a></li>';
}
else {
	// active
	echo '<li class="active"><a href="#" data-toggle="tab">Statistics</a></li>';
}

// ESR COORDINATOR TAB - only visible to ESR Coordinators
if ($isCoord == 1) {
	if (strpos($_SERVER['PHP_SELF'], 'esrcoordadmin.php') === false) {
		// inactive
		echo '<li><a href="esrcoordadmin.php">ESR Admin</a></li>';
	}
	else {
		// active
		echo '<li class="active"><a href="#" data-toggle="tab">ESR Admin</a></li>';
	}
}
	
// end <ul>
echo '</ul>';
?>