<?php
session_start();

//include this at the top of all public pages that do not require user to be logged in to access
if (isset($_SESSION['auth_characterid'])) {
	$charimg =  '<img src="http://image.eveonline.com/Character/'.
				$_SESSION['auth_characterid'].'_64.jpg">';
	$charname = $_SESSION['auth_charactername'];
	$chardiv =  '<div style="text-align: center;">'.$charimg.'<br />' .
				'<div><span class="white">' .$charname. '</span><br />' .
				'<span class="descr"><a href="../auth/logout.php">logout</a></span>' .
				'</div></div>';
}
else {
	$chardiv =  '<a href="../auth/login.php">'.
				'<img src="../img/EVE_SSO_Login_Buttons_Small_Black.png"></a>';
}
?>