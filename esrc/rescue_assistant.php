<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
// https://dev.evescoutrescue.com/esrc/rescue_assistant.php?pilot=Boyami%20Lost&system=J135031
// https://dev.evescoutrescue.com/esrc/rescue_assistant-dev.php?pilot=Boyami%20Lost&system=J135031
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';
require_once '../class/password.class.php';
require_once '../class/db.class.php';
require_once '../class/caches.class.php';

require_once '../class/systems.class.php';
require_once '../class/output.class.php';
require_once '../class/rescue.class.php';

// NOTE HTML ENTITIES PILOT NAME AND EVERYTHING
$pilot_name = 'Boyami Lost';
$pilot_system = 'J135031';
$cache_pass = '6d8s56fgiadft';
$cache_status = 'Healthy';
$filament_status = 1;

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

		
$pilot_name = isset($_REQUEST['pilot']) ?  TestInput(htmlspecialchars_decode($_REQUEST['pilot'])) : '[PILOT NAME]';
$pilot_system = isset($_REQUEST['system']) ?  TestInput(ucfirst(htmlspecialchars_decode($_REQUEST['system']))) : '[SYSTEM]';


$cache_status = '[CACHE STATUS]';
$cache_pass = '[CACHE PASSWORD]';
$filament_status = '[UNKNOWN]';

if ($pilot_system != '[SYSTEM]'){
	$database = new Database();
	$caches = new Caches($database);
	$row = $caches->getCacheInfo($pilot_system);

	if (!empty($row)){
		$cache_status = $row ['Status'];
		$cache_pass = Output::htmlEncodeString($row['Password']);
		$filament_status = $row['has_fil'] ? "and contains a filament" : "and does not contain a filament";		
	}
	else{
		$cache_status = "No cache available";
		$filament_status = "";
	}
}




function TestInput($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
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
?>
<html>
<head>
	<?php
	$pgtitle = 'Rescue Assistant';
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
        <?php //include_once 'top-middle.php'; ?>

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


<style>
#ra > *{
	font-size: .9em;
}

#ra .ra-main{
	position: relative;
}

#ra .page{
	border: solid 1px rgba(145,145,145,.5);
    border-radius: 6px;
    padding: 0px 10px 10px 10px;
	min-height: 580px;
	background-color: rgba(0, 0, 0, 0.4);
}

#ra #page1{
	min-height: 380px;
}

#ra p, h1, h2, h3, h4, h5 {
	cursor: default;
}
#ra h1 {
	margin-top: 0px;
	font-size: 1.3em;
}

#ra h3 {
	font-size: 1.2em;
	margin-top: 8px;
    margin-bottom: 4px;
}

#ra h4 {
	font-size: 1.1em;
	text-align: center;
}

#ra h5 {
	font-size: 1.1em;
	text-align: center;
	margin: 0px 0px 0px 0px;
}

#ra .btn{
	margin-left: 6px;
	margin-right: 6px;
	display: inline;
	font-size: .9em;
} 

#ra  .btnmenu{
	width: 50%;
	margin: 0px 6px 1px 6px;
	padding: 0px;
	border-radius: 12px;
	font-style: italic;
	
}

#ra .step {
	margin: 0px;
	    margin-bottom: 4px;
}

#ra .note {
	text-align: center;
    font-style: italic;
    margin-left: 64px;
    margin-right: 64px;
    opacity: 0.7;
}

#ra .note-label{
	display: inline-block;
    width: 32%;
    text-align: right;
}

#ra .note-text{
	border-color: transparent;
    border-bottom-color: #646464;
    color: #d0d0d0;
	width: 64%;
	vertical-align: top;
	margin-bottom: 6px;
}
#ra .step-label{
	color: white;
	display: inline;
	text-align: left;

}

#ra .step-inst{
	color: white;
	margin: 0px;
}
#ra .step-text{
	color: white;
	text-align: right;
}

#ra .step-btn{
	text-align: center;
}

/* text area types */

#ra .copyable{
	transition: box-shadow .5s;
	vertical-align: top;
	margin-bottom: 8px;
}

#ra textarea {
	border: solid 1px rgba(145,145,145,.5);
	outline: none;
	overflow: hidden;
	resize: none;
}

#ra input, textarea:focus {
    outline-offset: 0px;
	outline: none;
}



#ra .info {

	border-color: rgba(255, 186, 186, 1);
    color: rgba(255, 186, 186, 1);
	margin-left: 8px;
}

#ra .convo {

	border-color: rgba(0, 216, 216, 1);
    color: rgba(0, 216, 216, 1);
}

#ra .disco {

	border-color: rgba(196, 128, 255, 1);
    color: rgba(196, 128, 255, 1);
}

#ra .gametext{
	color: orange;
	font-style: italic;
}

#ra .copyclip{
    z-index: 1;
    cursor: pointer;
    margin-left: 12px;
	margin-right: 12px;	
	vertical-align: top;
}

#ra .shown {
	display: block;
}

#ra .notshown {
	display: none;
}

#ra .pagebottom{
	display:block;
	margin-top: 12px;
}

#ra .backbutton{
	position: absolute;
	top: 3px;
	left: 6px;
}

#ra .backbutton-foot{
    position: absolute;
    bottom: 6px;
    left: 6px;
}

#ra .copybutton {
	position: relative;
    min-width: 26px;
    height: 13px;
    display: inline-block;
    background-repeat: no-repeat;
	background-position: right;
	margin-left: 2px;
	cursor: default;
}

#ra .copybutton:before {
    content: "\f0ea";
    font-family: FontAwesome;
    left:0px;
    position:absolute;
    top:0;
	text-rendering: auto;
    -webkit-font-smoothing: antialiased;	
 }
 
#ra .lang-picker {
	position: absolute;
    top: 0px;
    right: 0px;
}

#ra .flag {
		position: relative;
		top: 2px;
		min-width: 24px;
		height: 14px;
		display: inline-block;
		background-repeat: no-repeat;
		background-position: right;
		margin-left: 12px;
		margin-right: 6px;
		border: 1px solid #696969;
}

#ra .en {
		background-image: url(../img/flag-english.jpg);
}
	
#ra .fr {
		background-image: url(../img/flag-french.jpg);
}

#ra .ru {
		background-image: url(../img/flag-russian.jpg);
}


#ra .glow {

		animation: pulse-white .75s normal forwards;
	
		/*

		*/		
}

@keyframes pulse-white {
	0% {
		transform: scale(1);
		box-shadow: 0 0 0 0 rgba(255, 255, 255,1);
	}
	
	10% {
		transform: scale(0.95);
		box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
	}
	
	70% {
		transform: scale(1);
		box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
	}
	
	100% {
		transform: scale(1);
		box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
	}
}
	
</style>


<!-- https://dev.evescoutrescue.com/esrc/rescue_assistant.php -->
<div id="ra">
<div class="row ra-main">
	<div class="col-md-8">
	<h1>Search And Rescue Assistant - <?=$pilot_system?></h1>
	<div class="lang-picker">
		<span class="flag en"></span>
		<select id="lang-picker"  onchange="changeLang()">
		  <option style="background-image:url(../img/flag-english.jpg);" value="en">English</option>
		  <option style="background-image:url(../img/flag-french.jpg);" value="fr">French</option>
		</select>
	</div>
	</div>
</div>

<div class="row">

	<section data="PAGE 1 BEGIN">		
			<!-- PAGE ONE -->
			<div class="col-md-8 page shown" id="page1">

			<h4>INITIAL CONTACT - <?=$pilot_name?></h4>
			
				<div class="step">
					<div class="step-label">
						<p class="step-inst">Find the pilot in game</p>	
					</div>					
					<div class="step-text">
						<input type="text" class="copyable info" value="<?=$pilot_name?>">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div class="step">
					<div class="step-label"><p class="step-inst">And start a conversation</p></div>
					<div class="step-text">
						<input type="text" class="copyable convo" style="width: 300px;" 
						value="I am from EvE-Scout Rescue. o7" 
						data-fr="Je fais partie de EvE-Scout Rescue o7">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#noresponseModal">Pilot not responding</button>		
					</p>
				</div>			

				
				
				<div class="step">
					<div class="step-label">
