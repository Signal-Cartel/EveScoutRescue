<?php

if (strpos($_SERVER['HTTP_HOST'],'dev') === FALSE) {
	// get production properties
	$sysconfig = parse_ini_file('../../config/esrc_defaults.ini');
}
else {
	// get development properties
	$sysconfig = parse_ini_file('../../config/esrc_defaults_dev.ini');
}

// check if properties are loaded
if ($sysconfig === FALSE)
{
	echo "<p><b>No system config found!</b></p>";
	// add error logging here
	exit(1);
}

// check for enabled maintenance mode in DB
define("ROOTPATH", $sysconfig['rootPath']);
define("AUTHCALLBACK", $sysconfig['authCallback']);
define("AUTHCLIENTID", $sysconfig['clientID']);
define("AUTHSECRET", $sysconfig['authSecret']);
define("USERAGENT", $sysconfig['userAgent']);
define("DEVSYSTEM", $sysconfig['devSystem']);
define("DISCORDCOORD", $sysconfig['discordCoordChannel']);
/**
 * Database connection handling wrapper. It's possible to run one query at a time only.
 */
class Config
{
	const ROOT_PATH = ROOTPATH;
	
	const AUTH_CALLBACK = AUTHCALLBACK;
	
	const AUTH_CLIENT_ID = AUTHCLIENTID;
	
	const AUTH_SECRET = AUTHSECRET;
	
	const USER_AGENT = USERAGENT;
	
	const DEV_SYSTEM = DEVSYSTEM;
	
	const DISCORD_SAR_COORD_TOKEN = DISCORDCOORD;
}