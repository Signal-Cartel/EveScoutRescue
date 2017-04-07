	<meta http-equiv="Content-Language" content="en-us">
	<title><?php echo $pgtitle; ?> :: EvE-Scout Rescue</title>
	<meta charset="utf-8">
	<link href="../css/main.css" rel="stylesheet">
	<!-- Latest compiled and minified Bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="../js/typeahead.js"></script>
    <!-- All background images courtesy of EvE-Scout Observatory: 
    		- http://observatory.eve-scout.com/
    		- https://www.flickr.com/photos/eve-scout/ -->
    <?php
	//load a different bg image on each new session
	$bg = array('bg01.jpg', 'bg02.jpg', 'bg03.jpg', 'bg04.jpg', 'bg05.jpg', 'bg06.jpg');
	$i = rand(0, count($bg)-1);
	if (!isset($_SESSION['selectedBg'])) {
		$_SESSION['selectedBg'] = "$bg[$i]";
	}
	?>
    <style type="text/css">
	<!--
		body {
			background: url(../img/<?php echo $_SESSION['selectedBg']; ?>) no-repeat;
			background-attachment: fixed;
		}
	-->
	</style>