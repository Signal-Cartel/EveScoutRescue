<?php

	/**
	 * Eve single sign on handling class
	 * @params 
	 * 	$credentials = Eve API aplication credentials from https://developers.eveonline.com/applications
	 *  $authkey = users authkey or refresh key
	 * @return array
	 *  $error
	 *  $refresh
	 *  $access
	 */

// https://dev.evescoutrescue.com/copilot/auth/php-session.php


class SSO {

	private $url = 'https://login.eveonline.com/v2/oauth/token';
	private $useragent = "Signal Cartel Co-Pilot";

	function auth_user($credentials, $authkey) {
	  
		$return_array = Array();
		
		$header = array( 
			'Authorization: Basic ' . $credentials,
			'Content-Type: application/x-www-form-urlencoded',
			'Host: login.eveonline.com'
		);

		$fields_string='';
		$fields=array('grant_type' => 'authorization_code', 'code' => $authkey);
		foreach ($fields as $key => $value) {
			$fields_string .= $key.'='.$value.'&';
		}
		rtrim($fields_string, '&');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$result = curl_exec($ch);

		if ($result === false) {
			$msg = curl_error($ch);
			curl_close($ch);
			$return_array['error'] = 'Curl error: ' . $msg;
			return $return_array;
		}
		else{
			curl_close($ch);
			$response = json_decode($result,true);		
			if (isset($response['refresh_token']) and isset($response['refresh_token'])){
				
				$return_array ['refresh_token'] = $response['refresh_token'];
				$return_array ['access_token'] = $response['access_token'];
				
				// new SSO uses JSON Web Tokens
				
				$jwt =  $response['access_token'];
				list($jwt_header, $jwt_payload, $jwt_signature) = explode (".", $jwt);
				$payload = json_decode(base64_decode($jwt_payload),true);
				/*
				{
				  "scp": [
					"esi-skills.read_skills.v1",
					"esi-skills.read_skillqueue.v1"
				  ],
				  "jti": "998e12c7-3241-43c5-8355-2c48822e0a1b",
				  "kid": "JWT-Signature-Key",
				  "sub": "CHARACTER:EVE:123123",
				  "azp": "my3rdpartyclientid",
				  "name": "Some Bloke",
				  "owner": "8PmzCeTKb4VFUDrHLc/AeZXDSWM=",
				  "exp": 1534412504,
				  "iss": "login.eveonline.com"
				}
				*/	
				$return_array['name'] = $payload['name'];
				$return_array['charid'] = explode(":",$payload['sub'])[2];
				$return_array['owner'] = $payload['owner'];
				
				return $return_array;
			}
			else{
				// error
				$return_array['error'] = 'result error: ' . $result;
				return $return_array;
			}
		}	
	}

	function refresh_user($credentials, $authkey) {
	  
		$return_array = Array();
		
		$header = array( 
			'Content-Type: application/x-www-form-urlencoded',
			'Host: login.eveonline.com',
			'Authorization: Basic ' . $credentials
		);

		$fields_string='';
		$fields=array(
			'grant_type' => 'refresh_token', 
			'refresh_token' => $authkey
		);
		$fields_string = http_build_query($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$result = curl_exec($ch);

		if ($result === false) {
			$msg = curl_error($ch);
			curl_close($ch);
			$return_array['error'] = 'Curl error: ' . $msg;
			return $return_array;
		}
		else{
			curl_close($ch);
			$response = json_decode($result,true);		
		if (isset($response['refresh_token']) and isset($response['refresh_token'])){
				
				$return_array ['refresh_token'] = $response['refresh_token'];
				$return_array ['access_token'] = $response['access_token'];
				
				// new SSO uses JSON Web Tokens
				
				$jwt =  $response['access_token'];
				list($jwt_header, $jwt_payload, $jwt_signature) = explode (".", $jwt);
				$payload = json_decode(base64_decode($jwt_payload),true);
				/*
				{
				  "scp": [
					"esi-skills.read_skills.v1",
					"esi-skills.read_skillqueue.v1"
				  ],
				  "jti": "998e12c7-3241-43c5-8355-2c48822e0a1b",
				  "kid": "JWT-Signature-Key",
				  "sub": "CHARACTER:EVE:123123",
				  "azp": "my3rdpartyclientid",
				  "name": "Some Bloke",
				  "owner": "8PmzCeTKb4VFUDrHLc/AeZXDSWM=",
				  "exp": 1534412504,
				  "iss": "login.eveonline.com"
				}
				*/	
				$return_array['name'] = $payload['name'];
				$return_array['charid'] = explode(":",$payload['sub'])[2];
				$return_array['owner'] = $payload['owner'];
				
				return $return_array;
			}
			else{
				// error
				$return_array['error'] = 'result error: ' . $result;
				return $return_array;
			}
		}	
	}

	function auth_911($credentials, $authkey) {
	  
		$return_array = Array();
		
		$header = array( 
			'Authorization: Basic ' . $credentials,
			'Content-Type: application/x-www-form-urlencoded',
			'Host: login.eveonline.com'
		);

		$fields_string='';
		$fields=array('grant_type' => 'authorization_code', 'code' => $authkey);
		foreach ($fields as $key => $value) {
			$fields_string .= $key.'='.$value.'&';
		}
		rtrim($fields_string, '&');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$result = curl_exec($ch);

		if ($result === false) {
			$msg = curl_error($ch);
			curl_close($ch);
			$return_array['error'] = $msg;
			return $return_array;
		}
		else{
			curl_close($ch);
			$response = json_decode($result,true);		
		if (isset($response['access_token']) and isset($response['refresh_token'])){
				
				$return_array ['refresh_token'] = $response['refresh_token'];
				$return_array ['access_token'] = $response['access_token'];
				
				// new oauth uses JSON Web Tokens - because CCP thinks we are running a bank now
				
				$jwt =  $response['access_token'];
				list($jwt_header, $jwt_payload, $jwt_signature) = explode (".", $jwt);
				$payload = json_decode(base64_decode($jwt_payload),true);
				/*
				{
				  "scp": [
					"esi-skills.read_skills.v1",
					"esi-skills.read_skillqueue.v1"
				  ],
				  "jti": "998e12c7-3241-43c5-8355-2c48822e0a1b",
				  "kid": "JWT-Signature-Key",
				  "sub": "CHARACTER:EVE:123123",
				  "azp": "my3rdpartyclientid",
				  "name": "Some Bloke",
				  "owner": "8PmzCeTKb4VFUDrHLc/AeZXDSWM=",
				  "exp": 1534412504,
				  "iss": "login.eveonline.com"
				}
				*/	
				$return_array['name'] = $payload['name'];
				$return_array['charid'] = explode(":",$payload['sub'])[2];
				$return_array['owner'] = $payload['owner'];
				
				return $return_array;
			}
			else{
				// error
				$return_array['error'] = 'result error: ' . $result;
				return $return_array;
			}
		}	
	}
	
}  

  

?>