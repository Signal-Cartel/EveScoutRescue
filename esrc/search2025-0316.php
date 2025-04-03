<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';//this starts the session
include_once '../class/users.class.php';
include_once '../class/config.class.php';
require_once '../class/password.class.php';
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/caches.class.php';
require_once '../class/systems.class.php';
require_once '../class/output.class.php';
require_once '../class/rescue.class.php';

function debug($output){
	echo "<script>console.log(JSON.parse('" . json_encode($output) . "'));</script>";
}
// set password to use for new cache
// if "pass" parameter is passed in, use that for password instead
$cachepass = (isset($_REQUEST['pass']) ? $_REQUEST['pass'] : Password::generatePassword());

// check if the user is alliance member
if (!Users::isAllianceUserSession()){
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
unset($_SESSION['AUTH_NOALLIANCE']);

// Planet numbers used in the modals
$romans = Array(
	'Star' => 'Star',
	'See Note' => '**',
	'0' => 0,
	'I' => 1,
	'II' => 2,
	'III' => 3,
	'IV' => 4,
	'V' => 5,
	'VI' => 6,
	'VII' => 7,
	'VIII' => 8,
	'IX' => 9,
	'X' => 10,
	'XI' => 11,
	'XII' => 12,
	'XIII' => 13,
	'XIV' => 14,
	'XV' => 15,
	'XVI' => 16,
	'XVII' => 17,
	'XVIII' => 18,
	'XIX' => 19,
	'XX' => 20,
	'XXI' => 21,
	'XXII' => 22,
	'XXIII' => 23,
	'XXIV' => 24,
	'XXV' => 25,
	'XXVI' => 26,
	'XXVII' => 27,
	'XXVIII' => 28,
	'XXIX' => 29,
	'XXX' => 30	
)
?>

	<?php
	$pgtitle = 'ESRC Search';
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
$systems = new Systems($database);
$rescue = new Rescue($database);
$leaderBoard = new Leaderboard($database);

// check for pilot roles
$isRecentSower = 0;
if (!isset($_SESSION['isAdmin'])){
	$isAdmin = $_SESSION['isAdmin'] = $users->isAdmin($charname);	
}
else{
	$isAdmin = $_SESSION['isAdmin'];
}
if (!isset($_SESSION['isCoord'])){
	$isCoord = $_SESSION['isCoord'] = ($isAdmin or $users->isSARCoordinator($charname));	
}
else{
	$isCoord = $_SESSION['isCoord'];
}
if (!isset($_SESSION['is911'])){
	$is911 = $_SESSION['is911'] = ($isCoord or $isAdmin or $users->is911($charname));	
}
else{
	$is911 = $_SESSION['is911'];
}


if ($_SERVER['HTTP_HOST'] === 'dev.evescoutrescue.com' && !empty($_SESSION['livedata']) && $_SESSION['livedata'] != 1) {
    if (!empty($_REQUEST['r'])) {
        // Initialize variables and session values
        $is911 = $_SESSION['is911'] = $isAdmin = $_SESSION['isAdmin'] = $isCoord = $_SESSION['isCoord'] = 0;
        $r = $_REQUEST['r'];
        if ($r === 'a') $isAdmin = $_SESSION['isAdmin'] = 1;
        if ($r === 'c' || $isAdmin) $is911 = $_SESSION['is911'] = $isCoord = $_SESSION['isCoord'] = 1;
        if ($r === '9' || $isAdmin || $isCoord) $is911 = $_SESSION['is911'] = 1;
    }
}



$system = '';
if(isset($_REQUEST['sys'])) {
	if (ucfirst(htmlspecialchars_decode($_REQUEST['sys'])) != 'Thera'){
		$systems = new Systems($database);		
		$trysys = ucfirst(htmlspecialchars_decode($_REQUEST['sys']));
		if (! (substr ( $trysys, 0, 1 ) === 'J')) $trysys = 'J'. $trysys;	
		if ($systems->validatename($trysys) === 0) {
			$system = $trysys;
		}			
	}
	else{
		$system = 'Thera';
	}
}

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }

$activeSAR = $activeSARtitle = '';
$islocatepilot = false;
// get active SAR requests of current system if locate pilot or 911 operator or higher
$requests = $rescue->getSystemRequests($system, 0, $isCoord);
if (count($requests) > 0) {
	foreach ($requests as $request){
		if ($request['locateagent'] == $charname) {$islocatepilot = true;}
	}
	if ($islocatepilot or $is911 ){
		$activeSAR = ' <span style="font-weight: bold; color: red;">(!)</span>';
		$activeSARtitle = '&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #ff6464;"> ACTIVE SAR SYSTEM!</span>';
	}
}


// CONFIRM PILOT'S IN-GAME LOCATION
$pilotLocStat = '';
// does not apply to SAR Coordinators
if ($isCoord == false) {
	// check for Allison login (required to sow/tend caches)
	if (isset($_SESSION['auth_char_location'])) {
		// check if pilot has sown/tended over 300 caches; if so, they are excluded from this check
		// This is awfully inefficient - ADP
		
		if (!isset($_SESSION['megacacher'])){
			$_SESSION['megacacher'] = 0;
			$daysdiff = round((strtotime("+1 day")- strtotime("2017-03-01")) / (60 * 60 * 24));
			$rows = $leaderBoard->getTop(2000, $daysdiff);
			$bitPilotMatch = 0;
			foreach ($rows as $value) {
				if ($charname ==  $value['Pilot']) {
					if ($value['cnt'] >= 300) {
						$_SESSION['megacacher'] = 1;
						break;
					}
				}
			}
		}
		// DISABLE MEGA CACHER PRIVELEGE TEMPORARILY
		if (true or $_SESSION['megacacher'] == 0) {
			// otherwise, pilot may only sow/tend caches for a system they are verified to be present in
			if ($_SESSION['auth_char_location'] != $system)  {
				$pilotLocStat = 'not_in_system';				
				$strBtnAttrib = 'data-toggle="tooltip" title="You must be in '.
					$system .' to perform this action, but you are in '.
					$_SESSION['auth_char_location'].'"';
			}
		}
	}
	else {
		$pilotLocStat = 'not_in_allison';
		$strBtnAttrib = 'data-toggle="tooltip" title="You must be logged into ALLISON in order to enter ESRC data."';
	}
}
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
	<div class="row" id="errormessage" style="background-color: #ff9999;">
		<div class="col-md-12 message">
			<?php echo nl2br($errmsg); ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
}


// will not retrieve cache password (or allow sow) if you are not in system or 911 or higher
$limited_data = $_SESSION['limited_data'] = 1;
if (isset($_SESSION['auth_char_location']) and ($_SESSION['auth_char_location'] == $system)) $limited_data = $_SESSION['limited_data'] = 0;
if (isset($_SESSION['prior_system']) and ($_SESSION['prior_system'] == $system)) $limited_data = $_SESSION['limited_data'] = 0;
if ($is911) $limited_data = $_SESSION['limited_data'] = 0;