<p class="step-inst">Make sure pilot is safe</p></div>
					<div class="step-text">
						<input type="text" class="copyable convo" style="width: 400px;" 
						value="First and most important, are you in a safe spot so we can talk?"
						data-fr="Avant toute chose, êtes vous dans un endroit sécurisée pour que nous puissions discuter?">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#unsureModal">Pilot not safe, or unsure</button>		
					</p>
				</div>	

				<div class="step">					
					<div class="step-label">	<p class="step-inst">Gather information</p></div>
					
					<div class="step-text">
						<input type="text" class="copyable convo" style="width: 320px;" 
						value="Good. What is your situation, and how can I help?"
						data-fr="Bien, quelle est votre situation, et comment puis-je vous aider?">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				<hr>
				
				<div class="step">
					<div class="step-label">
	<p class="step-inst">Cache status</p></div>
					<div class="step-text">
						<h4><?=$pilot_system?> : <?=$cache_status?> <?=$filament_status?></h4>
					</div>
				</div>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Can the cache be useful?</p>
					</div>
					<div class="step-text">
						<input type="text" class="copyable convo" style="width: 480px;" 
						value="Do you have a probe launcher fitted? or maybe a mobile depot in your cargo?"
						data-fr="Est-ce que vous avez un lanceur de probes sur votre vaisseau? Ou bien un dépôt mobile dans votre cargo?">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">(Optional) Maybe the pilot is with a fleet?</p>					</div>
					<div class="step-text">
						<input type="text" class="copyable convo" style="width: 390px;" value="Is anyone in your group flying a ship with a maintenance bay?"
						data-fr="Est-ce que quelqu’un dans votre groupe pilote un vaisseau avec un baie de maintenance?">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				

				<div class="pagebottom" id="ra_foot">
					<h4>How would you like to proceed?</h4>
					<h4>
					<button class="btn btn-success" onclick="ChangePage(2)">Rescue Cache</button>
					<button class="btn btn-danger" onclick="ChangePage(3)">Search & Rescue</button>
					</h4>
				</div>

		
			</div>
	</section>
			
	<section data="PAGE 2 RESCUE CACHE">		
			<!-- PAGE TWO AGENT -->	
			<div class="col-md-8 page notshown" id="page2" >

					<div class="backbutton">
					<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
					</div>

			<h4>RESCUE CACHE</h4>
			
				<div class="step">
					<div class="step-label">
							<p class="step-inst">In discord, ask for someone with admin access to add bookmark for the system:</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 420px;" value="@911 Operator Could you please add the bookmark for <?=$pilot_system ?>">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
					
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Will the pilot use:</p>					</div>
				</div>
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" onclick="CacheText('probes')"><i class="fa fa-bullseye" aria-hidden="true"></i> Probes</button>
						<button class="btn btn-default" onclick="CacheText('launcher')"><i class="fa fa-plus-square" aria-hidden="true"></i> Launcher & Probes</button>
						<button class="btn btn-info" onclick="CacheText('filament')"><i class="fa fa-random" aria-hidden="true"></i> Filament</button>					
					</p>
				</div>
				<div class="step">
					<div class="step-text">
						<textarea id='textcache' class="copyable convo" rows="2" cols="70"
						data-fr="Nous avons un container dans votre système avec des probes. Je vais vous envoyer par eve-mail le bookmark du container, ainsi que le mot de passe."
						>We have a cache in your system with some probes. I'm going to send you an eve-mail with the bookmark to the cache's location, and the password to the cache.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>			
				</div>


				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Add pilot to Bookmark Access List
						<span class="gametext">Neocom-&gt;Social-&gt;Access List</span>
						<input type="text" class="copyable info" value="<?=$pilot_name?>">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>			
					</p>					</div> 
				</div>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Then, send the ingame eve-mail to the pilot:</p>					</div>
				</div>
				
			<!-- Eve-Mail -->
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#mailModal">Eve-mail Cache Bookmark and Password</button>		
					</p>
				</div>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Pilot can’t find the cache? Check Overview settings.</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="4" cols="80"
						data-fr="Tell Parrot the French is missing here"
						>You may not be able to see the container because of your overview settings.
