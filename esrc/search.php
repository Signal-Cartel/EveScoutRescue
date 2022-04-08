<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';
require_once '../class/password.class.php';
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/caches.class.php';
require_once '../class/systems.class.php';
require_once '../class/output.class.php';
require_once '../class/rescue.class.php';

// set password to use for new cache
// if "pass" parameter is passed in, use that for password instead
$cachepass = (isset($_REQUEST['pass']) ? $_REQUEST['pass'] : Password::generatePassword());

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
$caches = new Caches($database);
$systems = new Systems($database);
$rescue = new Rescue($database);
$leaderBoard = new Leaderboard($database);

// check for SAR Coordinator login
if (!isset($_SESSION['isCoord'])){
	$isCoord = $_SESSION['isCoord'] = ($users->isSARCoordinator($charname) || $users->isAdmin($charname));	
}
else{
	$isCoord = $_SESSION['isCoord'];
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

// get active SAR requests of current system
$data = $rescue->getSystemRequests($system, 0, $isCoord);

$activeSAR = $activeSARtitle = '';
// check for active SAR request
if (count($data) > 0) {
	$activeSAR = ' <span style="font-weight: bold; color: red;">(!)</span>';
	$activeSARtitle = '&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #ff6464;"> ACTIVE SAR SYSTEM!</span>';
}

// CONFIRM PILOT'S IN-GAME LOCATION
$pilotLocStat = '';
// does not apply to SAR Coordinators
if ($isCoord == false  && (Config::DEV_SYSTEM != 1)) {
	// check for Allison login (required to sow/tend caches)
	if (isset($_SESSION['auth_char_location'])) {
		// check if pilot has sown/tended over 300 caches; if so, they are excluded from this check
		// This is awfully inefficient - ADP
		if (!isset($_SESSION['megacacher'])){
			$daysdiff = round((strtotime("+1 day")- strtotime("2017-03-01")) / (60 * 60 * 24));
			$rows = $leaderBoard->getTop(2000, $daysdiff);
			$bitPilotMatch = 0;
			foreach ($rows as $value) {
				if ($charname ==  $value['Pilot']) {
					if ($value['cnt'] >= 300) {
						$bitPilotMatch = 1;
						break;
					}
				}
			}
			$_SESSION['megacacher'] = $bitPilotMatch;
		}
		
		if ($_SESSION['megacacher'] == 0) {
			// otherwise, pilot may only sow/tend caches for a system they are verified to be present in
			if (($_SESSION['auth_char_location'] != $system) && ($_SESSION['prior_system'] != $system)) {
				$pilotLocStat = 'not_in_system';
				$strBtnAttrib = 'data-toggle="tooltip" title="You must be in or one jump out from '.
					$system .' in order to perform this action, but you are in '.
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


// check if a system is supplied
if (!empty($system)) {
	// display result for the selected system
	// get cache information from database
	$row = $caches->getCacheInfo($system);
	$strNotes = '';
	//only display the following if we got some results back
	if (!empty($row)) {
		// calculate status cell format
		$statuscellformat = '';
		switch ($row ['Status']) {
			case 'Healthy':
				$statuscellformat = ' style="color: #32ff32;"';//green
				break;
			case 'Upkeep Required':
				$statuscellformat = ' style="color:gold;"';
				break;
			case 'Locals Tend':
				$statuscellformat = ' style="color:#ff6464;"';// red
				break;
			default:
				$statuscellformat = '';
		}
		
		// save notes as separate var
		$strNotes = ($row['Note']);
		// include modals for modifying current cache
			include 'modal_agent.php';
			include 'modal_tend.php';
			include 'modal_edit.php';
			include 'modal_newnote.php';
		?>
		
		
		<!-- action button row -->
		<div class="row" id="systableheader">
			<div class="col-md-12">
				<div style="padding-left: 10px;">
					<!-- System Name display -->
					<p class="systemName"><?=$system . "<span $statuscellformat> " . $row ['Status'] . "</span>" . $activeSARtitle ?></p>
					<!-- TEND button -->
					<?php
					$strTended = '';
					if (0 == $caches->isTendingAllowed($system)) {
						$strTended = ' <i class="white fa fa-clock-o"></i>';
					}

					//check pilot status
					if ($pilotLocStat == '') {
						$strBtnAttrib = 'data-toggle="modal" data-target="#TendModal"';
					}?>
					<button type="button" class="btn btn-primary" role="button" <?=$strBtnAttrib?>>
						Tend<?=$strTended?></button>
					
					<!-- AGENT button -->
					<button type="button" class="btn btn-warning" role="button" data-toggle="modal"
						data-target="#AgentModal">Agent</button>
					
					<!-- SAR New button -->
					<a href="rescueoverview.php?new=1&sys=<?=$system?>" class="btn btn-danger"
						role="button">New SAR</a>
					
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
					$isRecentSower = $caches->isRecentSower($charname, $row['CacheID']);
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
							<th>Distance</th>
							<th>Password</th>
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
					<td><?=Output::getEveDate($row['InitialSeedDate'])?></td>
					<td><?=$row['AlignedWith']?></td>
					<td><?=$row['Location']?></td>
					<td><?=Output::htmlEncodeString($row['Distance'])?></td>
					<td><input type="text" id="cachepass1" style="width:100px;"
							value="<?=Output::htmlEncodeString($row['Password'])?>" readonly />
							<i id="copyclip" class="fa fa-clipboard"
								onClick="SelectAllCopy('cachepass1')"></i>
					</td>
					
					<?php
					// probes and filament status
					
					$haspas = ($row['has_pas'] == 1 ? 'Yes' : 'No');
					$statpas = ($row['has_pas'] == 1 ? ' style="color:rgba(90, 230, 90, .8);"' : ' style="color:rgba(234, 10, 10, .9);"');
					$hasfil = ($row['has_fil'] == 1 ? 'Yes' : 'No');
					$statfil = ($row['has_fil'] == 1 ? ' style="color:rgba(90, 230, 90, .8);"' : ' style="color:rgba(234, 10, 10, .9);"');
					?>
					<!--
					<td<?=$statpas ?>><?=$haspas?></td>
					<td<?=$statfil ?>><?=$hasfil?></td>
					-->
					<td><?=Output::getEveDate($row['ExpiresOn'])?></td>

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
						if ($pilotLocStat == '') {
							$strBtnAttrib = 'data-toggle="modal" data-target="#SowModal"';
						} ?>
						<button type="button" class="btn btn-success" role="button" <?=$strBtnAttrib?>>
							Sow New Cache</button>&nbsp;&nbsp;&nbsp;
														
						<!-- SAR New button -->
						<a href="rescueoverview.php?new=1&sys=<?=$system?>" class="btn btn-danger"
							role="button">New SAR</a>&nbsp;&nbsp;&nbsp;
						
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
			if ((true or $isCoord) and !empty($row)) {
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
	if (!empty($systemActivities)) {
		echo '<div class="row" id="historytable">';
		echo '<div class="col-md-12">';
		echo '<div style="padding-left: 0px;">';
		echo '<br /><span class="subhead">HISTORY</span><br />';
		echo '<table class="table" style="
				font-size: 1em;
				font-weight: normal;
				color: #a7a7a7;
				">
				<thead>
					<tr>
						<th class="white"> </th>
						<th class="white">Pilot</th>
						<th class="white">Type</th>
						<th class="white">Align</th>
						<th class="white">Location</th>
						<th class="white">Distance</th>
						<th class="white">Password</th>
						<th class="white">Expires</th>';
		if ($isCoord) {
				echo	'<th class="white">Aided&nbsp;Pilot</th>';
				}

				echo	'<th class="white">Note</th>
					</tr>
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
				echo '<td class="text-nowrap">'. Output::getEveDatetime($rowdate) .'</td>';
				$pilotcellformat = ($charname == $activity['Pilot'] ? $actioncellformat : '');
				echo '<td class="text-nowrap' . $pilotcellformat . '">'. $activity['Pilot'] .'</td>';
				echo '<td class="text-nowrap' . $actioncellformat . $actioncellBorderFormat .'">'. ucfirst($activity['EntryType']) .'</td>';
				$rowAW = (!empty($sowrow)) ? $sowrow['AlignedWith'] : '';
				echo '<td class="text-nowrap">'. $rowAW .'</td>';
				$rowLoc = (!empty($sowrow)) ? $sowrow['Location'] : '';
				echo '<td class="text-nowrap">'. $rowLoc .'</td>';
				$rowDist = (!empty($sowrow)) ? $sowrow['Distance'] : '';
				echo '<td class="text-nowrap">'. $rowDist.'</td>';

				$rowPass = (!empty($sowrow)) ? $sowrow['Password'] : '';
				echo '<td class="text-nowrap">'. Output::htmlEncodeString($rowPass) .'</td>';
				
				
				$rowExp = (!empty($sowrow)) ? Output::getEveDate($sowrow['ExpiresOn']) : '';
				echo '<td class="text-nowrap">'. $rowExp.'</td>';
				
				if ($isCoord) {
					echo '<td class="text-nowrap">'. Output::htmlEncodeString($activity['AidedPilot']) .'</td>';
				}
				echo '<td style="font-size: 1em;">'. Output::htmlEncodeString($activity['Note']) .'</td>';
				echo '</tr>';
			}
		}
		echo '</tbody>
			</table>';
		echo '</div></div></div>';
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
	include 'modal_sow.php';
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
