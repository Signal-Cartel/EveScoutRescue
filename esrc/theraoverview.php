<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

require_once '../includes/auth-inc.php';
require_once '../class/users.class.php';
require_once '../class/config.class.php';

// check if the user is alliance member
if (!Users::isAllianceUserSession())
{

	// check if last login was already a non auth user
	if (isset($_SESSION['AUTH_NOALLIANCE']))
	{
		// set redirect to root path
		$_redirect_uri = Config::ROOT_PATH;	
	}
	else
	{
		// set redirect to requested path
		$_redirect_uri = $_SERVER['REQUEST_URI'];
	}
	
	// void the session entries on 'attack'
	session_unset();
	// save the redirect URL to current page
	$_SESSION['auth_redirect']=$_redirect_uri;
	// set a flag for alliance user failure
	$_SESSION['AUTH_NOALLIANCE'] = 1;
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH."auth/login.php");
	// stop processing
	exit;
}

// check if a valid character name is set
if (!isset($charname)) {
	// no, set a dummy char name
	$charname = 'charname_not_set';
}
?>
<html>

<head>
	<?php 
	$pgtitle = 'Thera Scan';
	include_once '../includes/head.php'; 
	?>
	
	<style>
		.sartable th, td {
		    padding: 4px;
		    vertical-align: text-top;
		}
		.request a {
			color: aqua;
		}
		.request a:link {
			color: aqua;
		}
		.request a:visited {
			color: aqua;
		}
		.request a:hover {
			color: aqua;
		}
	</style>
	
</head>

<?php
require_once '../class/db.class.php';
//require_once '../class/output.class.php';
require_once '../class/systems.class.php';
require_once '../class/rescue.class.php';

// create a new database connection
$database = new Database();
// create object instances
$users = new Users($database);
// check for SAR Coordinator login
$isCoord = ($users->isSARCoordinator($charname) || $users->isAdmin($charname));
//testing
	//$isCoord = 1;
	//$charname = 'Captain Crinkle';

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }

?>
<body class="white" style="background-color: black;">
	<div class="container">
	
	<div class="row" id="header" style="padding-top: 20px;">
		<?php 
		include_once '../includes/top-right.php'; 
		include_once '../includes/top-left.php';
		//include_once 'top-middle.php'; 

		?>
	</div>
	<div class="ws"></div>
	<!-- NAVIGATION TABS -->
	<?php include_once 'navtabs.php'; ?>
	<div class="ws"></div>

<?php
// display error message if there is one
if (!empty($errmsg)) {
?>
	<div class="row" id="errormessage" style="background-color: #ff9999;">
		<div class="col-md-6 message">
			<?php echo nl2br($errmsg); ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
}

date_default_timezone_set('UTC');
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

if (isset($_REQUEST['daysrangeLB'])) { 
	$daysrangeLB = intval(htmlspecialchars_decode($_REQUEST['daysrangeLB']));
	$daysrangeLB = $daysrangeLB < 1 ? 1 : $daysrangeLB;
	$daysrangeLB = $daysrangeLB > 28 ? 28 : $daysrangeLB;
}
$daysrangeLB = isset($daysrangeLB) ? $daysrangeLB : 10;

$t=time();
$t0 = $t + (60 * 60 * 24);
$t30 = $t - (60 * 60 * 24 * $daysrangeLB);

	$start = formdate($t30);
	$end = formdate($t);
	$endquery = formdate($t0);//exclusive end date for API

$sigs = querySignatures($start,$endquery);
$topscouts = ListPilots($sigs,$t30,$t);

if (isset($_REQUEST['numberLB'])) {
	$numberLB = intval(htmlspecialchars_decode($_REQUEST['numberLB']));
	$numberLB = $numberLB < 1 ? 1 : $numberLB;
	$numberLB = $numberLB > count($topscouts) ? count($topscouts) : $numberLB;
}

$numberLB= isset($numberLB) ? $numberLB: '5';

function ListPilots($sigs,$start,$end){
	$pilots = Array();
	$pi_ids = Array();
	$st = $start;
	$et = $end;
	foreach ($sigs as $sig){
		$sigtime = strtotime($sig['createdAt']);
		if (($sigtime >= $st) and ($sigtime <= $et)){
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
?>

<script type="text/javascript">
	// initialize tooltip display
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip({container: 'body'}); 
	});
</script>

 <div class="col-md-4"> 

<!-- LEADERBOARD -->
<?php 


?>

	<!-- LB type, date range and number selection form -->
	<span class="subhead">THERA SCOUT LEADERS</span><br />
	<p> From: <?php echo date('d M H:i',$t30); ?> To:  <?php echo date('d M H:i',$t); ?> </p>
	<form id="LBform" name="LBform" method="get" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">

		Top <input type="text" name="numberLB" size="1" autocomplete="off" class="black"
				value="<?=$numberLB?>"> over the last <input type="text" name="daysrangeLB" 
				size="1" autocomplete="off" class="black" value="<?=$daysrangeLB?>"> days
		<input type="submit" style="display: none;">
	</form>
	
	
	<table class="table" style="width: auto;">
		<thead>
			<tr>
				<th>Pilot</th>
				<th align="right">Signatures</th>
			</tr>
		</thead>
		<tbody>
			<?php
		
			// display the specified LB
			$pcount = 1;
			foreach ($topscouts as $scout) {
				// do not display rows with no agent name
				$exclude = Array("Renek Dallocort");
				
				if (!empty($scout['pilot']) and !in_array($scout['pilot'],$exclude)) {
					echo '<tr>';
					echo 	'<td>'. $scout['pilot'] .'</td>';
					echo 	'<td align="right">'. $scout['actions'] .'</td>';
					echo '</tr>';
					$pcount++;
					if ($pcount > $numberLB) {break;}
				}
			}
			?>
		</tbody>
	</table>
 </div> 
 <div class="col-md-6" style="opacity: .8; margin-top: 80px;">
 <p style="font-size: 1.5em; text-align: center;"><em>ALL OF TIME AND SPACE.
 <br/>EVERYWHERE AND ANYWHERE.
 <br/>EVERY STAR THAT EVER WAS.
 <br/>WHERE DO YOU WANT TO START?</em>
 </p>
 <p style="font-size: 1.5em; text-align: right;">- DOCTOR WHO</p>
 </p>
 </div>
</body>
</html>
