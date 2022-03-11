<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth

// secret.php contains clientid and secret key from
// https://developers.eveonline.com/applications

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

require_once '../class/config.class.php';
require_once '../class/db.class.php';

/**
 * Log an error in case authentication fails
 * @param unknown $error_message
 */
function auth_error($error_message)
{
	print "There's been an error";
	error_log($error_message);
	exit();
}

session_start();

// for debug
/*
error_reporting(E_ALL);
ini_set('display_errors', 'on');
*/

// if we are on localhost, fake login for testing
if (strpos($_SERVER['HTTP_HOST'], 'localhost') > -1) {
	$_SESSION['auth_characterid'] = 96079190;
	$_SESSION['auth_charactername'] = 'Thrice Hapus';
	$_SESSION['auth_charactercorp'] = 98372649;
	$_SESSION['auth_characteralliance'] = 99005130;

	if (isset($_SESSION['auth_redirect'])) {
		header('Location: '. $_SESSION['auth_redirect']);
		exit;
	}
	else {
		header('Location: ../home/index.php');
		exit;
	}
}

//login.php sends an authorization request to EVE's SSO server. The EVE server 
//then sends its response to this script. We need to handle that response.

// Make sure that the state matches the one set before the redirect.
if (isset($_SESSION['auth_state']) and isset($_REQUEST['state']) and $_SESSION['auth_state']==$_REQUEST['state']) {
    
    //State matches, so we need to verify the authorization code we received from EVE SSO
	require_once '../class/sso.class.php';
	$credentials = base64_encode(Config::AUTH_CLIENT_ID.':'.CONFIG::AUTH_SECRET);
	$code=$_GET['code'];	

	$sso = new SSO();
	$response = $sso->auth_user($credentials,$code); // array response
	
	if (isset($response['error'])){
		// error and exit
		auth_error($response['error']);
	}

	$_SESSION['cp_refresh_token_v3'] = $response['refresh_token'];
	$_SESSION['cp_auth_token_v3'] = $authToken = $response['access_token'];	
		
	$_SESSION['auth_charactername'] = $name = $response['name'];
	$_SESSION['auth_characterid'] = $charId = $response['charid'];
	$_SESSION['auth_characterhash'] = $response['owner'];		


	// ...and look up the character's alliance and corp details on the API
	$ch = curl_init();
	$lookup_url="https://esi.evetech.net/latest/characters/".$_SESSION['auth_characterid']."/?datasource=tranquility";
	curl_setopt($ch, CURLOPT_URL, $lookup_url);
	curl_setopt($ch, CURLOPT_USERAGENT, Config::USER_AGENT);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	$result = curl_exec($ch);
	curl_close($ch);
	
	//Handle the response from our attempt to get character's alliance and corp from the API
	if ($result===false) {
		echo 'Error code: 4<br />';
		auth_error('No such character on the API');
	}
	else {
		// We got a response, so check to make sure we can use what we got back
		$response = json_decode($result);
		if (!isset($response->corporation_id)) {
			echo 'Error code: 5<br />';
			auth_error("No character details returned from API");
		}
		else {
			// We got a result we can use, so set relevant session vars...
			$_SESSION['auth_charactercorp'] = $response->corporation_id;
			$_SESSION['auth_characteralliance'] = (isset($response->alliance_id) ? $response->alliance_id : '');
		}
		
		// DB updates
		// data/auth_db.php?charid=123456&charhash=abcdef&charname=Joe%20Dokes&corpid=120002154454&allianceid=254678954621
		$ch = curl_init();
		// preserve any "+"s in character hash
		$encCharHash = str_replace("+", "%2B", $_SESSION['auth_characterhash']);
		// build URL
		$lookup_url= $_SERVER['HTTPS'] ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'] .
			'/data/auth_db.php?charid=' . $_SESSION['auth_characterid'] .
			'&charhash=' . $encCharHash .
			'&charname=' . urlencode($_SESSION['auth_charactername']) .
			'&corpid=' . $_SESSION['auth_charactercorp'] .
			'&allianceid=' . $_SESSION['auth_characteralliance'];
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//ignore the SSL error
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//follow redirect from Thrice's 303
		curl_setopt($ch, CURLOPT_URL, $lookup_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		//var_dump($result);
		//exit;
		curl_close($ch);
		if ($result == false) {
			//curl error
		}

		// Auth and DB updates complete; redirect user
		session_write_close();
		header('Location:'. $_SESSION['auth_redirect']);
		exit;
	}
		    
	    
    
} else {
    echo "State is wrong. Did you make sure to actually hit the login url first?";
    error_log($_SESSION['auth_state']);
    error_log($_GET['state']);
    // reset session for safety
    session_unset();
}

function debug($variable){
	if(is_array($variable)){
		echo "<pre>";
		print_r($variable);
		echo "</pre>";
		exit();
	}
	else{
		echo ($variable);
		exit();
	}
}
?>