<?php
// All images courtesy of EvE-Scout Observatory: http://observatory.eve-scout.com/
//load a different bg image on each new session
$bg = array('bg01.jpg', 'bg02.jpg', 'bg03.jpg', 'bg04.jpg', 'bg05.jpg', 'bg06.jpg');
$i = rand(0, count($bg)-1);
if (!isset($_SESSION['selectedBg'])) {
	$_SESSION['selectedBg'] = "$bg[$i]";
}
?>