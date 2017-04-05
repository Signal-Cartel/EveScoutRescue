<?php
//code adapted from https://github.com/fuzzysteve/eve-sso-auth

function auth_error($error_message)
{
    print "There's been an error";
    error_log($error_message);
    exit();
}
?>