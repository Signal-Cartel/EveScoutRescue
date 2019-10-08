<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
if (!defined('ESRC')) define('ESRC', TRUE);

require_once '../class/db.class.php';
require_once '../class/rescue_mail.class.php';
require_once '../class/users.class.php';
require_once '../class/rescue.class.php';
require_once '../class/systems.class.php';
require_once '../class/output.class.php';
include_once '../class/config.class.php';

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

$charname = $_SESSION['auth_charactername'];
// check if a valid character name is set
if (!isset($charname)) {
	// no, set a dummy char name
	$charname = 'EvE-Scout Rescue Coordinator Team';
}

// prepare DB object
$database = new Database();
$users = new Users($database);

// check for SAR Coordinator login
$isCoord = ($users->isSARCoordinator($charname) || $users->isAdmin($charname));
if (!$isCoord) {
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH);
	// stop processing
	exit;
}

// get rescue id for query
$reqID = (isset($_REQUEST['req'])) ? $_REQUEST['req'] : '';
// get mail type - SAR or ESRC
$mail_type = (isset($_REQUEST['typ'])) ? $_REQUEST['typ'] : '';

/* get all rescue information */
$rescue = new Rescue($database);
$systems = new Systems($database);
$request = $rescue->getRequest($reqID);

const EVEMAIL_DELIMITER = ';';

class RescueSuccess {

	public function generateTenderThanksMail($systemName, $sowPilot, $cacheTenders, $coordinator, $activitySummary)
	{
		$subject = "Successful Rescue in " . $systemName;
		$toList = $sowPilot . EVEMAIL_DELIMITER . $cacheTenders;

		$bodyText = "Greetings Signaleers (past and present)!

Multiple times a week pilots across New Eden are saved by rescue caches sown and tended by devoted Signaleers like yourselves.
Often these rescues are accomplished so quickly that few people even know they happened.

Recently a pilot was rescued using a cache in $systemName, sown by $sowPilot, which all of you contributed to keeping alive:

$activitySummary
Thank you for your dedication to the ESRC program. Your hard work has paid off!

Yours in service,

$coordinator,
EvE-Scout Rescue
https://evescoutrescue.com/
";

		return new RescueMail($toList, $subject, $bodyText);
	}