Right-click the menu ≡ on the upper-left corner of your overview→Open Overview Settings, look for “Tab Presets” than choose “Types” and in the search form, look for secure cargo container. It should be checked.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div id="supplyreturn">
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Ask to return cache supplies (if applicable)</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="2" cols="60"
						data-fr="Si vous en avez l’opportunité, penser-vous pouvoir ramener ce que vous avez pris dans la cache, quand vous n’en aurez plus besoin? Cela permettrait à un autre pilote dans votre situation de se servir du matériel"
						>If you have the chance, would you consider returning the equipment you used to the cache? It would help make the cache usable for another pilot.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>


				<div class="step">
					<div class="step-label">
							<p class="step-inst">If the pilot will return supplies:</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo" rows="2" cols="80"
						data-fr="Merci beaucoup! Dans ce cas, créez un Signet personnel pour la cache, il se peut que vous n’ayez plus accès au dossier partagé à votre retour."
						>Thank you very much! In that case, please bookmark the cache's location for yourself, as you may no longer have access to the shared bookmark folder when you come back.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	
				</div>
				
				<!--
				<div class="step">
					<div class="step-label">
							<p class="step-inst">If there is a static connection to HS/LS, you can inform the pilot</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo" rows="2" cols="60">There is a [get_security_level] static in your system. It seems like the easiest way out. The name of the wormhole is [get_static_name].</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</p>
				</div>
				-->
				
				<div class="pagebottom" id="ra_foot">
					<h4>Did the pilot successfully access the cache?</h4>
					<h4>Yes! <button class="btn btn-success" onclick="ChangePage(8)">Complete Agent report</button></h4>
					<h4>No? Is the pilot still alive?</h4>
					<h4>
					Yes <button class="btn btn-danger" onclick="ChangePage(3)">Begin New SAR?</button> No <button class="btn btn-default" onclick="ChangePage(6)">Post-Mortem SAR report</button>
					</h4>
				</div>

				<div class="backbutton-foot">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
				</div>				
			</div>	
	</section>		
			
	<section data="PAGE 3 SEARCH & RESCUE">
			<!-- PAGE THREE SAR AGENT -->	
			<div class="col-md-8 page notshown" id="page3" >

					<div class="backbutton">
					<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
					</div>

			<h4>SEARCH & RESCUE</h4>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">FIND A ROUTE:<br>In discord, ask Coordinators if we have a chain to pilot's system:</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 350px;" value="@ESR Team Do we have a chain for <?=$pilot_system?> ?">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Even if we have a chain, see if pilot has recent connection info:</p>					</div>
				</div>			
				<div class="step">
					<div class="step-text">
						<textarea class="copyable convo"  rows="2" cols="90"
						data-fr="Depuis combien de temps vous trouvez-vous dans ce système? Est-ce que vous savez si votre trou de ver à une chance d’être toujours là?"
						>For how long have you been in this system? Do you know by any chance if your entrance wormhole could to still be there?</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">If the pilot's entrance hole may still exist</p>					</div>
				</div>			
				<div class="step">
					<div class="step-text">
						<textarea class="copyable convo"  rows="2" cols="90"
						data-fr="Est-ce que vous vous souvenez du dernier système dans lequel vous vous trouviez avant d’entrer dans ce trou de ver? Vous devriez pouvoir trouver cette info dans le chat Local."
						>Do you remember the last known-space system you were in before entering this wormhole? (Check local chat)</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#doesntRememberModal">Pilot doesn't remember?</button>		
					</p>
				</div>	
			

				<div class="step">
					<div class="step-label">
							<p class="step-inst">If we have a possible route to the pilot's location, begin an immediate rescue:</p>					</div>
					<p class="step-btn"><button class="btn btn-danger" onclick="ChangePage(4)">Active SAR</button></p>
				</div>	

				<hr>
					
				<div class="step">
					<div class="step-label">
							<p class="step-inst">If not, inform the pilot:</p>					</div>
				</div>
				<div class="step">
					<div class="step-text">
						<textarea class="copyable convo" rows="2" cols="70"
						data-fr="Désolé, nous n’avons en ce moment aucune connection qui nous permette de vous atteindre."
						>Sorry, we do not have a connection to your system at the moment.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">And offer more options.</p>					</div>
				</div>

				<div class="pagebottom" id="ra_foot">
					<h4>
					<button class="btn btn-info" onclick="ChangePage(13)">More options</button>
					</h4>
				</div>
				<div class="backbutton-foot">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
				</div>				

				
			</div>	
	</section>

			
	<section data="PAGE 4 ACTIVE SAR">
		<div class="col-md-8 page notshown" id="page4">
			<div class="backbutton">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
		
			<h4>ACTIVE SAR</h4>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">FIRST: Open the new SAR so the Coordinator team knows there is an active rescue. 
					</p>					</div> 
				</div>	
				
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-danger" data-toggle="modal" data-target="#ModalSARNew">Open new SAR report</button>		
					</p>
				</div>

				<div class="step note">
					<p class="step-inst">NOTE: For your safety, (and because we are not WH "locators") do not share any route information with pilot that they did not provide directly. 
					</p> 
				</div>					
				

				<div class="step">
					<div class="step-label">
							<p class="step-inst">THEN: Inform the team that you are on your way to the pilot's location.</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 420px;" value="SAR pending. I'm on my way to a rescue in <?=$pilot_system?>">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">If needed, ask for backup in 911 channel:</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 420px;" value="@911 Operator Anyone available to help with a live rescue?">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">If organizing rescue with multiple pilots, create a shared bookmark folder:
								<ol>
								<li>Create ACL with all pilots involved</li>
								<li>Create a shared folder</li>
								<li>Assign the newly created ACL to Admin Access, Manage Access, Use Access and View Access</li>
								<li>Share the link to the folder in convo with your fellow rescuers</li>
								</ol>
							</p>					</div> 
				</div>			
				<h4>
				<input type="button" class="btn btn-default" value="Anoik.is" onclick="window.open('http://anoik.is/systems/<?=$pilot_system?>','_an');"></input>
				<input type="button" class="btn btn-default" value="Tripwire" onclick="window.open('https://tripwire.eve-apps.com','_tw');"></input>
				</h4>	
				<hr/>

				<div class="step">
					<div class="step-label">
					<p class="step-inst">FLY TO SYSTEM 				
					</p>					</div> 
				</div>				
				
				<div class="step">
					<div class="step-label">
						<p class="step-inst">If you are able to reach the pilot's system: 
						<button class="btn btn-danger" onclick="ChangePage(7)">Live rescue operation</button><!-- RESCUE OP -->					
					</p>					</div> 
				</div>
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">If not, inform the pilot:</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
				<textarea class="copyable convo" rows="1" cols="70">Sorry, the connection to your system is no longer there. We couldn't reach you.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	

				<div class="step">
					<div class="step-label">
						<p class="step-inst">And offer more options:  
						<button class="btn btn-default" onclick="ChangePage(13)">More options</button>				
					</p>
					</div> 
				</div>	
				
				
			<div class="pagebottom" id="ra_foot">
			</div>	
			<div class="backbutton-foot">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>				
		</div>	
	</section>


	<section data="PAGE 5 WAIT FOR RESCUE">
		<div class="col-md-8 page notshown" id="page5" >
			<div class="backbutton">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
		
			<h4>WAIT FOR RESCUE</h4>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Information gathering</p>					</div> 
				</div>	
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="4" cols="80"
						data-fr="Je vais remplir un rapport qui va alerter tous les pilotes de Signal Cartel, et il vous rechercherons. Pour ce rapport je vais avoir besoin de quelques informations. Ne me donnez aucune information que vous n’avez pas envie de partager. Premièrement, nous avons besoin de savoir quel est le meilleur moyen de vous contacter quand nous aurons trouvé votre système. Est-ce que vous avez un alt? Un contact Discord?"
						>I am going to file a report which will alert all pilots in Signal Cartel to look for you. For that report I am going to need some information. You do not have to give me any information you are not comfortable providing. First, we need to know the best way to contact you once we locate your wormhole. Do you have an alt we can contact with Eve mail? Do you have a Discord contact?</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Discord instructions</p>					</div> 
				</div>	
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="4" cols="80"
						data-fr="EvE-Scout maintient un salon Discord («Stranded pilot lounge») pour les pilotes perdus, à cette adresse: https://discord.gg/Xy6XEU et vous y êtes bienvenue. Si votre situation devait changer, merci de nous le faire savoir à cet endroit ou par eve-mail. Attention, ce salon est publique, ne révélez aucune information sur votre situation ou sur votre position. Dites juste bonjour et un Operateur EvE-Scout s’occupera de vous."
						>EvE-Scout Rescue maintains a Discord channel (the stranded pilot's lounge) for our stranded pilots, located here: https://discord.gg/Xy6XEU, and you are welcome to join. Should your situation change, you can contact us by posting in lounge chat. Be aware that it is a public channel so do not disclose any information in that channel that would jeopardize your safety.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Hull size (optional)</p>					</div> 
				</div>	
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="4" cols="80"
						data-fr="Nous n’avons pas besoin de savoir quel vaisseau vous pilotez, mais si vous avez besoin d’une certaine taille de trou de ver et que nous sommes au courant, nous pourrons mieux planifier votre extraction. Pour votre sécurité, nous scannerons les trou de ver et trouverons une route sûre avant que vous ne vous connectiez pour être secouru. Connaitre la taille de votre vaissaux nous aidera à faire les bons choix."
						>We do not need to know the type of ship you are flying but if you need a special size wormhole, knowing that information would help us map your escape. For your safety, we map your route to known space before we even ask you to log back in for the rescue operation. Knowing your ship size will ensure the escape route we map will work for you.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Have you asked time zone? Language?</p>					</div> 
				</div>	
				
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-danger" data-toggle="modal" data-target="#ModalSARNew">Open new SAR report</button>		
						OR 
						<button class="btn btn-danger" data-toggle="modal" data-target="#ModalSAREdit">Update your existing SAR</button>
					</p>
				</div>
				

			
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Fly safe!</p>					</div> 
				</div>	
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="3" cols="80"
						data-fr="Bien, j’ai rempli le rapport. Nous sommes maintenant à votre recherche. Nous vous contacterons dès que nous trouverons votre système. Si nous ne vous avons pas trouvé dans les 7 prochains jours, nous vous contacterons pour vérifier que vous voulez toujours que l’on vous cherche. Avez-vous des questions?"
						>Ok, I have filed the SAR report. We are now looking for you. We will contact you as soon as we find your system. If we don’t find your system within 7 days, we will contact you to see if you want us to continue looking. Do you have any questions?</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>				


				<div class="step">
					<div class="step-label">
							<p class="step-inst">Optional Eve-mail to pilot with info about Signal Cartel</p>					</div>
				</div>
				
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#mailModalnewsar">Eve-mail wrap up</button>		
					</p>
				</div>

				
				<div class="backbutton-foot">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
				</div>				

		</div>	
	</section>

	<section data="PAGE 6 POST MORTEM SAR">
		<div class="col-md-8 page notshown" id="page6" >
			<div class="backbutton">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
		
			<h4>POST MORTEM SAR</h4>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">FIRST:
					</p>					</div> 
				</div>	

				<div class="step">
					<p class="step-btn">
						<button class="btn btn-danger" data-toggle="modal" data-target="#ModalSARNew">Open new SAR report</button>		
					</p>
				</div>
				
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">THEN:
					</p>					</div> 
				</div>		
		
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Inform the team about the outcome.</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-label">
							<p class="step-inst">If pilot self-destructed:</p>					</div> 
				</div>	
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 480px;" value="SAR <?=$pilot_system?> can be closed. Pilot elected to SD">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">If pilot was killed:</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 480px;" value="SAR <?=$pilot_system?> can be closed. Pilot was killed.">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Optional Eve-mail to pilot with info about Signal Cartel</p>					</div>
				</div>
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#mailModalfailure">Eve-mail wrap up</button>		
					</p>
				</div>				
			<hr>

			<div class="pagebottom" id="ra_foot">

			</div>	
				<div class="backbutton-foot">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
				</div>				

		</div>	
	</section>	

	<section data="PAGE 7 RESCUE OP">
		<div class="col-md-8 page notshown" id="page7" >
			<div class="backbutton">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
		
			<h4>RESCUE OP (draft ideas)</h4>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">IN SYSTEM OPERATIONS:
					</p>					</div> 
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Once you arrive in system, PRESS RECHECK on Allison to be credited for rescue.
					</p>					</div> 
				</div>	
				
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Copyable text for in-system operations
					</p>					</div> 
				</div>			
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="2" cols="60">I will make a bookmark for the exit and share it with you.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">More ideas</p>					</div> 
				</div>	
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo"  rows="2" cols="60">I am at the exit wormhole. I will invite you to a fleet. Accept the invitation, then warp to me.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>



				
			<hr>
				<div class="step">
					<div class="step-label">
							<p class="step-inst">RESCUE SUCCESS:
					</p>					</div> 
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">911 Channel: Let the team know!</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 480px;" value="SAR <?=$pilot_system?> can be closed. Pilot has been rescued!">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Optional Eve-mail to pilot with info about Signal Cartel</p>					</div>
				</div>
				
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#mailModalinfo">Eve-mail wrap up</button>		
					</p>
				</div>				

			<hr>
				<div class="step">
					<div class="step-label">
							<p class="step-inst">RESCUE FAILURE:
					</p>					</div> 
				</div>	

				<div class="step">
					<div class="step-label">
							<p class="step-inst">911 Channel: If pilot was killed:</p>					</div> 
				</div>			
				<div class="step">
					<div class="step-text">
						<input type="text" class="copyable disco" style="width: 480px;" value="SAR <?=$pilot_system?> can be closed. Pilot was killed.">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>					

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Optional Eve-mail to pilot with info about Signal Cartel</p>					</div>
				</div>
				
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#mailModalfailure">Eve-mail wrap up</button>		
					</p>
				</div>
				
			<div class="pagebottom" id="ra_foot">



			</div>	
				<div class="backbutton-foot">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
				</div>				

		</div>	
	</section>	

	<section data="PAGE 8 AGENT">
		<!-- PAGE COMMENT -->	
		<div class="col-md-8 page notshown" id="page8" >
			<div class="backbutton">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
		
			<h4>RESCUE AGENT</h4>


				<div class="step">
					<div class="step-label">
							<p class="step-inst">Thank the pilot</p>					</div>
				</div>
				<div class="step">
					<div class="step-text">
						<textarea class="copyable convo"  rows="1" cols="80"
						data-fr="ous de nous avoir donné l’opportunité de vous aider aujourd’hui. N’hésitez jamais à nous contacter chaque fois que vous avez besoin o/"
						>Thank you for letting us help you today. Do not hesitate to reach out to us anytime you need. o/</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Remove pilot from Bookmark Access List 
						<span class="gametext">Neocom-&gt;Social-&gt;Access List</span>					
						<input type="text" class="copyable info" value="<?=$pilot_name?>">
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>			
					</p>					</div> 
				</div>		


				<div class="step">
					<div class="step-label">
							<p class="step-inst">911 Channel: Ask to have bookmark removed from the folder.</p>					</div> 
				</div>	
				<div class="step">
					<div class="step-text">
						<textarea class="copyable disco"  rows="2" cols="60">Agent filed, pilot removed from the access list. Can someone remove the bookmark for <?=$pilot_system?> please?</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>	
				
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Optional Eve-mail to pilot with info about Signal Cartel</p>					</div>
				</div>
				
				<div class="step">
					<p class="step-btn">
						<button class="btn btn-default" data-toggle="modal" data-target="#mailModalinfo">Eve-mail wrap up</button>		
					</p>
				</div>

							
				
			<hr>

			


			<div class="pagebottom" id="ra_foot">
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Final Step</p>					</div>
				</div>

				<div class="step">
					<p class="step-btn">
						<button class="btn btn-success" data-toggle="modal" data-target="#AgentModal">Record agent action</button>		
					</p>
				</div>	

			</div>	
				<div class="backbutton-foot">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
				</div>				

		</div>	
	</section>

	<section data="PAGE 12 COMPLETE">
		<!-- PAGE COMMENT -->	
		<div class="col-md-8 page notshown" id="page12" >
			<div class="backbutton">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
			<h4>COMPLETE</h4>

			<hr>

			<div class="pagebottom" id="ra_foot">
				<h4>
				<button class="btn btn-default" onclick="ChangePage(1)">Start over</button>
				</h4>
			</div>	
		</div>	
	</section>

	<section data="PAGE 13 MORE OPTIONS">
		<!-- PAGE COMMENT -->	
		<div class="col-md-8 page notshown" id="page13" >
			<div class="backbutton">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
			<h4>MORE OPTIONS</h4>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Offer more options:
					</p>					</div> 
				</div>	
				
				<div class="step">
					<div class="step-text">
<textarea class="copyable convo"  rows="14" cols="90"
data-fr="Nous avons toutefois quelques options pour vous sortir de là:
1. Nous avons un Noise-5 Needlejack filament dans notre container de secours. En l’utilisant, vous serez transporté dans un système aléatoire en Nullsec. Il se peut que cette option aggrave votre situation.
2. Vous pouvez appeler à l’aide dans le chat local, et demander s’il y a une sortie. Si vous optenez une réponse, nous serons en mesure de venir vous chercher. S’il y a une structure dans vote système, vous pouvez aussi chercher à qui elle appartient et tenter de contacter le CEO de cette corporation. Encore une fois, il se peut que ces options aggravent votre situation.
3. Vous pouvez vous auto-détruire. Cette option aggravera définitivement votre situation ;-)
4. Nous pouvons nous lancer à votre recherche. En moyenne, cela nous prends 6 jours pour trouver des pilotes perdus?
Considérez la valeur de votre vaisseau ou de vos implants avant de prendre votre décision.
"
>
We still have more options to get you out: 
1. We have a Noise-5 Needlejack filament in our Rescue Cache. By using it, you will be transported to a random nullsec system. Be aware that this may result in you being killed. 
2. You can request help in local chat, ask for the known-space static connection and if you get a response, tell us which system it is (we will come and get you), or, if you see a structure in the wormhole, you could look up which corporation owns it and contact the CEO for help. All of these options may, again, result in you being killed.
3. You can self-destruct. This will definitely result in you being killed ;-)
4. We will search for you. It takes us on average 6 days to find a lost pilot.

