<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');
session_start();
//make sure they are logged into copilot	
if (!isset($_SESSION['auth_copilot'])){
		exit;
}
require_once('./scipher.php');
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
  
    <?php 
    // process form submit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    	
    	$error = false;
    	
    	if (isset($_POST['ciphertext']) && strlen($_POST['ciphertext']) > 20) {
    		$ciphertext = $_POST['ciphertext'];
    	} 
    	else {
    		$error = true;
    	}
    	
    	$target = array();
    	if (isset($_POST['PAS'])) {
    		$target['PAS'] = $_POST['PAS'];
    	}
    	
    	// fill the target filter with all our values
    	foreach (SCIPHER_SESSION_MAP as $key => $skey) {
    		if (isset($_SESSION[$skey])) {
    			if (isset(SCIPHER_TARGET_ISNUMERIC[$key])) {
    				if (is_numeric($_SESSION[$skey])) {
    					$target[$key] = $_SESSION[$skey];
    				}
    			} 
    			else {
    				$target[$key] = $_SESSION[$skey];
    			}
    		}
    	}
    	
    	echo "<pre>";
    	
    	if (!$error) {
    		$plaintext = scipher_decrypt($ciphertext, $target);
    		
    		print "Decrypted message\n";
    		print "$plaintext\n";
    		print "ERROR: $scipherError\n";
			echo "</pre>";
    	} 
    	else {
    		print "ERROR: Bad parameters, nothing to process.\n";
			echo "</pre>";
    	}
    	echo '
		<div class="stamped">
			<form class="form-horizontal" method="GET" action="encrypt.php">
			<input class="raised-btn" type="submit" value="Encryption">
			</form>
			<form class="form-horizontal" method="GET" action="decrypt.php">
			<input class="raised-btn" type="submit" value="Decryption">
			</form>
		</div>
		';
    }
    
    // display decrypt form
    else {
    	?>
      <form class="form-horizontal" method="POST" action="decrypt.php">
        <h4>DECRYPT: Target Selection</h4>
		<p>
		<label for="PAS">Passphrase:</label>
        <input type="text" name="PAS">
        <hr/>
        <label for="ciphertext">Message body:</label>
        <textarea rows="10" cols="40" name="ciphertext"></textarea><br/>
        <br/>
        <input class="raised-btn" type="submit" value="Decrypt now">
		</p>
      </form>
	  	<form class="form-horizontal" method="GET" action="index.php">
			<input class="raised-btn" type="submit" value="Cancel">
		</form>
	 </div>
    <?php 
    }


/*
	echo '<pre>';
  	print_r($_SESSION);
				 
				  
  	echo '</pre>';
*/
?>
  </body>
</html>