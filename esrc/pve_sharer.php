<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';

//include_once '../class/config.class.php';

//require_once '../class/password.class.php';
require_once '../class/db.class.php';
//require_once '../class/leaderboard.class.php';
//require_once '../class/caches.class.php';
require_once '../class/systems.class.php';
//require_once '../class/output.class.php';
require_once '../class/rescue.class.php';


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

?>
<html>

<head>
	<?php
	$pgtitle = 'Site Tracker';
	include_once '../includes/head.php';
	?>
</head>

<?php

$database = new Database();

if (!isset($charname))
{
	// no, set a dummy char name
	$charname = 'charname_not_set';
}

// create object instances
$users = new Users($database);
// check for SAR Coordinator login
$isCoord = ($users->isSARCoordinator($charname) || $users->isAdmin($charname));

$system = '';
$activeSAR = '';
if(isset($_REQUEST['sys'])) {
    $system = ucfirst(htmlspecialchars_decode($_REQUEST['sys']));
    // get active SAR requests of current system
    $data = $rescue->getSystemRequests($system, 0, $isCoord);
    // check for active SAR request
    if (count($data) > 0) {
        $activeSAR = ' <span style="font-weight: bold; color: red;">(!)</span>';
    }
}

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }


?>
<body class="white" style="background-color: black;">
<div class="container">

    <div class="row" id="header" style="padding-top: 20px;">
        <?php include_once '../includes/top-right.php'; ?>
        <?php include_once '../includes/top-left.php'; ?>
        <?php include_once 'top-middle.php'; ?>

    </div>
    <div class="ws"></div>

    <!-- NAVIGATION TABS -->
    <?php include_once 'navtabs.php'; ?>

    <div class="ws"></div>

<?php
// display error message if there is one
if (!empty($errmsg)) {
?>
	<div class="row" id="errormessage" style="background-color: #ff9999;" >
		<div class="col-md-12 message">
			<?php echo nl2br($errmsg); ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
}
?>

<div class="row" id="st-submit-row" >
		<div class="col-md-12">
			<h2>Site tracker is currently in early beta testing</h2>
			<p>Paste the contents of your scanner window below and submit</p>
            <textarea id="st-submit" rows="10" cols="90" style="display: block;"></textarea>
            <button class="btn btn-success" onclick="SubmitSigs()">Submit signatures</button>

		</div>
</div>
<div class="ws"></div>
<div class="row" id="st-result-row" >
		<div class="col-md-12">
			<p>The following result was obtained</p>
            <textarea readonly id="st-response" rows="4" cols="60" style="display: block;">Submit signatures to view response</textarea>

		</div>
</div>


<script type="text/javascript">
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}

var showlog = true;

function SubmitSigs(){
    document.getElementById("st-response").value = 'Uno momento...';
    var pasteContents = document.getElementById("st-submit").value;
    var data = pasteContents;
    var apiUrl = "pve_sharer_post.php";
    var apiParams = "data=" + data;
    var callback = stCallback;
    GetResource(apiUrl, apiParams, callback);
}


function GetResource(url, params, callback) {
	//url
	//params
	//callback - name of function
	//parsed - bool - true send parsed json array, or false send raw json

	xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange =
	function () {
		if (this.readyState == 4 && this.status == 200) {
			var response = xmlhttp.responseText;
			if (response.length > 0) {
				if (showlog){console.log("Success. Response:\n" + response);}
				callback(response);
				return;
			} else {
				console.log("I have no response.");
				callback(false);
				return;
			}
		}
		else if(this.readyState == 4 && this.status >= 400){
			// we have an error
			console.log("I received " + this.status + " errors.");
			callback(false);
			return ('status: ' + this.status);
		}
	}
	xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlhttp.onerror = function () {
		console.log("** An error occured in the network.");
		callback(false);
		return;
	};
	xmlhttp.send(params);
}

	function stCallback(response) {
		if (response != false) {
			document.getElementById("st-response").value = response;
            document.getElementById("st-submit").value = '';
		}
		else {
			document.getElementById("st-response").value = 'Huh, everything felt good when I sent it in. But there must have been an error.';
		}
	}
	


</script>

</body>
</html>
