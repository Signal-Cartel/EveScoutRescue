<?php
session_start();

//include this at the top of all pages private to alliance admins only

//disable auth routines if we are on localhost
if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
	//user is logged in
	if (isset($_SESSION['auth_characterid'])) {
		$charname = $_SESSION['auth_charactername'];
		$admins = array('Thrice Hapus','Mynxee','Johnny Splunk');
		//if logged in user is not an admin, redirect them back to the home page
		if (array_search($charname, $admins) === false) {
			header("Location: /");
		}
		$charimg =  '<img src="http://image.eveonline.com/Character/' .$_SESSION['auth_characterid']. '_64.jpg">';
		$chardiv =  '<div style="text-align: center;">' .$charimg. '<br />' .
					'<div style="background-color: black;">' .
					'<span class="white">' .$charname. '</span><br />' .
					'<span class="descr"><a href="../auth/logout.php">logout</a></span>'.
					'</div></div>';
	}
	//user is not logged in and must be to access this page,
	//so set this as the return page and redirect to login
	else {
		$_SESSION['auth_redirect'] = 'http://evescoutrescue.com'.htmlentities($_SERVER['PHP_SELF']);
		header("Location: ../auth/login.php");
	}
}
?>