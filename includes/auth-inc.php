<?php
// set timeout to a lower bound of 2 hours
// (see https://stackoverflow.com/questions/8311320/how-to-change-the-session-timeout-in-php)
// server should keep session data for AT LEAST 2 hours
ini_set('session.gc_maxlifetime', 7200);
// each client should remember their session id for EXACTLY 2 hour
session_set_cookie_params(7200);
session_start(); // ready to go!

// - Set arrays of different page types
// - If it's not in one of these arrays, it is a public page that does not require login to access
$pgsAdmin    = array('/esrc/payoutadmin.php');
$pgsAlliance = array('/esrc/data_entry.php','/esrc/search.php','/esrc/rescueoverview.php');

//populate array of admin users
$admins      = array('Thrice Hapus','Mynxee','Johnny Splunk');
$sarcoords   = array('Lucas Ballard','Igaze','Triffton Ambraelle');

//populate display strings for authenticated users
if (isset($_SESSION['auth_characterid'])) {
	$charimg    = '<img src="https://image.eveonline.com/Character/'.
				$_SESSION['auth_characterid'].'_64.jpg">';
	$charname   = $_SESSION['auth_charactername'];
	$chardiv    = '<div style="text-align: center;">'.$charimg.'<br />' .
				  '<div><span class="white">' .$charname. '</span><br />' .
				  '<span class="descr"><a href="../auth/logout.php">logout</a></span>' .
				  '</div></div>';
}

//populate display string for non-authenticated users
else {
	$chardiv  = '<a href="../auth/login.php">'.
				'<img src="../img/EVE_SSO_Login_Buttons_Small_Black.png"></a>';
}

// Only run through auth routines if we are NOT on localhost
if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
	// We are on an admin page...
	if (in_array($_SERVER['PHP_SELF'], $pgsAdmin)) {
		//1a. ...and user is logged in...
		if (isset($_SESSION['auth_characterid'])) {
			//2. ...but user is not an admin, so redirect back to home
			if (array_search($charname, $admins) === false) {
				header("Location: /");
			}
		}
		//1b. ...and user is not logged in, so redirect
		else {
			login_redirect();
		}
	}
	// We are on an Alliance-only page...
	elseif (in_array($_SERVER['PHP_SELF'], $pgsAlliance)) {
		//1a. ...and user is logged in...
		if (isset($_SESSION['auth_characterid'])) {
			//2. ...but user is not part of EvE-Scout alliance, so redirect back to home
			if (!$_SESSION['auth_characteralliance'] == 'EvE-Scout Enclave') {
				header("Location: /");
			}
		}
		//1b. ...and user is not logged in, so redirect
		else {
			login_redirect();
		}
	}
}

//set the return page and redirect to login
function login_redirect() {
	$_SESSION['auth_redirect'] = 'https://evescoutrescue.com'.htmlentities($_SERVER['PHP_SELF']);
	header("Location: ../auth/login.php");
	exit;
}
?>