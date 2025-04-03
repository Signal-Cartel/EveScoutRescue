<?php
session_start();
//make sure they are logged into copilot	
if (!isset($_SESSION['auth_copilot'])){
		exit;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SCIPHER - Signal Cartel Intelligence Protection Heuristic Encryption Routine</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
 	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>  
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
	<link rel="stylesheet" href="scipher.css?v=<?=filemtime('scipher.css')?>" />
  </head>
 <body>
    <div class="stamped" style="padding: 0px;">
	<img src="scipher-logo.png" alt="SCIPHER" style="display: block; margin-left: auto; margin-right: auto;"> 
	</div>
    <h2>Signal Cartel Intelligence Protection Heuristic Encryption Routine</h2>
	<div class="stamped">
		<form class="form-horizontal" method="GET" action="encrypt.php">
		<input class="raised-btn" type="submit" value="Encryption">
		</form>
		<form class="form-horizontal" method="GET" action="decrypt.php">
		<input class="raised-btn" type="submit" value="Decryption">
		</form>
	</div>
  </body>
</html>