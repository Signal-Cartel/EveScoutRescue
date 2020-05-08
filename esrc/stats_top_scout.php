
<?php
date_default_timezone_set('UTC');

//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

// https://evescoutrescue.com/esrc/stats_top_scout.php
// https://evescoutrescue.com/esrc/stats_top_scout.php?l=true

// API is inclusive of start date, but exclusive of end date.
//Signatures API is here: https://www.eve-scout.com/api/reporting/signatures?startDate=2020-02-22&endDate=2020-02-23
//Donations API is here: https://www.eve-scout.com/api/reporting/donations?startDate=2020-04-29&endDate=2019-05-05
$fulllist = isset($_GET['l']) ? true : false;


$t=time();
$t0 = $t + (60 * 60 * 24);
$t1 = $t - (60 * 60 * 24);
$t2 = $t - (60 * 60 * 48);
$t30 = $t - (60 * 60 * 24 * 30);
$hour = date("H",$t);

// are we past CCP downtime today?
if (!$fulllist and $hour <= 10){
	$start = formdate($t2);
	$end = formdate($t1);
	$endquery = formdate($t);//exclusive end date for API
}
elseif(!$fulllist){
	$start = formdate($t1);
	$end = formdate($t);
	$endquery = formdate($t0);//exclusive end date for API
}
else{
	$start = formdate($t30);
	$end = formdate($t);
	$endquery = formdate($t0);//exclusive end date for API
}


$sigs = querySignatures($start,$endquery);
$topscouts = ListPilots($sigs,$start,$end);

if($fulllist){
	$output = $topscouts;	
}
else{
	$output = Array(
		'uid' => intval($topscouts[0]['uid']),
		'Pilot' => $topscouts[0]['pilot'],
		'Actions' => intval($topscouts[0]['actions'])
	);
}


echo(json_encode($output));

//{"uid":97117031,"Pilot":"Captain Crinkle","Actions":29}

exit();

function ListPilots($sigs,$start,$end){
	$pilots = Array();
	$pi_ids = Array();
	$st = strtotime($start . "T11:00:00.000Z");
	$et = strtotime($end . "T11:00:00.000Z");
	foreach ($sigs as $sig){
		$sigtime = strtotime($sig['createdAt']);
		if (($sigtime >= $st) and ($sigtime < $et)){
			$pilot = $sig['createdBy'];
			if (array_key_exists($pilot,$pilots)){
				$pilots[$pilot]++;
			}
			else{
				$pi_ids[$pilot] = $sig['createdById'];
				$pilots[$pilot] = 0;
				$pilots[$pilot]++;
			}			
		}
	}
	arsort($pilots);
	$returnarray = Array();
	foreach ($pilots as $pilot=>$count){
		$arr = Array(
			'pilot' => $pilot,
			'actions' => $count,
			'uid' => $pi_ids[$pilot]
		);
		$returnarray[] = $arr;
	}
	return $returnarray;
}

function formdate($t){
		
		$yr = date("Y",$t);
		$m = date("m",$t);
		$dy = date("d",$t);
		return "$yr-$m-$dy";
}

function querySignatures($start,$end){
	$ch = curl_init();
	$lookup_url = "https://www.eve-scout.com/api/reporting/signatures?startDate=$start&endDate=$end";
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//ignore SSL error
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//follow redirect
	curl_setopt($ch, CURLOPT_URL, $lookup_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	//debug($result);
	curl_close($ch);
	if ($result == false) {
		//curl error
		return Array();
	}
	// we have our response
	$response = DecodeResponse($result, $lookup_url);
	return $response;
}
	
exit();				
		
function DecodeResponse($response, $url){
	try {
		$return = json_decode($response, true);
		if ($return === null) {//we have an error - invalid json
	   
			if (strpos($response,'502 Bad Gateway')){
				echo "502 Bad Gateway";
			}
			else if(strpos($response,'500')){
				echo "500 Internal Error";		
			}
			else{
				echo "$url returned [$response]";
	
			}
			return Array();
		}
		return $return;
	} 
	catch (Exception $e) {
		echo 'Exception: ',  $e->getMessage(), "\n";			
		$message = "Error returned from $url";
		return Array();
	}
}
	
function debug($variable)
{
    if (is_array($variable)) {
        echo "<pre>";
        print_r($variable);
        echo "</pre>";
        exit();
    } else {
        echo($variable);
        exit();
    }
}

function output($variable)
{
    if (is_array($variable)) {
        echo "<pre>";
        print_r($variable);
        echo "</pre>";

    } else {
		echo "<pre>";
        echo($variable);
		echo "<pre>";
    }
	return;
}

/*
{
	"id": 51582,
	"signatureId": "HZH",
	"type": "wormhole",
	"status": "expired",
	"wormholeMass": "stable",
	"wormholeEol": "critical",
	"wormholeEstimatedEol": "2019-04-29T17:49:36.000Z",
	"wormholeDestinationSignatureId": "DUL",
	"createdAt": "2019-04-29T01:49:36.000Z",
	"updatedAt": "2019-04-29T13:49:46.000Z",
	"deletedAt": null,
	"statusUpdatedAt": "2019-04-29T13:32:23.000Z",
	"tripwireId": 21297201,
	"createdBy": "Mzsbi Haev",
	"createdById": "2113781815",
	"updatedBy": "Tripwire",
	"updatedById": "0",
	"deletedBy": null,
	"deletedById": null,
	"wormholeSourceWormholeTypeId": 142,
	"wormholeDestinationWormholeTypeId": 91,
	"solarSystemId": 31000005,
	"wormholeDestinationSolarSystemId": 30003602,
	"sourceWormholeType": {
		"id": 142,
		"name": "M164",
		"src": "lowsec",
		"dest": "thera",
		"lifetime": 16,
		"jumpMass": 300,
		"maxMass": 2000
	},
	"destinationWormholeType": {
		"id": 91,
		"name": "K162",
		"src": "anywhere",
		"dest": "anywhere",
		"lifetime": 0,
		"jumpMass": 0,
		"maxMass": 0
	},
	"sourceSolarSystem": {
		"id": 31000005,
		"name": "Thera",
		"constellationID": 21000324,
		"security": -0.99,
		"regionId": 11000031,
		"region": {
			"id": 11000031,
			"name": "G-R00031"
		}
	},
	"destinationSolarSystem": {
		"id": 30003602,
		"name": "Ratillose",
		"constellationID": 20000525,
		"security": 0.366468,
		"regionId": 10000044,
		"region": {
			"id": 10000044,
			"name": "Solitude"
		}
	}
}
*/
?>
