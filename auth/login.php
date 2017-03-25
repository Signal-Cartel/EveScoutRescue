<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config/secret.php');
session_start();
if (isset($_SESSION['auth_characterid'])) {
    echo "Logged in. ".$_SESSION['auth_characterid'];
    exit;
} else {
    //Throw login redirect.
	$authsite='https://login.eveonline.com';
    $authurl='/oauth/authorize';
    $redirect_uri="http%3A%2F%2Fevescoutrescue.com%2Fauth%2Fauthcallback.php";
    $state=uniqid();
	$_SESSION['auth_state']=$state;
	if (!isset($_SESSION['auth_redirect'])) {
		$_SESSION['auth_redirect']='http://evescoutrescue.com/';
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