<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
require_once '../class/output.class.php';
require_once '../class/db.class.php';
require_once '../class/users.class.php';
require_once '../class/config.class.php';

// - Set arrays of different page types
// - If it's not in one of these arrays, it is a public page that does not require login to access
$pgsAdmin    = array('/esrc/payoutadmin.php');

//populate display strings for authenticated users
if (isset($_SESSION['auth_characterid'])) {
	$charimg    = '<img src="https://image.eveonline.com/Character/'.
				$_SESSION['auth_characterid'].'_64.jpg">';
	$charname   = $_SESSION['auth_charactername'];
	$chardiv    = '<div style="text-align: center;">'.$charimg.'<br />' .
				  '<div><span class="white">' .Output::htmlEncodeString($charname). '</span><br />' .
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
				$database = new Database();
			$users = new Users($database);
			//2. ...but user is not an admin, so redirect back to home
			if ($users->isAdmin($charname) === false) {
					header("Location: ".Config::ROOT_PATH);
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