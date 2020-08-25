<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth
// details on EVE SSO are available at 
// http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/

// REQUIRED on all secured pages
define('ESRC', TRUE);

// autoload classes and set debug params
require_once '../class/autoload.php';
require_once '../resources/handler_debug-parameters.php';

session_start();

if (isset($_SESSION['auth_characterid'])) {
    echo "Logged in. ".$_SESSION['auth_characterid'];
    exit;
} 
else {
	// check if a session redirect PATH is already set
	if (!isset($_SESSION['auth_redirect'])) {
		// no, set a default redirect path
		$_SESSION['auth_redirect']= '/';
	}

	// if we are on localhost, fake login for testing
	if (strpos($_SERVER['HTTP_HOST'], 'localhost') > -1) {
		header('Location: authcallback.php');
		exit;
	}
	else {
		$authsite='https://login.eveonline.com';
		$authurl='/oauth/authorize';
		$state=uniqid();
		$_SESSION['auth_state']=$state;
		$_SESSION['charip']=getIp();
		session_write_close();
		header(
			'Location:'.$authsite.$authurl
			.'?response_type=code&redirect_uri='.urlencode(Config::AUTH_CALLBACK)
			.'&client_id='.Config::AUTH_CLIENT_ID.'&scope=&state='.$state
		);
		exit;
	}
}


function getIp() 
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {	// Test if it is a shared client
	  $ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {	// Is it a proxy address?
	  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
	  $ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}
?>