Please consider the value of your ship/implants and the time it may take for us to find you in making your decision.
</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>			
				</div>

				<div class="pagebottom" id="ra_foot">
					<div class="step">
						<div class="step-label">
							<p class="step-inst">Pilot's choice?</p>					</div> 
					</div>	
					<h4>
					<button class="btn btn-danger" onclick="ChangePage(5)">Wait for rescue - SAR</button>
					<button class="btn btn-info" onclick="ChangePage(2)">Filament</button>
					<button class="btn btn-default" onclick="ChangePage(6)">Self Destruct</button>
					</h4>

					<div class="step">
						<div class="step-label">
							<p class="step-inst">Or, maybe he received connection information from locals?</p>					</div> 
					</div>	
					<h4>
						<button class="btn btn-success" onclick="ChangePage(4)">Return to active SAR</button>
					</h4>						
				</div>
				<div class="backbutton-foot">
				<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
				</div>				

		</div>	
	</section>
	
	<section data="PAGE 10 EMPTY">
		<!-- PAGE COMMENT -->	
		<div class="col-md-8 page notshown" id="page10" >
			<div class="backbutton">
			<button class="btn btn-warning" onclick="BackPage()">&#9668; BACK</button>
			</div>
		
		
			<h4>UNDER CONSTRUCTION</h4>

		
		
			<hr>

			


			<div class="pagebottom" id="ra_foot">
				<h4>PILOT'S CHOICE?</h4>
				<h4>
				<button class="btn btn-default" onclick="ChangePage(5)">Wait and see</button>
				<button class="btn btn-danger" onclick="ChangePage(4)">Immediate rescue</button>
				<button class="btn btn-danger" onclick="ChangePage(5)">SAR</button>
				<button class="btn btn-info" onclick="ChangePage(2)">Filament</button>
				<button class="btn btn-default" onclick="ChangePage(6)">Self Destruct</button>
				</h4>
			</div>	
		</div>	
	</section>
			
