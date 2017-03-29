<?php
session_start();
if (isset($_SESSION['auth_characterid'])) {
	$charimg = '<img src="http://image.eveonline.com/Character/'.$_SESSION['auth_characterid'].'_64.jpg">';
	$charname = $_SESSION['auth_charactername'];
	$chardiv = '<div style="text-align: center;">'.$charimg.'<br /><div style="background-color: black;"><span class="white">'.$charname.'</span><br /><span class="descr"><a href="/auth/logout.php">logout</a></span></div></div>';
}
else {
	$chardiv = '<a href="../auth/login.php"><img src="../img/EVE_SSO_Login_Buttons_Small_Black.png"></a>';
}

$bg = array('bg01.jpg', 'bg02.jpg', 'bg03.jpg', 'bg04.jpg', 'bg05.jpg', 'bg06.jpg'); // array of filenames
$i = rand(0, count($bg)-1); // generate random number size of the array
if (!isset($_SESSION['selectedBg'])) {
	$_SESSION['selectedBg'] = "$bg[$i]"; // set variable equal to which random filename was chosen
}
?>