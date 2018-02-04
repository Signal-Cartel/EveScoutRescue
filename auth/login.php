<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth
// details on EVE SSO are available at 
// http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/

//direct users to this page when they click to login
require_once '../class/config.class.php';
session_start();

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
	$authsite='https://login.eveonline.com';
    $authurl='/oauth/authorize';
    $state=uniqid();
	$_SESSION['auth_state']=$state;
	
    session_write_close();
	header(
        'Location:'.$authsite.$authurl
        .'?response_type=code&redirect_uri='.urlencode(Config::AUTH_CALLBACK)
        .'&client_id='.Config::AUTH_CLIENT_ID.'&scope=&state='.$state
    );
	exit;
}
?>