<section data="NOTES">
	<div class="col-md-4" id="notes">

		<div class="step">
			<p class="step-btn">
				<button class="btn btn-default" data-toggle="modal" data-target="#faqModal">F.A.Q.s</button>
				<button class="btn btn-default" data-toggle="modal" data-target="#utilityModal">Utilities</button>				
			</p>
		</div>	
					
		<hr/>
		<div>
			<h3>Your Notes</h3>
		<div class="note-label">HULL</div>	
		<input data-lab="Hull" class="note-text" type="text">
		<div class="note-label">ALTS</div>	
		<input data-lab="Alts" class="note-text" type="text">
		<div class="note-label">DISCORD NAME</div>	
		<input data-lab="Discord name" class="note-text" type="text">
		<div class="note-label">TIME ZONE</div>	
		<input data-lab="Time zone" class="note-text" type="text">
		<div class="note-label">LANGUAGE</div>	
		<input data-lab="Language" class="note-text" type="text">
		<div class="note-label">REASON</div>	
		<input data-lab="Reason" class="note-text" type="text">
		<div class="note-label">OTHER INFO</div>		
		<textarea id="other-text" rows="4" class="note-text" style=""></textarea>
		</div>
		<hr style="margin: 10px 0px 0px 0px;">
		<h4>JUMP TO</h4>
			<div class="step">
				<h4>
					<button class="btn btn-danger" data-toggle="modal" data-target="#ModalSARNew">SAR Form</button>
					<button class="btn btn-success" data-toggle="modal" data-target="#AgentModal">AGENT Form</button>	
				</h4>
			</div>	
		<h5><button class="btn btn-default btnmenu" onclick="ChangePage(1)">Initial Contact</button></h5>
		<h5><button class="btn btn-success btnmenu" onclick="ChangePage(2)">Rescue Cache</button></h5>
		<h5><button class="btn btn-success btnmenu" onclick="ChangePage(8)">Agent</button></h5>		
		<h5><button class="btn btn-danger btnmenu" onclick="ChangePage(3)">Search & Rescue</button></h5>
		<h5><button class="btn btn-danger btnmenu" onclick="ChangePage(4)">Active SAR</button></h5>
		<!--<h5><button class="btn btn-info btnmenu" onclick="ChangePage(13)">More options</button></h5>-->		
		<h5><button class="btn btn-danger btnmenu" onclick="ChangePage(5)">Wait for Rescue SAR</button></h5>
		<h5><button class="btn btn-default btnmenu" onclick="ChangePage(6)">Post Mortem SAR</button></h5>


	</div>
</section>	

</div>

<!-- BEGIN MODALs ------------------------------------------------>

<div id="ModalSARNew" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
	  <div class="modal-header sar">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title sechead">Search &amp; Rescue</h4>
	  </div>
	  <form name="sarnewform" id="sarnewform" action="ra_rescueaction.php" method="POST">
		  <div class="modal-body black">
			<div class="form-group">
				<label class="control-label" for="system">System: </label>
				<input type="hidden" name="system" value="<?=$pilot_system?>" />
				<span class="sechead"><?=$pilot_system?></span>
			</div>
			<div class="field">
				<label class="control-label" for="pilot">Stranded Pilot
					<span class="descr">Other contact names, if any, should be entered in Notes.</span>
				</label>
				
				<input type="text" class="form-control " id="pilot" name="pilot" value="<?=$pilot_name?>"/>
			</div>
			
			<label class="checkbox-inline">
				<input id="canrefit" name="canrefit" type="checkbox" value="1">
				<strong>Can Refit</strong>
			</label>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<label class="checkbox-inline">
				<input id="launcher" name="launcher" type="checkbox" value="1">
				<strong>Has Probe Launcher</strong>
			</label>
			<div class="ws"></div>

			<div class="field">
				<label class="control-label" for="sarnotes">Notes<span class="descr">Is there any other important information we need to know?</span></label>
				<textarea class="form-control" id="sarnotes" name="notes" rows="12"></textarea>
			</div>
		  </div>
		  <div class="modal-footer">
			<div class="form-actions">
				<input type="hidden" name="action" value="Create">
				<button type="submit" class="btn btn-info">Submit</button>
				<script>
				$('#sarnewform').submit(function() {
					$('#ModalSARNew').modal('hide');
				});
				</script>
			</div>
		  </div>
	  </form>
	</div>

  </div>
</div>

<div id="ModalSAREdit" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sar">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title sechead">Search &amp; Rescue</h4>
      </div>

		<form name="sareditform" id="sareditform" action="ra_rescueaction.php" method="POST">
			<div class="modal-body black">
				<table class="black sartable">
				<tr>
					<td align="right">System: </td>
					<td><strong><?=$pilot_system?></strong></td>
				</tr>
				<tr>
					<td align="right">Pilot: </td>
					<td><strong><?=$pilot_name?></strong></td>
				</tr>
				</table>
			<hr>
				<div class="field">
					<label class="control-label" for="sareditnotes">Enter a new note</label>
					<textarea class="form-control" id="sareditnotes" name="notes" rows="12"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<div class="form-actions">
					<input type="hidden" name="pilot" value="<?=$pilot_name?>">
					<input type="hidden" name="system" value="<?=$pilot_system?>" />
					<input type="hidden" name="action" value="UpdateRequest">
					<button type="submit" class="btn btn-info">Submit</button>
				<script>
				$('#sareditform').submit(function() {
					$('#ModalSAREdit').modal('hide');
				});
				</script>
				
				</div>
			</div>
		</form>
    </div>

  </div>
</div>

<div id="mailModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Eve-mail <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">
	  
	  
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Subject</p>
					</div>
					<div class="step-text">
					<textarea class="copyable convo" rows="1" cols="64"
					data-fr="EvE-Scout Rescue - Information sur le container"
					>EvE-Scout Rescue - Cache Information</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>				
			</div>
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Body</p>
					</div>
					<div class="step-text">
<textarea class="copyable convo" rows="20" cols="64"
data-fr="Cher <?= $pilot_name?>,   &#013;&#010;&#010;Voici le lien pour le dossier partagé de Signet:&#013;&#010;&lt;a href=&quot;bookmarkFolder:6026349&quot;&gt;ESR 911 Stranded Pilots&lt;/a&gt;  &#013;&#010;&#010;Mettez ce dossier en ligne et n’oubliez pas d’entrer «L» pour ouvrir la fenêtre des signets sur votre écran. De notre côté, nous allons vous ajouter à la liste d’accès du dossier, et mettre le signet à votre disposition. Cela peut prendre quelques minutes. Si vous ne pouvez acceder au dossier ou si vous ne voyez pas le signet, attendez juste quelques instants et essayez à nouveau. &#013;&#010;Le mot de passe du container est le suivant:&#013;&#010;<?= $cache_pass?>&#013;&#010;Copiez/Collez le mot de passe depuis cet email.&#013;&#010;&#010;Prenez ce dont vous avez besoin."
>Dear <?= $pilot_name?>,

Here is the link to the shared bookmark folder:
&lt;a href="bookmarkFolder:6026349"&gt;ESR 911 Stranded Pilots&lt;/a&gt;

Put that folder online and don't forget to type "L" to see your saved locations. It might take a few minutes to add you to the folder's access list, and to put the bookmark up. If you can't access the folder or if you don't see the bookmark, just wait a little and try again.

The password to open the Rescue Cache is:
<?= $cache_pass?>&#013;&#010;
Please copy/paste the password from this email.

Take whatever you need.
</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>				
			</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="mailModalinfo" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Eve-mail <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">
	  
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Subject</p>
					</div>
					<div class="step-text">
					<textarea class="copyable convo" rows="1" cols="64"
					data-fr="">EvE-Scout Rescue - Wrap Up</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>				
			</div>
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Body</p>
					</div>
					<div class="step-text">
<textarea class="copyable convo" rows="24" cols="64">
Dear <?= $pilot_name?>,

We hope you found your way home safely. We are very happy to know that we have been of some help. If you wish to learn more about the EvE-Scout Rescue program, come visit <a href="https://evescoutrescue.com"> https://evescoutrescue.com </a> As you can see, some rescued pilots have submitted testimonials about their rescue. Feel free to add your own!

We also have an in-game public channel: <a href="joinChannel:player_-65344012">EvE-Scout</a>, and a Discord server: <a href="https://discord.gg/6wNp5w">https://discord.gg/6wNp5w</a> Come and say hello if you have the chance.

Another activity of <a href="showinfo:2//98372649">Signal Cartel</a> and the <a href="showinfo:16159//99005130">EvE-Scout</a> Alliance is to maintain a public list of <a href="showinfo:5//31000005">Thera</a>’s wormhole connections. Should you wish to travel to or through Thera, don’t hesitate to check our website: <a href="https://www.eve-scout.com/thera/">www.eve-scout.com</a>

