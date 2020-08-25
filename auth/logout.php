<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

require_once '../class/config.class.php';

//direct users to this page when they click the link to log out from their session
session_start();
session_unset();
header("Location: /");  // send to home page on logout
exit;
?>