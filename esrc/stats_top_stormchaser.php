<?php 
// created for the discord bot to reward top Storm Chaser pilot with icon on discord name
define('ESRC', TRUE);

require_once '../class/db.class.php';
require_once '../class/config.class.php';
require_once '../class/storms.class.php';

// initialize objects
$database = new Database();
$storms = new Storms($database);

$data = $storms->getTopStormChaser();
header('Content-type: application/json');
echo(json_encode($data));
