<?php
/**
 * Page(s) that include this page:
 * - should be included on all pages
 * 
 * Requirements:
 * - None
 * 
 * Notes:
 * - Direct access of included resources is prevented by an "ESRC" constant 
 *   defined on each relevant page
 * 
 * jmh - 20200716
 */
 // if no session, start one
 if (session_status() === PHP_SESSION_NONE) { session_start(); }
 
 // page cannot be accessed directly
 if (!defined('ESRC')) { die ('Direct access not permitted'); }

 // autoload classes and set debug params
 require_once '../class/autoload.php';
 require_once '../resources/handler_debug-parameters.php';

 // set default TZ
date_default_timezone_set('UTC');

// populate display strings for authenticated vs non-authed users
if (isset($_SESSION['auth_characterid'])) {
	$charimg    = '<img src="https://image.eveonline.com/Character/'.
				$_SESSION['auth_characterid'].'_64.jpg"  style="width: 40px;">';
	$charname   = $_SESSION['auth_charactername'];
	$logoutlink = (isset($_SESSION['auth_copilot'])) ? '' : '<span class="descr"><a href="../auth/logout.php">logout</a></span>';
	$chardiv    = '<div style="text-align: center;"><a href="../esrc/personal_stats.php?pilot=' . 
				  urlencode($charname) . '">'.$charimg.'<br />' . 
				  '<div><span class="white">' . Output::htmlEncodeString($charname) . 
				  '</span></a><br />' . $logoutlink . '</div></div>';
}
else {
	$chardiv  =	'<a class="login" href="../auth/login.php"></a>';			
}


// We are on an admin page...
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
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

//set the return page and redirect to login
function login_redirect() {
	$_SESSION['auth_redirect'] = 'https://evescoutrescue.com'.htmlentities($_SERVER['PHP_SELF']);
	header("Location: ../auth/login.php");
	exit;
}
?>