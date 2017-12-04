<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth
// details on EVE SSO are available at 
// http://eveonline-third-party-documentation.readthedocs.io/en/latest/sso/

//direct users to this page when they click to login
// secret.php contains clientid and secret key from 
// https://developers.eveonline.com/applications

require_once '../../config/secret.php';	

session_start();

if (isset($_SESSION['auth_characterid'])) {
    echo "Logged in. ".$_SESSION['auth_characterid'];
    exit;
} 
else {
	if (!isset($redirect_uri))
	{
		echo 'Redirect URI not configured.';
		exit(1);
	}
	
 	$_SESSION['auth_redirect']=$appbase;
	$authsite='https://login.eveonline.com';
    $authurl='/oauth/authorize';
    $state=uniqid();
	$_SESSION['auth_state']=$state;
	
    session_write_close();
	header(
        'Location:'.$authsite.$authurl
        .'?response_type=code&redirect_uri='.urlencode($redirect_uri)
        .'&client_id='.$clientid.'&scope=&state='.$state
    );
	exit;
}
?>