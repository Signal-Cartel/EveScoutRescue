<?php
session_start();

// - Set arrays of different page types
// - If it's not in one of these arrays, it is a public page that does not require login to access
$pgsAdmin    = array('/esrc/payoutadmin.php');
$pgsAlliance = array('/esrc/data_entry.php','/esrc/search.php');

//populate array of admin users
$admins      = array('Thrice Hapus','Mynxee','Johnny Splunk');

//populate display strings for authenticated users
if (isset($_SESSION['auth_characterid'])) {
	$charimg    = '<img src="http://image.eveonline.com/Character/'.
				$_SESSION['auth_characterid'].'_64.jpg">';
	$charname   = $_SESSION['auth_charactername'];
	$chardiv    = '<div style="text-align: center;">'.$charimg.'<br />' .
				  '<div><span class="white">' .$charname. '</span><br />' .
				  '<span class="descr"><a href="../auth/logout.php">logout</a></span>' .
				  '</div></div>';
	//prepare footer with links for EvE-Scout pilots
	if ($_SESSION['auth_characteralliance'] == 'EvE-Scout Enclave') {
		$charfooter = '<footer class="footer">
				       <div class="container">
        		       <span class="text-muted">EvE-Scout: <a href="../esrc/search.php">ESRC Search</a>&nbsp;&nbsp;&nbsp;';
		//additional footer links for admin users
		if (in_array($charname, $admins)) {
			$charfooter = $charfooter. 'Admin: <a href="../esrc/payoutadmin.php">Payouts</a>';
		}
		$charfooter = $charfooter. '</span></div></footer>';
	}
	
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
	$_SESSION['auth_redirect'] = 'http://evescoutrescue.com'.htmlentities($_SERVER['PHP_SELF']);
	header("Location: ../auth/login.php");
	exit;
}
?>