On behalf of the EvE-Scout Rescue Division,
Have a great day and fly safe o/
</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>				
			</div>

	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="mailModalnewsar" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Eve-mail <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">
	  
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Subject</p>
					</div>
					<div class="step-text">
					<textarea class="copyable convo" rows="1" cols="64"
					data-fr="">EvE-Scout Rescue - Wrap Up</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>				
			</div>
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Body</p>
					</div>
					<div class="step-text">
<textarea class="copyable convo" rows="24" cols="64">
Dear <?= $pilot_name?>,

We are now looking for you. We will contact you as soon as we find you. Should we fail to do so in the next 7 days, we will follow-up with you to make sure you want us to continue our search.
Once again, our discord channel for stranded pilots can be found here: <a href="https://discord.gg/Xy6XEU">https://discord.gg/Xy6XEU</a>

If you wish to learn more about the EvE-Scout Rescue program, come visit  <a href="https://evescoutrescue.com">https://evescoutrescue.com</a>

We also have an in-game public channel: <a href="joinChannel:player_-65344012">EvE-Scout</a>. Come and say hello if you have the chance.

Another activity of <a href="showinfo:2//98372649">Signal Cartel</a> and the <a href="showinfo:16159//99005130">EvE-Scout</a> Alliance is to maintain a public list of <a href="showinfo:5//31000005">Thera</a>’s wormhole connections. Should you wish to travel to or through Thera, don’t hesitate to check our website: <a href="https://www.eve-scout.com/thera/">https://www.eve-scout.com/thera/</a>

On behalf of the EvE-Scout Rescue Division,
We hope to find you very soon o7

</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>				
			</div>	  


			
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="mailModalfailure" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Eve-mail <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">
	  
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Subject</p>
					</div>
					<div class="step-text">
					<textarea class="copyable convo" rows="1" cols="64"
					data-fr="">EvE-Scout Rescue - Wrap Up</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>				
			</div>
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Body</p>
					</div>
					<div class="step-text">
<textarea class="copyable convo" rows="24" cols="64">
Dear <?= $pilot_name?>,

We're very sorry we couldn't help you this time. We hope it won't prevent you from calling us whenever you need to in the future. If you wish to learn more about the EvE-Scout Rescue program, come visit <a href="https://evescoutrescue.com"> https://evescoutrescue.com </a>

We also have an in-game public channel: <a href="joinChannel:player_-65344012">EvE-Scout</a>, and a Discord server: <a href="https://discord.gg/6wNp5w">https://discord.gg/6wNp5w</a> Come and say hello if you have the chance.

Another activity of <a href="showinfo:2//98372649">Signal Cartel</a> and the <a href="showinfo:16159//99005130">EvE-Scout</a> Alliance is to maintain a public list of <a href="showinfo:5//31000005">Thera</a>’s wormhole connections. Should you wish to travel to or through Thera, don’t hesitate to check our website: <a href="https://www.eve-scout.com/thera/">www.eve-scout.com</a>

On behalf of the EvE-Scout Rescue Division,
Have a great day and fly safe o/
</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>				
			</div>	  


			
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="resultModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Result</h4>
	  </div>
	  <div class="modal-body">
			<div class="step">
				<p class="step-text" id="resultText">
					Result
				</p>				
			</div>

	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="noresponseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Pilot Not Responding <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">
	  
			<div class="step">
				<div class="step-label">
							<p class="step-inst">The pilot may have logged out for safety reasons.<br>
				In this case, send an eve-mail (Mail A) asking them to convo you with an alt, and invite them to join the stranded pilot's lounge on Discord.
				</p>					</div> 
			</div>	


			<div class="step">
				<div class="step-label">
							<p class="step-inst">In the rare case you get immediately kicked out of your conversation, the pilot may have set the private chat to automatically refuse new invitations. If that happens, try Mail B first.
				</p>					</div>
				 <p class "step-btn">
				 <button class="btn btn-default" onclick="changeMail('a')">Mail A</button> 
				 <button class="btn btn-default" onclick="changeMail('b')">Mail B</button> 
				</p> 
			</div>	

			<div class="step">
					<div class="step-label">
						<p class="step-inst">Subject</p>
					</div>
					<div class="step-text">
					<textarea class="copyable convo" id="mail-subj-nr" rows="1" cols="60" data-fr="Je fais partie de EvE-Scout Rescue Division o7">EvE-Scout Rescue - Your 911 call</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>				
			</div>
			<div class="step">
					<div class="step-label">
						<p class="step-inst">Body</p>
					</div>
					<div class="step-text">
					<textarea class="copyable convo" id="mail-body-nr" rows="20" cols="60" 
					data-fr="Cher <?= $pilot_name?>,   &#013;&#010;&#010;Vous avez déclenché un appel 911, et nous avons bien reçu votre demande. Nous sommes en train d’essayer de vous contacter en conversation privée, mais vous ne répondez pas. Il se peut que vous vous soyez déconnecté pour des raisons de sécurité. Dans ce cas, vous pouvez me joindre en conversation privée avec un alt, ou bien rejoindre la «Stranded Pilot Lounge» sur notre serveur Discord: &lt;a href=&quot;https://discord.gg/ru6Juw&quot;&gt;https://discord.gg/ru6Juw&lt;/a&gt;   &#013;&#010;&#010;Attention, ce salon est publique, ne révélez aucune information sur votre situation ou sur votre position. Dites juste bonjour et un Operateur EvE-Scout s’occupera de vous. &#013;&#010;&#010;Merci o/">Dear <?= $pilot_name?>, &#013;&#010;&#010;You issued a 911 call, and we received you request. We are trying to reach you in a private conversation, but you are not responding. You may have logged out for safety reasons. In that case, you can join me in a private conversation with an alt, or you can alternatively join the "Stranded pilot lounge" on our discord server here: &lt;a href="https://discord.gg/ru6Juw"&gt;https://discord.gg/ru6Juw&lt;/a&gt; &#013;&#010;&#010;Be aware that the Discord channel is a public channel, do not reveal any information about your position or situation. Just wave, and an EvE-Scout Rescue Operator will be in touch. &#013;&#010;&#010;Thank you o/</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>				
			</div>	
			
			<div class="step">
				<div class="step-label">
							<p class="step-inst">If pilot still does not reply, open a placeholder SAR 
				<button class="btn btn-danger" data-dismiss="modal" data-toggle="modal" data-target="#ModalSARNew">Open SAR</button>
				</p>
					</div> 
			</div>	
<script>			
function changeMail(choice){
	var subj = document.getElementById('mail-subj-nr'); 	
	var body = document.getElementById('mail-body-nr'); 	
	switch(choice) {
	  case 'a':
		subj.value = "EvE-Scout Rescue - Your 911 call";
		subj.dataset['fr'] = "EvE-Scout Rescue – Votre appel 911";
		
		body.value = "Dear <?= $pilot_name?>,   \r\n\r\nYou issued a 911 call, and we received you request. We are trying to reach you in a private conversation, but you are not responding. You may have logged out for safety reasons. In that case, you can join me in a private conversation with an alt, or you can alternatively join the \"Stranded pilot lounge\" on our discord server here: <a href=\"https://discord.gg/ru6Juw\">https://discord.gg/ru6Juw</a>   \r\n\r\nBe aware that the Discord channel is a public channel, do not reveal any information about your position or situation. Just wave, and an EvE-Scout Rescue Operator will be in touch.   \r\n\r\nThank you o/";
		body.dataset['fr'] = "Cher <?= $pilot_name?>,   \r\n\r\nVous avez déclenché un appel 911, et nous avons bien reçu votre demande. Nous sommes en train d’essayer de vous contacter en conversation privée, mais vous ne répondez pas. Il se peut que vous vous soyez déconnecté pour des raisons de sécurité. Dans ce cas, vous pouvez me joindre en conversation privée avec un alt, ou bien rejoindre la «Stranded Pilot Lounge» sur notre serveur Discord: <a href=\"https://discord.gg/ru6Juw\">https://discord.gg/ru6Juw</a>   \r\n\r\nAttention, ce salon est publique, ne révélez aucune information sur votre situation ou sur votre position. Dites juste bonjour et un Operateur EvE-Scout s’occupera de vous.   \r\n\r\nMerci o/";
		
		break;
		
	  case 'b':
		subj.value = "EvE-Scout Rescue - Your 911 call";
		subj.dataset['fr'] = "EvE-Scout Rescue – Votre appel 911";
		
		body.value = "Dear <?= $pilot_name?>,   \r\n\r\n You issued a 911 call, and we received you request. We are trying to reach you in a private conversation, but you are not responding. Because we are immediately kicked out of the conversation, we think you may have set your private chat to automatically refuse any new invitation. In order for us to help you, could you please either change your chat Settings (ESC-> CHAT tab -> Auto reject invitations), or initiate yourself a private chat with me?    \r\n\r\n Thank you very much o/";
		body.dataset['fr'] = "Cher <?= $pilot_name?>,   \r\n\r\nVous avez déclenché un appel 911, et nous avons bien reçu votre demande. Nous sommes en train d’essayer de vous contacter en conversation privée, mais vous ne répondez pas. Parce que nous sommes immédiatement ejectés de la conversation, nous pensons que vous avez reglé vos configuré vos paramètres de chat de telle sorte que les invitations soient automatiquement refusées. Afin que nous soyons en mesure de vous aider, pourriez-vous reconfigurer vos paramètres (ESC->Chat->Rejeter automatiquement les invitations), ou m’inviter vous-même dans une nouvelle conversation privée?   \r\n\r\nMerci beaucoup o/";
		
		break;
		
	  default:
		return;
	}	
}

