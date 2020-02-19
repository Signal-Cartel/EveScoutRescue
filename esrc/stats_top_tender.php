<?php 

define('ESRC', TRUE);

//include_once '../includes/auth-inc.php';
require_once '../class/db.class.php';
require_once '../class/config.class.php';
require_once '../class/caches.class.php';

// initialize objects
// https://dev.evescoutrescue.com/esrc/stats_top_tender.php

$database = new Database();
$caches = new Caches($database);

$ctrrescues = $caches->getTopTender();

echo(json_encode($ctrrescues));

