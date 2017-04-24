<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth
// details on EVE SSO are available at 
// http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/

//direct users to this page when they click to login
// secret.php contains clientid and secret key from 
// https://developers.eveonline.com/applications

require_once '../config/secret.php';	

session_start();

if (isset($_SESSION['auth_characterid'])) {
    echo "Logged in. ".$_SESSION['auth_characterid'];
    exit;
} 
else {
    //Throw login redirect to EVE auth server
	$authsite='https://login.eveonline.com';
    $authurl='/oauth/authorize';
    $redirect_uri="https%3A%2F%2Fevescoutrescue.com%2Fauth%2Fauthcallback.php";
    $state=uniqid();
	$_SESSION['auth_state']=$state;
	if (!isset($_SESSION['auth_redirect'])) {
		$_SESSION['auth_redirect']='https://evescoutrescue.com/';
	}	
    session_write_close();
	header(
        'Location:'.$authsite.$authurl
        .'?response_type=code&redirect_uri='.$redirect_uri
        .'&client_id='.$clientid.'&scope=&state='.$state
    );
    exit;
}
?>