<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth
require_once '../../config/secret.php';

//direct users to this page when they click the link to log out from their session
session_start();
session_unset();
header("Location: ".$appbase);
?>