	public function generateRescueFollowupMail($systemName, $rescuedPilot, $coordinator,$mail_type)
	{
		$subject = "Successful Rescue from " . $systemName;
		
// MAIL BODY FOR SAR FOLLOWUP
if ($mail_type == 'sar') {	
	
$bodyText = "Hello there,

My name is $coordinator, and I am a Rescue Coordinator for the EvE-Scout Search and Rescue (SAR) Program.

I recently learned of your rescue from $systemName. Congratulations on making it out! I am very happy to know that our Rescue Pilots were able to assist you in getting out of J-Space.

I am reaching out to you now just to follow up on your experience and see if there was anything that you would like to share regarding it. Also, assuming that you were satisfied with the service that you received, I was wondering if you would consider writing a short testimonial that we might use upon our webpage? Of course you are under no obligation to do so, and if you wished you could also do so anonymously.

You can submit your testimonial at https://evescoutrescue.com/home/testimonial_submit.php or feel free to send me an evemail with any thoughts or concerns.

Again, I am glad to know that you made it out. Feel free to mention us to your friends, and do not hesitate to reach out to us again at any time, even if just to say \"hi\". Just go to the EvE-Scout channel in game. There is always someone there.

Yours in service,

$coordinator
EvE-Scout Rescue
https://evescoutrescue.com/
";
}
else{
// MAIL BODY FOR ESRC FOLLOWUP
$bodyText = "Hello there,

My name is $coordinator, and I am a Rescue Coordinator for the EvE-Scout Search and Rescue (SAR) Program.

I recently learned of your rescue from $systemName. Congratulations on making it out! I am very happy to know that our team was able to assist you in getting out of J-Space.

I am reaching out to you now just to follow up on your experience and see if there was anything that you would like to share regarding it. Also, assuming that you were satisfied with the service that you received, I was wondering if you would consider writing a short testimonial that we might use upon our webpage? Of course you are under no obligation to do so, and if you wished you could also do so anonymously.

You can submit your testimonial at https://evescoutrescue.com/home/testimonial_submit.php or feel free to send me an evemail with any thoughts or concerns.

Again, I am glad to know that you made it out. Feel free to mention us to your friends, and do not hesitate to reach out to us again at any time, even if just to say \"hi\". Just go to the EvE-Scout channel in game. There is always someone there.

Yours in service,

$coordinator
EvE-Scout Rescue
https://evescoutrescue.com/
";	
}
			return new RescueMail($rescuedPilot, $subject, $bodyText);
		}
	}

	$rescueSuccess = new RescueSuccess();

	$rescueDate = Output::getEveDate($request['requestdate']);
	$systemName = Output::htmlEncodeString($request['system']);
	$rescuedPilot = Output::htmlEncodeString($request['pilot']);
	//$coordinator = ($request['closeagent']) ? Output::htmlEncodeString($request['closeagent']) : "EvE-Scout Rescue Coordinator Team";
	$coordinator = $charname;

	$cacheParticipants = $systems->getActiveCacheParticipants($systemName);
	$activitySummary = "";

	if (empty($cacheParticipants)) {
		echo "Error: Nothing found for system " . $request['system'];

	} else {

		foreach ($cacheParticipants as $activity) {
			if (empty($sowPilot)) {
				$sowPilot = Output::htmlEncodeString($activity['Pilot']);
			} elseif (empty($cacheTenders)) {
				$cacheTenders = Output::htmlEncodeString($activity['Pilot']);
			} else {
				$cacheTenders .= EVEMAIL_DELIMITER . Output::htmlEncodeString($activity['Pilot']);
			}

			$activitySummary .= Output::htmlEncodeString($activity['LastActivity'] . " - " . $activity['EntryType'] . " - " . $activity['Pilot'] . "\r");
		}

		if (empty($cacheTenders)) {
			$cacheTenders = "";
		}

		$tenderThanksMail = $rescueSuccess->generateTenderThanksMail($systemName, $sowPilot, $cacheTenders, $coordinator, $activitySummary);
		$rescueFollowupMail = $rescueSuccess->generateRescueFollowupMail($systemName, $rescuedPilot, $coordinator,$mail_type);
		
		
	}

	?>

<html>

	<head>
		<?php $pgtitle = 'ESRC Search'; include_once '../includes/head.php'; ?>
	</head>

	<body >
		<div class="rescueMail">
			<h3 class="rescueMailTitle"><?=$rescueDate?> - Rescue Success Mails for system - <?=$systemName?></h3>

			<div class="grid-container">
				<h3 class="rescueMailSubtitle">Rescued Pilot(s):</h3>
				<input class="addresses" value="<?=$rescueFollowupMail->getAddressees()?>"/>
				<h5 class="rescueMailSubtitle">Subject:</h5>
				<input class="subject" value="<?=$rescueFollowupMail->getSubject()?>"/>
				<h5 class="rescueMailSubtitle">Body:</h5>
				<textarea id="rescueFollowupMail" class="eveMailBody"><?=$rescueFollowupMail->getBody()?></textarea>
			</div>
			
			<div class="grid-container">
				<h3 class="rescueMailSubtitle">Signaleers:</h3>
				<input class="addresses" value="<?=$tenderThanksMail->getAddressees()?>"/>
				<h5 class="rescueMailSubtitle">Subject:</h5>
				<input class="subject" value="<?=$tenderThanksMail->getSubject()?>"/>
				<h5 class="rescueMailSubtitle">Body:</h5>
				<textarea id="tenderThanksMail" class="eveMailBody"><?=$tenderThanksMail->getBody()?></textarea>
			</div>



			<p>Please review and edit messages. Then click to send all mail:
				<button class="btn" onclick="alert('hiya, I dont do anything yet! :D')">Send Rescue Success EveMails</button></p>
			</div>

	</body>
</html>
