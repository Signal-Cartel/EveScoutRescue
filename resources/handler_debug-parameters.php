<?php
// page cannot be accessed directly
if (!defined('ESRC')) { die ('Direct access not permitted'); }


// report all errors
error_reporting(E_ALL);

// Display errors on dev for debug
if (strpos($_SERVER['HTTP_HOST'],'dev.evescoutrescue') > -1 || 
    strpos($_SERVER['HTTP_HOST'],'localhost') > -1) {
	//chown("../../logs/php-error_dev.log", "apache");	// set log owner to "apache" user
	ini_set('display_errors', 'on');
	ini_set('log_errors', 'on');
	ini_set("error_log", "../../logs/php-error_dev.log");
	//error_log("test error log");
}
// but hide errors on production and log them instead
else {
	//chown("../../logs/php-error.log", "apache");	// set log owner to "apache" user
	ini_set('display_errors', 'off');
	ini_set('log_errors', 'on');
	ini_set("error_log", "../../logs/php-error.log");
	//error_log("test error log");
}
?>