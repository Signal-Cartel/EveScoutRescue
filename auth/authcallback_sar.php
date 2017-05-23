<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth

// secret_sar.php contains clientid and secret key from
// https://developers.eveonline.com/applications

require_once($_SERVER['DOCUMENT_ROOT'].'/config/secret_sar.php');

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

//login.php sends an authorization request to EVE's SSO server. The EVE server 
//then sends its response to this script. We need to handle that response.

// Make sure that the state matches the one set before the redirect.
if (isset($_SESSION['auth_state']) and isset($_GET['state']) and 
		$_SESSION['auth_state']==$_GET['state']) {
    
    //State matches, so we need to verify the authorization code we received from EVE SSO
    $url='https://login.eveonline.com/oauth/token';
    $header='Authorization: Basic '.base64_encode($clientid.':'.$secret);
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
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
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
	    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
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
				//We got a CharacterID, so set relevant session vars...
		    	$_SESSION['auth_characterid']=$response->CharacterID;
		    	$_SESSION['auth_charactername']=$response->CharacterName;
		    	$_SESSION['auth_characterhash']=$response->CharacterOwnerHash;
		    	//...and look up the character's alliance and corp details on the API
		        $ch = curl_init();
		        $lookup_url="https://api.eveonline.com/eve/CharacterAffiliation.xml.aspx?ids=".$response->CharacterID;
		        curl_setopt($ch, CURLOPT_URL, $lookup_url);
		        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
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
			        //We got a response, so check to make sure we can use what we got back
		        	$xml=simplexml_load_string($result);
			        if (!isset($xml->result->rowset->row->attributes()["characterID"])) {
			        	echo 'Error code: 5<br />';
			        	auth_error("No character details returned from API");
			        } 
			        else {
			        	//We got a result we can use, so set relevant session vars...
			        	$_SESSION['auth_charactercorp'] = (string)$xml->result->rowset->row->attributes()["corporationName"];
			            $_SESSION['auth_characteralliance'] = (string)$xml->result->rowset->row->attributes()["allianceName"];
			            //$corporationID = (string)$xml->result->rowset->row->attributes()["corporationID"];
			            //$allianceID = (string)$xml->result->rowset->row->attributes()["allianceID"];
			        }
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
}
?>