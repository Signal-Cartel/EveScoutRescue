<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth
// details on EVE SSO are available at 
// http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

//direct users to this page when they click to login
require_once '../class/config.class.php';
session_start();

function getIp() {

	// Get IP
	// Test if it is a shared client
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
	  $ip=$_SERVER['HTTP_CLIENT_IP'];
		//Is it a proxy address
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
	  $ip=$_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}


if (isset($_SESSION['auth_characterid'])) {
    echo "Logged in. ".$_SESSION['auth_characterid'];
    exit;
} 
else {
	// check if a session redirect PATH is already set
	if (!isset($_SESSION['auth_redirect'])) {
		// no, set a default redirect path
		$_SESSION['auth_redirect']=Config::ROOT_PATH;
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
?>