function changeOS(choice){
	var convoDiv = document.getElementById('convoDiv'); 	
	switch(choice) {
	  case 'windows':
		convoDiv.value = "On Windows, you should find your logs under ~Documents\\EVElogs\\Chatlogs In that folder, look for the last Local_date file.";
		break;
	  case 'mac':
		convoDiv.value = "On a Mac, you should find your logs under /Users/<name>/Documents/EVE/logs/Chatlogs";
		break;
	  case 'linux':
		convoDiv.value = "On Linux, you should find your logs under ~/.steam/steam/steamapps/compatdata/8500/pfx/drive_v/users/steamuser/MyDocuments/EVE/logs/Chatlogs In that folder, look for the last Local_date file.";
		break;
		default:
		return;
	}	
}
</script>
			
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="unsureModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Pilot not safe <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">

			<div class="step">
				<div class="step-label">
							<p class="step-inst">Is the pilot being actively hunted?
				</p>					</div> 
			</div>	
			
			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo" rows="2" cols="60" 
					data-fr="Est-ce que quelqu’un est en train de vous chasser?"
					>Is someone hunting you right now?</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>

			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo" rows="2" cols="60"
					data-fr="Je peux attendre que vous trouviez un endroit sûr. Ne prenons aucun risque."
					>I can wait for you to find a safe spot. We don't want to take any risk.</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>
			
			
			<div class="step">
				<div class="step-label">
							<p class="step-inst">Suggest that the pilot log out and join us with alt, or on Discord:
				</p>					</div> 
			</div>	
			
			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo"  rows="2" cols="60"
					data-fr="Si vous pensez que vous êtes en danger, vous pouvez vous déconnecter et me joindre avec un alt dans une autre conversation privée."
					>If you feel you are not safe, you can safe log with this character and join me with an alt in a new private chat.</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>

			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo" rows="4" cols="60"
					data-fr="Vous pouvez aussi joindre notre «Stranded pilot lounge» sur notre serveur Discord: https://discord.gg/ru6Juw (Attention, ce salon est publique, ne révélez aucune information sur votre situation ou sur votre position. Dites juste bonjour et un Operateur EvE-Scout s’occupera de vous.)"
					>You can also join our stranded pilot lounge on Discord: https://discord.gg/ru6Juw    (be aware that it's a public channel, don't reveal any information about your situation: just wave and an EvE-Scout Rescue Operator will be in touch). </textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>

			<div class="step">
				<div class="step-label">
							<p class="step-inst">Or suggest pilot call us back later.
				</p>					</div> 
			</div>
			
			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo" rows="2" cols="60"
					data-fr="Vous pouvez toujours déclencher un appel 911 plus tard, quand la situation sera moins risquée. Nous attendrons votre appel."
					>You can always issue a 911 later, when the situation looks safer. We will hold and wait for your call.</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>

			<div class="step">
				<div class="step-label">
							<p class="step-inst">In these cases, open a placeholder SAR 
				<button class="btn btn-danger" data-dismiss="modal" data-toggle="modal" data-target="#ModalSARNew">Open SAR</button>
				</p>					</div> 
			</div>	

			
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="faqModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">FAQs <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">

				<h4>Ready made answers to common pilot questions!</h4>
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">What do you charge for a rescue?</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo" style="height: 1.8em; width:40em;">Our rescue services are completely free, but donations are always appreciated.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">How can I make a donation?</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo" style="height: 3.2em; width:40em;">You can donate in-game to the corp Signal Cartel. Please put "ESR" or "Rescue" in the memo line.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				
				<div class="step">
					<div class="step-label">
							<p class="step-inst">What is a safe spot?</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo" style="height: 11em; width:40em;">A safe spot is a location in space away from any celestial or structure that you can warp to for evading hostiles. To create a create a safe spot, you have to warp from a celestial (or an object in space) to another one, create a bookmark while warping (ctrl+b) and place it (ENTER) between those two entities. You can then warp to that bookmark and you are a little more safe. You can create multiple safe spots in a system, and warp from one to another for more safety. You can even create safe spots between two safe spots (even safer).</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">How to use a filament?</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo" style="height: 8em; width:40em;">First, you have to form a fleet with yourself (right-click on your own name and select “form fleet with”), and put your safety on “partial” (green button on the top left corner of your HUD). Put the filament in your cargo, right click on it and launch the filament. Don’t forget to put your safety back to “enable” (green) after landing in null sec.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>

				<div class="step">
					<div class="step-label">
							<p class="step-inst">Why don’t you put a mobile depot in your caches?</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
						<textarea class="copyable convo" style="height: 5em; width:40em;">We would love to be able to put mobile depots in our rescue caches, but physics are against us: it’s impossible to put a “container” inside another “container”. We tried.</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				

			
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>

<div id="utilityModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">UTILITIES</span></h4>
	  </div>
	  <div class="modal-body">
		
				<div class="step">
					<div class="step-label">
							<p class="step-inst">Copy the following into an in game note and always have these items ready to 'drag' into an eve-mail or chat window</p>					</div>
				</div>
				<div class="step">
				<div class="step-text">
<textarea class="copyable convo" rows="10" cols="64"
><a href="showinfo:3467">Small Secure Container</a>
<a href="showinfo:17938">Core Probe Launcher I</a>
<a href="showinfo:30013">Core Scanner Probe I</a>
<a href="showinfo:53977">Noise-5 'Needlejack' Filament</a>
<a href="showinfo:33474">Mobile Depot</a>
Public channel <a href="joinChannel:player_-65344012">EvE-Scout</a>
Corporation infor for <a href="showinfo:2//98372649">Signal Cartel</a>
</textarea>
						<span class="copybutton" onclick="SelectAllCopy(this)"></span>
					</div>
				</div>
				

			
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>


<div id="doesntRememberModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Other ways to discover last k-space system <span class="flag en"></span></h4>
	  </div>
	  <div class="modal-body">

			<div class="step">
				<div class="step-label">
							<p class="step-inst">1. Check in-game log files 
				<br><em>This will not work if pilot has full logs, or has logged out since entering system.</em>
				</p>					</div> 
			</div>	
			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo" rows="2" cols="60">Let's look in your Eve game logs. You may find your last known-space system in there. Go to Necom->Utilities->Logs&Messages</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>


			<div class="step">
				<div class="step-label">
							<p class="step-inst">2. Check Eve program logs on computer</p>					</div> 
			</div>	
			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo" rows="2" cols="60">We might have another chance with Eve's program logs on your computer. Do you know how to access them?</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>

			<div class="step">
				<div class="step-label">
							<p class="step-inst">If the pilot doesn't know</p>					</div>
				<div class="step-text">
					<input type="text" class="copyable convo" style="width: 400px;" value="What operating system are you running? Windows? Mac? Linux?">
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>
			</div>						
			<div class="step">
				 <p class "step-btn">
				 <button class="btn btn-default" onclick="changeOS('windows')">Windows</button> 
				 <button class="btn btn-default" onclick="changeOS('mac')">Mac</button> 
				 <button class="btn btn-default" onclick="changeOS('linux')">Linux</button> 
				 </p> 
			</div>		
			<div class="step">
				<div class="step-text">
					<textarea class="copyable convo"  style="height:5em; width: 90%;" id="convoDiv">Click an OS button above to reveal instruction</textarea>
					<span class="copybutton" onclick="SelectAllCopy(this)"></span>
				</div>			
			</div>			
			

			
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>
<!-- END MODAL ------------------------------------------------>

