<?php
// page cannot be accessed directly
if (!defined('ESRC')) { die ('Direct access not permitted'); }


// get current runtime environment
switch ($_SERVER['HTTP_HOST']) {
	case 'dev.evescoutrescue.com':	// staging
		if (isset($_SESSION['livedata']) and  $_SESSION['livedata'] == 1){
			$configPath = '../../config/esrc_defaults.ini';
		}
		else{
			$configPath = '../../config/esrc_defaults_dev.ini';
		}
		break;
	
	case 'evescoutrescue.com':	// production
		$configPath = '../../config/esrc_defaults.ini';
		break;

	default:	// usually localhost dev
		$configPath = '../../conf/esrc_defaults_localhost.ini';

}


// find config file
$sysconfig = parse_ini_file($configPath);

// check if properties are loaded
if ($sysconfig === false) {
	echo "<p><b>No system config found!</b></p>";
	exit(1);
}

// define constants
define("ANOIKISDIV", $sysconfig['anoikisDiv']);
define("ROOTPATH", $sysconfig['rootPath']);
define("AUTHCALLBACK", $sysconfig['authCallback']);
define("AUTHCLIENTID", $sysconfig['clientID']);
define("AUTHSECRET", $sysconfig['authSecret']);
define("USERAGENT", $sysconfig['userAgent']);
define("DEVSYSTEM", $sysconfig['devSystem']);
define("DISCORDCOORD", $sysconfig['discordCoordChannel']);
define("DISCORD_EXPLO", $sysconfig['discordExploChannel']);
define("ES_API_URI", $sysconfig['eveScoutApiBaseUri']);
define("ES_API_SECRET", $sysconfig['eveScoutApiSecret']);


class Config
{
	const ROOT_PATH = ROOTPATH;
	const AUTH_CALLBACK = AUTHCALLBACK;
	const AUTH_CLIENT_ID = AUTHCLIENTID;
	const AUTH_SECRET = AUTHSECRET;
	const USER_AGENT = USERAGENT;
	const DEV_SYSTEM = DEVSYSTEM;
	const DISCORD_SAR_COORD_TOKEN = DISCORDCOORD;
	const DISCORDEXPLO = DISCORD_EXPLO;
  const ES_API_URI = ES_API_URI;
  const ES_API_SECRET = ES_API_SECRET;
}