<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

// http://dev.evescoutrescue.com/esrc/sitetrackerpost.php

date_default_timezone_set('UTC');
session_start();

if (!isset($_SESSION['auth_copilot'])) {
    exit();
}
/*
this is dev:
uri: https://mma.ultramega.info/rest/sitetracker_post_signatures.php
secret: 643e60c4cd9be13cf9b3073d2c4bf07fe08f60e77ffb256f33ca65be1c7a9e01

this is prod:
uri: https://signalcartel.space/mma/rest/sitetracker_post_signatures.php
secret: 004d84cab359167842b8659a21e6ae021d82175239100e6dbc345ee82a424722
*/
$server = $_SERVER['SERVER_NAME'];
if ($server == 'evescoutrescue.com'){
	// "We are on the production server";
	$environment = 'production_esi';
	$_SESSION['environment'] = "prod";
	$sitetracker_url = 'https://signalcartel.space/mma/rest/sitetracker_post_signatures.php';
	$sitetracker_secret = '004d84cab359167842b8659a21e6ae021d82175239100e6dbc345ee82a424722';
}
else{
	// "We are on the dev server";
	$environment = 'development_esi';
	$_SESSION['environment'] = "dev";
	$sitetracker_url = 'https://mma.ultramega.info/rest/sitetracker_post_signatures.php';
	$sitetracker_secret = '643e60c4cd9be13cf9b3073d2c4bf07fe08f60e77ffb256f33ca65be1c7a9e01';
}


/*
We need to submit
{
	"character_id": 96293852,
	"system_id": 30005221,
	"signatures": "EUZ-080    Cosmic Anomaly    Combat Site    Blood Den    100.0%    6.78 AU\nFWK-989    Cosmic Signature    Wormhole    Unstable Wormhole    100.0%    11.03 AU\nMTC-988    Cosmic Signature    Combat Site        27.3%    3.73 AU\nMTC-988    Cosmic Signature    Combat Site    Mul-Zatah Monastery    100.0%    16.25 AU\n"
}
*/

if (isset($_POST['data'])) {
    $data = $_POST['data'];
}



$data_array = array(
    'character_id' => $_SESSION['auth_characterid'],
    'system_id' => $_SESSION['auth_character_sysid'],
    'signatures' => $data
);

$json_data = json_encode($data_array);

//debug
    //echo $json_data;
    //exit();

$response = CurlRequest($sitetracker_url,$json_data);

if ($response){
    if (is_array($response)){
        echo ($response['status'] = 200 ? $response['message'] : $response['status']);
    }
    else{
        echo "Error of some kind or another. Did you try turning off and on again?";
    }

}
else{

        echo "Error of some kind or another. Did you try turning off and on again?";
    
}
exit();

function CurlRequest($url, $json_data){
	global $sitetracker_secret;
    // json_data is JSON string
    // https://mma.ultramega.info/rest/sitetracker_post_signatures.php
    // with header X-Mma-Discord: 643e60c4cd9be13cf9b3073d2c4bf07fe08f60e77ffb256f33ca65be1c7a9e01
    //build the following array to return:
    $returnArray = array();
    $ch = curl_init();
    $lookup_url = $url;
	$secret_header = 'X-Mma-Discord: ' . $sitetracker_secret;
    $headers = [
        $secret_header,
        'Content-Type: application/json',
        'Referer: sitetrackerpost.php',
        'User-Agent: Allison'
    ];
    curl_setopt($ch, CURLOPT_URL, $lookup_url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//ignore the SSL error
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//follow redirect from 303's
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);// to return result if success, false on fail

    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if ($result === false) {
        //$debug[] = "curl returned FALSE";
        return false;
    }
    if (!$info['http_code'] == 200) {
        //$debug[] = "http code returned <> 200" . $result;
        return false;
    }
    if ($info['size_download'] < 3) {
        //$debug[] = "curl returned empty: " . $result;
        return false;
    } else {
		try {
			$response = json_decode($result, true);//true to return array (instead of Object)
			// If exception Code following an exception is not executed.
			if (is_array($response)) {
				if (array_key_exists('error', $response)) {
					//$debug[] = "server returned response error: " . $response['error'];
					return false;
				}
				return $response;
			} else {
				//$debug[] = "unknown error in route check " . $result;
				return false;
			}

		} catch (Exception $e) {
			//$debug[] = "exception decoding JSON " . $e->getMessage();
			return false;
		}
    }
}



//echo '{"status":200,"message":"EUZ-080, FWK-989, MTC-988 have been added."}';
exit();