// check if a system is supplied	
if (!empty($system)) {
	// display result for the selected system
	// get cache information from database
	$caches = new Caches($database);	
	$cache_info = $caches->getCacheInfo($system, $limited_data);// returns empty if no cache
	//debug($system); debug($cache_info);
	$isTendingAllowed = $caches->isTendingAllowed($system);

	$strNotes = '';
	//only display the following if we got some results back
	if (!empty($cache_info)) {
	$status_display = "";
		$isRecentSower = $caches->isRecentSower($charname, $cache_info['CacheID']);
		// calculate status color and words to display
		$statuscellformat = '';
		switch ($cache_info ['Status']) {
			case 'Healthy':
				$statuscellformat = ' style="color: #32ff32;"';//green
				$status_display = (0 == $isTendingAllowed) ? ": recently tended" : ": needs to be tended";
				break;
			case 'Upkeep Required':
				$statuscellformat = ' style="color:gold;"';
				$status_display = " (possibly missing supplies)";
				break;
			case 'Locals Tend':
				$statuscellformat = ' style="color:#ff6464;"';// red
				break;
			default:
				$statuscellformat = '';
		}
		
		// save notes as separate var
		$strNotes = ($cache_info['Note']);
		
			
		?>
		
		
		<!-- action button row -->
		<div class="row" id="systableheader">
			<div class="col-md-12">
				<div style="padding-left: 10px;">
					<!-- System Name display -->
					<p class="systemName"><?=$system . "<span $statuscellformat> " . $cache_info ['Status'] . $status_display  . "</span>". $activeSARtitle ?></p>
					<!-- TEND button -->
					<?php
					$strTended = '';
					
					if (0 == $isTendingAllowed) {
						$strTended = ' <i class="white fa fa-clock-o"></i>';
					}
					
					//check pilot status
					if ($pilotLocStat == '') {
						$strBtnAttrib = 'data-toggle="modal" data-target="#TendModal"';
					}
					?>
					<button type="button" class="btn btn-primary" role="button" <?=$strBtnAttrib?>>
						Tend<?=$strTended?></button>
					
					<!-- AGENT and SAR buttons (if 911 or higher)-->
					<?php
					if($is911){
						
						echo '<button type="button" class="btn btn-warning" role="button" data-toggle="modal"  data-target="#AgentModal">';
						echo 'Agent</button>';					
						echo '<a href="rescueoverview.php?new=1&sys=' . $system . '" class="btn btn-danger" role="button">New SAR</a>';
					}
					?>
					<!-- TW button -->
					<a href="https://tripwire.eve-apps.com/?system=<?=$system?>" class="btn btn-info"
						role="button" target="_blank">Tripwire</a>
					
					<!-- anoik.is button -->
					<a href="http://anoik.is/systems/<?=$system?>" class="btn btn-info" role="button"
						target="_blank">anoik.is</a>
					
					<!-- Chains and Edit buttons, if relevant -->
					<?php
					// "chains" button is Coord-only
					if ($isCoord) {
						echo '<a href="/copilot/data/chains?system='. $system .'" class="btn btn-info"
							role="button" target="_blank">Chains</a>&nbsp;&nbsp;&nbsp;';
					}
					//edit function only available to Coordinators and recent sowers

					if ($isCoord || $isRecentSower) {
						echo '<button type="button" class="btn btn-success" role="button" data-toggle="modal"
							data-target="#EditModal">Edit Cache</button>';
					}

					
					
					?>
				</div>
				<div class="ws"></div>
			</div>
		</div>
		
		<div class="row" id="systable">
			<div class="col-md-12">
				<!-- DETAIL RECORD -->
				
				
				
				<table class="table" style="">
					<thead>
						<tr>
							<th>Sown On</th>
							<th>Aligned With</th>
							<th>Location</th>
							<? if (isset($cache_info['Password'])){
								echo '<th>Distance</th>
									<th>Password</th>';
							}

							?>
							<!--
							<th>Probes</th>
							<th>Filament</th>
							-->
							<th>Expires On</th>
							<th>Bookmark</th>
						</tr>
					</thead>
					<tbody>
					<tr>
					<td><?=Output::getEveDate($cache_info['InitialSeedDate'])?></td>
					<td><?=$cache_info['AlignedWith']?></td>
					<td><?=$cache_info['Location']?></td>
					
					<? 
					if(!isset($cache_info['Password'])) {$sty_le = ' style="display:none;"';}else{$sty_le='';}; 
					?>
					
					<td<?=$sty_le?>>
						<? echo isset($cache_info['Distance']) ? Output::htmlEncodeString($cache_info['Distance']) : '&nbsp;';?>
					</td>
					<td<?=$sty_le?>>
						<input type="text" id="cachepass1" style="width:100px;"
							value="<? echo isset($cache_info['Password']) ? Output::htmlEncodeString($cache_info['Password']) : '';?>" readonly />
							<i id="copyclip" class="fa fa-clipboard"
								onClick="SelectAllCopy('cachepass1')"></i>
					</td>
					
					<?php
					// probes and filament status
					
					$haspas = ($cache_info['has_pas'] == 1 ? 'Yes' : 'No');
					$statpas = ($cache_info['has_pas'] == 1 ? ' style="color:rgba(90, 230, 90, .8);"' : ' style="color:rgba(234, 10, 10, .9);"');
					$hasfil = ($cache_info['has_fil'] == 1 ? 'Yes' : 'No');
					$statfil = ($cache_info['has_fil'] == 1 ? ' style="color:rgba(90, 230, 90, .8);"' : ' style="color:rgba(234, 10, 10, .9);"');
					
					?>
					<!--
					<td<?=$statpas ?>><?=$haspas?></td>
					<td<?=$statfil ?>><?=$hasfil?></td>
					-->
					<td><?=Output::getEveDate($cache_info['ExpiresOn'])?></td>

					<td><input type="text" id="bookmark1" style="width:180px;"
							value="<?= $system . ' Rescue Cache' ?>" readonly />
							<i id="copyclip2" class="fa fa-clipboard"
								onClick="SelectAllCopy('bookmark1')"></i>
					</td>


					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		// log the PW access
		if (true and isset($cache_info['Password'])) {
			$fname = "passlog20240809.csv";
			$logData = [
				gmdate("Y-m-d H:i:s"),
				$cache_info['CacheID'],
				str_replace(" ", "_", $system),
				str_replace(" ", "_", $charname),
				$_SESSION['auth_characterid']
			];
			if (($handle = fopen($fname, 'a')) !== FALSE) {
				fputcsv($handle, $logData);
				fclose($handle);
			}
		}
	}
	else {
		// no results returned, so give an option to sow a new cache in this system
		// check for valid system name
		if ($systems->validatename($system) !== 0) {		
		?>
			<div class="row" id="systableheader">
				<div class="col-md-12">
					<div style="padding-left: 10px;">
						<span class="subhead white">'<?=$system?>' not a valid system name. Please correct name and resubmit.&nbsp;&nbsp;&nbsp;</span>
					</div>
				</div>
			</div>
			
		<?php
			exit();
			
		}
		
		
		
			$lockedDate = $systems->locked($system);

			if (!isset($lockedDate))
			{
				// yes, create a link to the data entry page
				?>
			 <div class="row" id="systableheader">
				<div class="col-md-12">
					<div style="padding-left: 10px;">
						<!-- System Name display -->

						<p class="systemName"><? echo($system . "<span style=\"color: red;\"> No Cache</span>" . $activeSARtitle); ?></p>



						<!-- SOW button  -->
					<?php
						//check pilot status
						if (($limited_data === 0)){	
							$sowBtnAttrib = 'data-toggle="modal" data-target="#SowModal"';
						}
						else{
							$sowBtnAttrib = 'data-toggle="tooltip" title="You must be logged into Allison and located in '.
											$system .' to perform this action"';
						}
					?>
					
						<button type="button" class="btn btn-success" role="button" <?=$sowBtnAttrib?>>
							Sow New Cache</button>&nbsp;&nbsp;&nbsp;
														
						<!-- SAR New button -->
						<?php
						if ($is911){
							echo '<a href="rescueoverview.php?new=1&sys=' . $system . '" class="btn btn-danger"
							role="button">New SAR</a>&nbsp;&nbsp;&nbsp;';
						}
						?>
						
						<!-- TW button -->
						<a href="https://tripwire.eve-apps.com/?system=<?=$system?>"
							class="btn btn-info" role="button" target="_blank">Tripwire</a>&nbsp;&nbsp;&nbsp;
						
						<!-- anoik.is button -->
						<a href="http://anoik.is/systems/<?=$system?>" class="btn btn-info"
							role="button" target="_blank">anoik.is</a>
				
						<!-- Chains button if relevant -->
						<?php
						// "chains" button is Coord-only
						if ($isCoord) {
							echo '<a href="/copilot/data/chains?system='. $system .'" class="btn btn-info"
								role="button" target="_blank">Chains</a>&nbsp;&nbsp;&nbsp;';
						}
						?>
					
							
						<br />

						<!-- Name for new cache -->
						<p>NAME <input type="text" id="cachename2" style="width:620px; margin-top: 5px;"
							value="EvE-Scout Rescue Cache - Stranded in this wormhole? Request help in the EvE-Scout channel." readonly />
							<i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy('cachename2')"></i>


						<!-- Password for new cache -->
						&nbsp;PASS <input type="text" id="cachepass2" style="width:125px; margin-top: 5px;"
							value="<?=$cachepass?>" readonly />
							<i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy('cachepass2')"></i></p>
					</div>
				</div>
			 </div>


			 <?php
			}
			else {
				?>
			<div class="row" id="systableheader">
				<div class="col-md-12">
					<div style="padding-left: 10px;">
						<p class="systemName"><? echo($system . "<span style=\"color: red;\"> No Sow System</span>") ?></p>
						<p>
							<span class="subhead white">Upon request of the current wormhole residents,
								caches are not to be sown in <?=$system?> until
								<?=date("Y-M-d", strtotime($lockedDate))?>.
							</span>
						</p>					
						<!-- TW button -->
						<a href="https://tripwire.eve-apps.com/?system=<?=$system?>" class="btn btn-info"
							role="button" target="_blank">Tripwire</a>				
						<!-- anoik.is button -->
						<a href="http://anoik.is/systems/<?=$system?>" class="btn btn-info" role="button"
							target="_blank">anoik.is</a>				
						<!-- Chains and Edit buttons, if relevant -->
						<?php
						// "chains" button is Coord-only
						if ($isCoord) {
							echo '<a href="/copilot/data/chains?system='. $system .'" class="btn btn-info"
								role="button" target="_blank">Chains</a>&nbsp;&nbsp;&nbsp;';
						}
						?>
					</div>
				</div>
			</div>
				<?php
			}
		

	} //(!empty($row))

		
	?>
		<!-- NOTE(S) SECTION -->
		<p style="margin-left: 36px"><em>Signaleers, if you see a <b>system note</b> that seems inaccurate or out of date, please mention that in your cache tending note.</em></p>
		<div style='
				display: flex;
				justify-content: flex-end;
				flex-direction: column;
				align-items: flex-end;
				margin: 12px 64px 0px 0px;
			'>
			
			<style type="text/css" scoped>
				p.notes { 
					margin: 0px;
					font-size: 1.4em;
					line-height: 1.1em;
					font-weight: normal;
					color: white;
				}
				
				p.notes.temp{ 
					color: white;
				}
				p.notes.info { 
					color: #32ff32;
				}
				
				p.notes.warn { 
					color: #ff6464;
				}
				
				a.notes {
					font-size: .6em;
				}
				
				div.cachenote {
					width: 90%;
					position: relative;
					    border-left: 0.3em solid #224422;
						background: linear-gradient(90deg, rgba(34,68,34,1) 1%, rgba(34,68,34,0.05) 6%, rgba(34,68,34,0) 100%);
					padding: 14px 0px 0px 32px;
					margin-bottom: 12px;
				}
				div.sysnote {
					width: 90%;
					position: relative;
					    border-left: 0.3em solid #224422;
						background: linear-gradient(90deg, rgba(34,68,34,1) 1%, rgba(34,68,34,0.05) 6%, rgba(34,68,34,0) 100%);
					padding: 14px 0px 0px 32px;
				}
				 h3 {
					font-size: 1rem;
					color: white;
					text-transform: uppercase;
					letter-spacing: 3px;
					opacity: 0.6;
					position: absolute;
					top: -18px;
					left: -66px;
					width: 115px;
					text-align: right;
				 }
			</style>
			
			
					
			<?php			
			if (true or $strNotes != ''){
				echo '<div class="cachenote"><h3>Cache notes</h3><p class="notes temp">' . Output::htmlEncodeString($strNotes) . '</p>';
				//<a href="javascript:void(0)" class="notes" data-toggle="modal" data-target="#EditModal">edit delete</a>
			}
			//new note only available to Coordinators?
			if ((true or $isCoord) and !empty($cache_info)) {
					echo '<a href="#" class="notes" data-toggle="modal" data-target="#NoteModal">
					<i class="white" style="font-size: 1.1rem; position:absolute; right: 0px; top: 0px;">
					<span class="white fa fa-plus">&nbsp;</span>New Cache Note</i>
					</a>';
			}

			// $arrSysnotes is set in top-middle.php from getSystemNotes() in systems.class 
			if (true or !empty($arrSysnotes)) {
				echo '</div><div class="sysnote"><h3>System notes</h3>';
				foreach ($arrSysnotes as $val) {
					
					$colorclass = 'temp';
					if (!is_null($val['notetype'])){
						$colorclass = $val['notetype']; 
					}
					
					
					echo '<p class="notes ' . $colorclass . '">';
					// provide a visual indicator that the note has been edited
					// if (!is_null($val['LastUpdated'])) { echo ' <strong>* </strong>'; }

					echo (Output::htmlEncodeString($val['note']));
					// the person who created the note can edit; coordinators can edit AND delete
					
					if ($isCoord || $charname == $val['noteby']) { 
						echo '&nbsp;<a class="notes" href="' . $phpPage . '?sys=' . $system . '&noteid=' . $val['id'] . '"><span class="white fa fa-edit">&nbsp;</span></a>'; 
					}
					if ($isCoord) { 
						echo '&nbsp;<a class="notes" href="' . $phpPage . '?sys=' . $system . '&noteid=' . $val['id'] . '&notedel=1"><span class="white fa fa-trash">&nbsp;</span></a>';
					}
					
					echo '</p>';
				}
				
			}


				if ($isCoord) { 
				//<!-- New note -->
				echo '<a href="#" data-toggle="modal" data-target="#ModalSysNotesEdit">
						<i class="white" style="font-size: 1.1rem; position:absolute; right: 0px; top: 0px;">
						<span class="white fa fa-plus">&nbsp;</span>New System Note</i>
					</a>';
				}
?>					
			</div>				
		</div>
		
<?php
		

	//HISTORY
	// see if there is historical data to display for this system
	$systemActivities = $systems->getSystemHistory($system);
	$actioncount = 0;
	if (!empty($systemActivities)) {
		
		$actioncount = count($systemActivities);
		echo '<div class="row" id="historytable">';
		echo '<div class="col-md-12">';
		echo '<div style="padding-left: 0px;">';
		echo "<br /><span class='subhead'>HISTORY: $actioncount actions</span><br />";
		
		echo '<table class="table" style="
				font-size: 1em;
				font-weight: normal;
				color: #a7a7a7;
				">
				<thead>
					<tr>
						<th class="white">YYY-MM-DD</th>
						<th class="white">Pilot</th>
						<th class="white">Action</th>
						<th class="white">Align</th>
						<th class="white">Loc</th>
						';
		if ($limited_data == 0){
						echo '
						<th class="white">Dist</th>
						<th class="white">Pass</th>
						';
		}				
		echo			'<th class="white">Expires</th>';
		if ($isCoord) {
				echo	'<th class="white">Client</th>';
		}

				//echo	'<th class="white">Note</th>';
				echo '</tr>
				</thead>
				<tbody>';
		

		foreach ($systemActivities as $activity) {
			// show all activity except for Tamayo in Anoikis System
			if (   !(($system == constant("ANOIKISDIV")) and (($activity['Pilot']) == 'Tamayo'))   ){			
				
				// only display aligned, location, etc info on new Sow rows 
				$sowrow = '';
				if ($activity['EntryType'] == 'sower') {								
					$sowrow = $activity;
				}
				switch ($activity['EntryType']) {
					case 'sower':
						$actioncellformat = ' actionSower';
						break;
					case 'tender':
						$actioncellformat = ' actionTender';
						break;
					case 'note':
						$actioncellformat = ' actionTender';
						break;
					case 'agent':
						$actioncellformat = ' actionAgent';
						break;
					default:
						$actioncellformat = '';
				}

				switch ($activity['CacheStatus']) {
					case 'Healthy':
						$actioncellBorderFormat = ' cacheHealthy';
						break;
					case 'Expired':
						$actioncellBorderFormat = ' cacheExpired';
						break;
					case 'Upkeep Required':
						$actioncellBorderFormat = ' cacheUpkeepRequired';
						break;
					default:
						$actioncellBorderFormat = ' cacheNoStatus';
				}

				echo '<tr class="history">';
				$rowdate = $activity['ActivityDate'];
				echo '<td class="text-nowrap">'. Output::getEveDatetimeShort($rowdate) .'</td>';
				$pilotcellformat = ($charname == $activity['Pilot'] ? $actioncellformat : '');
				$p1 = substr($activity['Pilot'], 0, 10);
				$p2 = $p1 == $activity['Pilot'] ? "" : '<span style="font-size: .6em;">...</span>';
				echo '<td class="text-nowrap' . $pilotcellformat . '">'. $activity['Pilot'] .'</td>';
				echo '<td class="text-nowrap' . $actioncellformat . $actioncellBorderFormat .'">'. ucfirst($activity['EntryType']) .'</td>';
				$rowAW = (!empty($sowrow)) ? $sowrow['AlignedWith'] : '';
				echo '<td class="text-nowrap">'. $rowAW .'</td>';
				$rowLoc = (!empty($sowrow)) ? $sowrow['Location'] : '';
				echo '<td class="text-nowrap">'. $rowLoc .'</td>';
				
				if ($limited_data == 0) {
					$rowDist = (!empty($sowrow)) ? $sowrow['Distance'] : '';			
					echo '<td class="text-nowrap">'. $rowDist.'</td>';

					$rowPass = (!empty($sowrow)) ? $sowrow['Password'] : '';
					echo '<td class="text-nowrap">'. Output::htmlEncodeString($rowPass) .'</td>';
				}
				
				$rowExp = (!empty($sowrow)) ? Output::getEveDate($sowrow['ExpiresOn']) : '';
				echo '<td class="text-nowrap" >'. $rowExp.'</td>';
				
				if ($isCoord) {
					$p1 = substr(Output::htmlEncodeString($activity['AidedPilot']), 0, 10);
					$p2 = $p1 == Output::htmlEncodeString($activity['AidedPilot']) ? '' : '<span style="font-size: .6em;">...</span>';
					echo '<td class="text-nowrap" >'. Output::htmlEncodeString($activity['AidedPilot']) .'</td>';
				}
				if ($activity['Note'] !== ""){
					echo '</tr>';
					echo '<tr><td colspan="2" style="border-top:none;">&nbsp;</td>';
					echo '<td colspan="10" class="' . $actioncellBorderFormat . '" style="border-top: none; text-align: right;">';
					echo 'NOTE: <em>'. Output::htmlEncodeString($activity['Note']) .'</em></td>';
				}
				
				
				echo '</tr>';
			}
		}
		echo '</tbody>
			</table>';
		echo '</div></div></div>';
	}
	
	// include modals for modifying current cache		
	if ($pilotLocStat == '') {			
		include 'modal_tend.php';
	}		

	if ($isCoord || $isRecentSower) {
		include 'modal_edit.php';
	}
	
	include 'modal_newnote.php';
	
	if ($is911){
		include 'modal_agent.php';		
	}
}
// no system selected, so show summary stats
else {
	include_once 'stats_esrc.php';
} 
//if (isset($system))?>
</div>

<!-- MODAL includes with or without current system-->
<?php
if ($limited_data === 0) {	
	include 'modal_sow.php';
}
?>

<script type="text/javascript">
		
		
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}
	
</script>

</body>
</html>
