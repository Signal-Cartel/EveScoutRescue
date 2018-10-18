	<meta http-equiv="Content-Language" content="en-us">
	<title><?php echo $pgtitle; ?> :: EvE-Scout Rescue</title>
	<meta charset="utf-8">
	<!-- CSS -->
	<link rel="stylesheet" href="../css/main.css?v=<?=filemtime('../css/main.css')?>">
	<link rel="stylesheet" href="../css/sticky-footer.css">
	<link rel="stylesheet" href="../css/datatables_custom.css">
	<link rel="stylesheet" href="../css/pikaday.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- Favicon -->
	<link rel="icon" type="image/png" href="/favicon.png">
    <!-- JS -->
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/typeahead.js"></script>
    <script src="../js/validator.js"></script>
    <script src="../js/moment.min.js"></script>
	<script src="../js/pikaday.js"></script> <!-- https://dbushell.com/Pikaday/ -->
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
			-webkit-background-size: cover;
   			-moz-background-size: cover;
   			-o-background-size: cover;
   			background-size: cover;
		}
		
		#copyclip {
		  position: relative;
		  z-index: 1;
		  left: -25px;
		  top: 1px;
		  color: #7B7B7B;
		  cursor: pointer;
		  width: 0;
		}
	-->
	</style>