</div><!-- END #ra -->            

<div id="AgentModal" class="modal fade" role="dialog">
<!-- Agent Modal Form -->
<?php
// get active cache info
$database = new Database();
$caches = new Caches($database);
$rowAgent = $caches->getCacheInfo($pilot_system);
$cacheid = $rowAgent['CacheID'];
?>

  <div class="modal-dialog">

	<!-- Modal content-->
	<div class="modal-content">
	  <div class="modal-header adjunct">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Agent</h4>
	  </div>
	  <form name="agentform" id="agentform" action="ra_process_agent.php" method="POST">
		  <div class="modal-body black">
			<div class="form-group">
				<label class="control-label" for="sys_adj">System:
					<input type="hidden" name="sys_adj" value="<?=$pilot_system?>"/>
				</label>
				<span class="sechead"><?=$pilot_system?></span>
			</div>
			<div class="checkbox">
				<label class="control-label" for="updateexp">
					<input type="checkbox" id="updateexp" name="updateexp" value="1" onClick="checkLogic(this)">
					Cache was accessed					
				</label>
			</div>
			<span class="control-label">Pilot was rescued using:</span>
			<div class="checkbox">
				<label class="control-label" for="succesrc">
					<input type="checkbox" id="succesrc" name="succesrc" value="1" onClick="checkLogic(this)">	
					Probes & scanner
				</label>
			</div>
			<div class="checkbox">
				<label class="control-label" for="succesrcf">
					<input type="checkbox" id="succesrcf" name="succesrcf" value="1" onClick="checkLogic(this)">
					Filament					
				</label>
			</div>
			<div class="field">
				<label class="control-label" for="aidedpilot">Aided Pilot<span class="descr">What is the name of the Capsuleer who required assistance?</span>
					<input type="text" readonly class="form-control" id="aidedpilot" name="aidedpilot" value="<?=$pilot_name?>"/>
				</label>
			</div>
			<div class="field">
				<label class="control-label" for="agentnotes">Notes<span class="descr">Other information we need to know?</span>
					<textarea maxlength="1000" class="form-control" id="agentnotes" name="notes" rows="12"></textarea>
				</label>
			</div>
		  </div>
		  <div class="modal-footer">
			<div class="form-actions">
					<input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
					<input type="hidden" name="CacheID" value="<?=$cacheid?>" />
				<button type="submit" class="btn btn-info">Submit</button>
				<script>
				$('#agentform').submit(function() {
					$('#AgentModal').modal('hide');
					ChangePage(12);
				});
				</script>				
			</div>
		  </div>
	  </form>
	</div>
  </div>
</div>

</body>
<script>

var showlog = true;
var priorPage = Array();
var currentPage = 'page1';
var language = 'en';

$("#ModalSARNew").on('show.bs.modal', function(){
   CopyNoteFieldsTo('sarnotes');
});

$("#ModalSAREdit").on('show.bs.modal', function(){
   CopyNoteFieldsTo('sareditnotes');
});

$("#AgentModal").on('show.bs.modal', function(){
   CopyNoteFieldsTo('agentnotes');
});


function CopyNotesTo(element){
	var notes = document.getElementById('notes-text').value;
	el = document.getElementById(element);
	el.value = notes;
}

function CopyNoteFieldsTo(element){
	var noteText = "";
	var notes = document.getElementsByClassName("note-text");
	for(var i=0; i < notes.length; i++) {
		if (notes[i].value != ""){noteText += notes[i].dataset['lab'] + ": " + notes[i].value + "\r\n"}
	}
	var other = document.getElementById('other-text').value;
	if (other.value){noteText += "Other Info:" + other.value;}	
	el = document.getElementById(element);
	el.value = noteText;
}

function SelectAllCopy(ele) {
	//default english in first child
	var textBox = ele.previousElementSibling;
	//console.log(textBox);

	//textBox.dataset['fr'];
	if (language != 'en' && textBox.dataset[language]){
		var dummy = document.createElement("textarea");
		document.body.appendChild(dummy);
		dummy.value = textBox.dataset[language];
		dummy.select();
		document.execCommand("copy");
		document.body.removeChild(dummy);			
	}
	else{
		textBox.focus();
		textBox.select();
		document.execCommand("Copy");	
		let sel = document.getSelection();
		sel.removeAllRanges();		
	}
	textBox.classList.add("glow");
	setTimeout(function(){textBox.classList.remove("glow");},1000);
}

function changeLang() {
	language = document.getElementById("lang-picker").value;
	var flags = document.getElementsByClassName("flag");
	var classnames = "flag " + language;
	for(var i=0; i < flags.length; i++) {
		flags[i].className = classnames;
	}
	var buts = document.getElementsByClassName("copybutton");
	var lang = (language == "en" ? "" : language); 
	for(var i=0; i < buts.length; i++) {
		buts[i].innerText = lang;
	}
}
  

function ChangePage(pagenum) {
	pageshow = 'page' + pagenum;
	pagehide = currentPage;
	priorPage.push(currentPage);
	currentPage = pageshow;
	var hide = document.getElementById(pagehide);
	var show = document.getElementById(pageshow);
	hide.classList.toggle("shown");
	hide.classList.toggle("notshown");
	show.classList.toggle("shown");
	show.classList.toggle("notshown");
}

function BackPage() {
	pageshow = priorPage.pop();
	if (pageshow == 'undefined' ) return;
	
	pagehide = currentPage;
	currentPage = pageshow;
	
	var hide = document.getElementById(pagehide);
	var show = document.getElementById(pageshow);
	hide.classList.toggle("shown");
	hide.classList.toggle("notshown");
	show.classList.toggle("shown");
	show.classList.toggle("notshown");
}


function CacheText(choice){
	var ele = document.getElementById('textcache'); 
	var sr = document.getElementById('supplyreturn'); 	
	switch(choice) {
	  case 'probes':
		ele.value = "We have a cache in your system with some probes. I'm going to send you a mail with the bookmark to the cache's location, and the password of the cache.";
		ele.dataset['fr'] = "Nous avons un container dans votre système avec des probes. Je vais vous envoyer par eve-mail le bookmark du container, ainsi que le mot de passe.";
		sr.style.display = 'block';
		break;
	  case 'launcher':
		ele.value = "We have a cache in your system with a launcher and some probes. I'm going to send you a mail with the bookmark to the cache's location, and the password of the cache.";
		ele.dataset['fr'] = "Nous avons un container dans votre système avec des probes et un lanceur de probes. Je vais vous envoyer par eve-mail le bookmark du container, ainsi que le mot de passe";
		sr.style.display = 'block';
		break;
	  case 'filament':
		ele.value = "We have a cache in your system with a filament. I'm going to send you a mail with the bookmark to the cache's location, and the password of the cache.";
		ele.dataset['fr'] = "Nous avons un container dans votre système avec un filament. Je vais vous envoyer par eve-mail le Signet du container, ainsi que le mot de passe";
		sr.style.display = 'none';
		break;
	  default:
		return;
	}	
}

	
 function checkLogic(ele){
	var cacheAccessed = document.getElementById('updateexp');
	var usedProbes = document.getElementById('succesrc');
	var usedFilament = document.getElementById('succesrcf');
	var choice = ele.id;
	switch(choice) {
	  case 'updateexp':
		if (usedFilament.checked == true || usedProbes.checked == true){
			cacheAccessed.checked = true;	
		}
		break;
	  case 'succesrc':
			usedFilament.checked = false;
			cacheAccessed.checked = true;
		break;
	  case 'succesrcf':
			usedProbes.checked = false;
			cacheAccessed.checked = true;
		break;
	  default:
		return;
	}

 }



</script>

</html>
