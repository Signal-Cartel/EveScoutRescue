<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth

// secret.php contains clientid and secret key from
// https://developers.eveonline.com/applications

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

//login.php sends an authorization request to EVE's SSO server. The EVE server 
//then sends its response to this script. We need to handle that response.

// Make sure that the state matches the one set before the redirect.
if (isset($_SESSION['auth_state']) and isset($_REQUEST['state']) and 
		$_SESSION['auth_state']==$_REQUEST['state']) {
    
    //State matches, so we need to verify the authorization code we received from EVE SSO
    $url='https://login.eveonline.com/oauth/token';
    $header='Authorization: Basic '.base64_encode(Config::AUTH_CLIENT_ID.':'.CONFIG::AUTH_SECRET);
    $code=$_GET['code'];
    //$state=$_GET['state'];
    $fields_string='';
    $fields=array('grant_type' => 'authorization_code', 'code' => $code);
    foreach ($fields as $key => $value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, Config::USER_AGENT);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);
    
    //Handle the response from our attempt to verify the EVE authorization code 
    if ($result===false) {
        echo 'Error code: 1<br />';
    	auth_error(curl_error($ch));
        curl_close($ch);
    }
    else {
    	//We got an access token, so use it to get the user's CharacterID from EVE SSO
    	curl_close($ch);
	    $response = json_decode($result);
	    $auth_token = $response->access_token;
	    $verify_url = 'https://login.eveonline.com/oauth/verify';
	    $ch = curl_init();
	    $header='Authorization: Bearer '.$auth_token;
	    curl_setopt($ch, CURLOPT_URL, $verify_url);
	    curl_setopt($ch, CURLOPT_USERAGENT, Config::USER_AGENT);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	    $result = curl_exec($ch);
	    
	    //Handle the response from our attempt to get CharacterID from EVE SSO
	    if ($result===false) {
	    	echo 'Error code: 2<br />';
	        auth_error(curl_error($ch));
	        curl_close($ch);
	    }
	    else {
		    curl_close($ch);
		    $response=json_decode($result);
		    if (!isset($response->CharacterID)) {
		    	echo 'Error code: 3<br />';
		        auth_error('No character ID returned');
		    }
		    else {
				// We got a CharacterID, so set relevant session vars...
		    	$_SESSION['auth_characterid']=$response->CharacterID;
		    	$_SESSION['auth_charactername']=$response->CharacterName;
		    	$_SESSION['auth_characterhash']=$response->CharacterOwnerHash;
		    	// ...and look up the character's alliance and corp details on the API
		        $ch = curl_init();
		        $lookup_url="https://esi.tech.ccp.is/latest/characters/".$_SESSION['auth_characterid']."/?datasource=tranquility";
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
						isset($response->alliance_id) ? 
							$_SESSION['auth_characteralliance'] = $response->alliance_id : 
							$_SESSION['auth_characteralliance'] = '';
		        	}
		        	
		        	// Check if user is in db, and add if needed
		        	// create objects 
		        	$database = new Database();
		        	// check for user in db
		        	$database->query("SELECT * FROM user WHERE user.characterid = :characterid
						    AND characterownerhash = :characterhash");
		        	$database->bind(":characterid", $_SESSION['auth_characterid']);
		        	$database->bind(":characterhash", $_SESSION['auth_characterhash']);
		        	// get the result line
		        	$userRecord = $database->single();
		        	// close the query
		        	$database->closeQuery();
		        	// if user is not in db, add them
		        	if (empty($userRecord)) {
		        		error_log('Creating user details...');
		        		$database->query('INSERT INTO user (characterid, characterownerhash,
							character_name,corporationid) VALUES (:characterid, :characterhash, 
							:charactername, :corporationid)');
		        		$database->bind(":characterid", $_SESSION['auth_characterid']);
		        		$database->bind(":characterhash", $_SESSION['auth_characterhash']);
		        		$database->bind(":charactername", $_SESSION['auth_charactername']);
		        		$database->bind(":corporationid", $_SESSION ['auth_charactercorp']);
		        		$database->execute();
		        		error_log("user added to db");
		        	}
		        	// if user is in db, check for new corp and update if needed
		        	else {
		        		if ($userRecord['corporationid'] != $_SESSION ['auth_charactercorp']) {
		        			error_log('User has new corporation...');
		        			$database->query('UPDATE user SET corporationid = :corporationid, 
								WHERE characterid = :characterid AND characterhash = :characterhash');
		        			$database->bind(":corporationid", $_SESSION ['auth_charactercorp']);
		        			$database->bind(":characterid", $_SESSION['auth_characterid']);
		        			$database->bind(":characterhash", $_SESSION['auth_characterhash']);
		        			$database->execute();
		        			error_log("Corporation updated for user.");
			        	}
		        	}
		        	// check for alliance in db; if not present, add it
		        	$database->query("SELECT allianceid, allianceticker FROM alliance 
						WHERE allianceid = :allianceid");
		        	$database->bind(":allianceid", $_SESSION['auth_characteralliance']);
		        	// get the result line
		        	$allianceRecord = $database->single();
		        	// close the query
		        	$database->closeQuery();
		        	// if alliance is not in db, add it
		        	if (empty($allianceRecord)) {
		        		error_log('Getting alliance details...');
		        		$ch = curl_init();
		        		$lookup_url="https://esi.tech.ccp.is/latest/alliances/".
		        			$_SESSION['auth_characteralliance']."/?datasource=tranquility";
		        		curl_setopt($ch, CURLOPT_URL, $lookup_url);
		        		curl_setopt($ch, CURLOPT_USERAGENT, Config::USER_AGENT);
		        		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		        		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		        		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		        		$result = curl_exec($ch);
		        		curl_close($ch);
		        		$alliance_data = json_decode($result);
		        		$allianceticker = $alliance_data->ticker;
		        		$alliancename = $alliance_data->name;
		        		
		        		error_log('Creating alliance details...');
		        		$database->query('INSERT INTO alliance (allianceid, alliancename, allianceticker) 
                    		VALUES (:allianceid, :alliancename, :allianceticker)');
		        		$database->bind(":allianceid", $_SESSION['auth_characteralliance']);
		        		$database->bind(":alliancename", $alliancename);
		        		$database->bind(":allianceticker", $allianceticker);
		        		$database->execute();
		        		error_log("alliance added to db");
		        	}
		        	
		        	// check for corp in db; if not present, add it
		        	$database->query("SELECT corporationid, corporationticker FROM corporation 
						WHERE corporationid = :corporationid");
		        	$database->bind(":corporationid", $_SESSION['auth_charactercorp']);
		        	// get the result line
		        	$corpRecord = $database->single();
		        	// close the query
		        	$database->closeQuery();
		        	// if corp is not in db, add it
		        	if (empty($corpRecord)) {
		        		error_log('Getting corporation details...');
		        		$ch = curl_init();
		        		$lookup_url="https://esi.tech.ccp.is/latest/corporations/".
				        	$_SESSION ['auth_charactercorp']."/?datasource=tranquility";
				        curl_setopt($ch, CURLOPT_URL, $lookup_url);
				        curl_setopt($ch, CURLOPT_USERAGENT, Config::USER_AGENT);
				        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
				        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				        $result = curl_exec($ch);
				        curl_close($ch);
				        $corp_data = json_decode($result);
				        $corpticker = $corp_data->ticker;
				        $corpname = $corp_data->name;
				        		
				        error_log('Creating corporation details...');
				        $database->query('INSERT INTO corporation
                			(corporationid, corporationname, corporationticker, allianceid)
                			VALUES (:corporationid, :corporationname, :corporationticker, :allianceid)');
				        $database->bind(":corporationid", $_SESSION['auth_charactercorp']);
				        $database->bind(":corporationname", $corpname);
				        $database->bind(":corporationticker", $corpticker);
				        $database->bind(":allianceid", $_SESSION['auth_characteralliance']);
				        $database->execute();
				        error_log("corporation added to db");
		        	}

		        	// Auth and DB updates complete; redirect user
		        	session_write_close();
				    header('Location:'. $_SESSION['auth_redirect']);
				    exit;
		        }
		    }
	    }
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