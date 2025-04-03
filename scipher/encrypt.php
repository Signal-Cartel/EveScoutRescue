<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
session_start();
//make sure they are logged into copilot	
if (!isset($_SESSION['auth_copilot'])){
		header('Location: login.php');
		exit;
}
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

require_once 'scipher.php';

//$path = preg_replace('/\/htdocs\/.*$/', '', $_SERVER['REDIRECT_DOCUMENT_ROOT']);
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/class/db.class.php');


$db = new Database();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SCIPHER - Signal Cartel Intelligence Protection Heuristic Encryption Routine</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
 	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
 	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
	<script src="../js/typeahead.js"></script>
	
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="pikaday.css">
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
		
    	if (isset($_POST['signature']) && $_POST['signature'] == 'yes' && isset($_SESSION['auth_charactername'])) {
    			$playerName = $_SESSION['auth_charactername'];
    	} 
    	else {
    		$playerName = 'Anonymous';
    	}
    			
    	if (isset($_POST['shipid']) && $_POST['shipid'] == 'yes' && isset($_SESSION['auth_character_ship_item_id'])) {
    		$shipid = $_SESSION['auth_character_ship_item_id'];
    	} 
    	else {
    		$shipid = -1;
    	}
    				
    	if (isset($_POST['plaintext']) && strlen($_POST['plaintext']) > 20) {
    		$plaintext = $_POST['plaintext'];
    	} 
    	else {
    		$error = true;
    	}
    		
    	// EXPIRY DATE
    	// create Unix timestamp from date selected
    	if (isset($_POST['expiry_date']) && strlen($_POST['expiry_date']) > 0) {
    		$arrDate = explode('-', $_POST['expiry_date']);
    		$expYear = intval(substr($arrDate[0], -3)) + 1898;
    		$expMonth = intval(date('m', strtotime($arrDate[1])));
    		$expDay = intval($arrDate[2]);
    		$expDate = mktime(0, 0, 0, $expMonth, $expDay, $expYear);
    		$expiry_time = $expDate;
    	} 
    	// if no date selected, set expiration to ten years from now
    	else {
    		$expiry_time = sprintf("%d", time() + 86400*365*10);
    	}
    			
    	$target = array();
    	
    	// lookup target character ID, if needed
    	if (isset($_POST['person']) && strlen($_POST['person']) > 0) {
    		// do db lookup for ID 
    		$db->query('SELECT characterid FROM user WHERE character_name = :person');
    		$db->bind (':person', $_POST['person']);
    		$result = $db->single();
    		$db->closeQuery();
    		// set the target param
    		$target['PER'] = $result['characterid'];
    	}
    	
    	// lookup target ship type ID, if needed
    	if (isset($_POST['shiptype']) && strlen($_POST['shiptype']) > 0) {
    		// do db lookup for ID
    		$db->query('SELECT ShipID FROM shiptypeids WHERE ShipType = :shiptype');
    		$db->bind (':shiptype', $_POST['shiptype']);
    		$result = $db->single();
    		$db->closeQuery();
    		// set the target param
    		$target['SHT'] = $result['ShipID'];
    	}
    	
    	// set target ship ID, if needed
    	if ($shipid != -1) {
    		$target['SHI'] = $shipid;
    	}
    	
    	// lookup target system ID, if needed
    	if (isset($_POST['system']) && strlen($_POST['system']) > 0) {
    		// do db lookup for ID
    		$db->query('SELECT solarSystemID FROM mapSolarSystems WHERE solarSystemName = :system');
    		$db->bind (':system', $_POST['system']);
    		$result = $db->single();
    		$db->closeQuery();
    		// set the target param
    		$target['SYS'] = $result['solarSystemID'];
    	}
    	
    	// lookup target station ID, if needed
    	if (isset($_POST['station']) && strlen($_POST['station']) > 0) {
    		// do db lookup for ID
    		$db->query('SELECT stationID FROM stations WHERE stationName = :station');
    		$db->bind (':station', $_POST['station']);
    		$result = $db->single();
    		$db->closeQuery();
    		// set the target param
    		$target['STA'] = $result['stationID'];
    	}
    	
    	foreach (SCIPHER_TARGET as $key) {
    		if (isset($_POST[$key])) {
    			if (isset(SCIPHER_TARGET_ISNUMERIC[$key])) {
    				if (is_numeric($_POST[$key]) && $_POST[$key] > 0) {
    					$target[$key] = $_POST[$key];
    				}
    			} 
    			elseif (strlen($_POST[$key]) > 0) {
    				$target[$key] = $_POST[$key];
    			}
    		}
    	}
    	
    	echo "<pre>";
    					
    	if (!$error) {
    		$ciphertext = scipher_encrypt($playerName, $plaintext, $expiry_time, $target);
    		print "Encrypted message\n\n";
    		print "$ciphertext\n";
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
    
    // display encrypt form
    else {
    	?>
      <form class="form-horizontal" method="POST" action="encrypt.php">
        <h4>ENCRYPT<br />
			Target Selection
			&nbsp;
			<i class="white fa fa-question-circle" style="vertical-align: bottom;" data-toggle="tooltip" data-placement="right" 
				title="At least one of the target parameters below (A-F) must be specified">
			</i>
		</h4>
		<p>
			<label for="person">(A) Name:</label>
			<input type="text" name="person" id="person" autocomplete="off" style="width:100%;" 
				placeholder="Enter Signaleer Name">
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
				title="Limit decoding to only this particular individual; 
				leave blank if anyone should be able to decode your message"></i>
			<br/>
			<label for="shiptype">(B) Hull Type:</label>
			<input type="text" name="shiptype" id="shiptype" autocomplete="off" 
				placeholder="Enter Hull Type">
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
			title="Limit decoding to only someone aboard the type of ship specified">
			</i>
			<br/>
			<label for="shipid">(C) Ship ID:</label>
			<select name="shipid">
			  <option value="yes">Your current ship</option>
			  <option value="no" selected>Any ship</option>
			</select>
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
				title="Limit decoding to only someone aboard the ship you are currently aboard; otherwise, leave set to
				Any Ship">
			</i>
			<br/>
			<label for="system">(D) System:</label>
			<input type="text" name="system" id="system" autocomplete="off" 
				placeholder="Enter System Name">
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
				title="Limit decoding to only someone in the system specified; 
				leave blank if message can be decoded from anywhere">
			</i>
			<br/>
			<label for="station">(E) Station:</label>
			<input type="text" name="station" id="station" autocomplete="off" 
				placeholder="Enter Station Name">
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
				title="Limit decoding to only someone in station specified; 
				leave blank if message can be decoded from anywhere">
			</i>
			<br/>
			<label for="PAS">(F) Password:</label>
			<input type="text" name="PAS" 
				placeholder="minimum 5 characters">
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
				title="Limit decoding to only someone in possession of the  
				correct passphrase; leave blank if message can be decoded without needing a passphrase">
			</i>
			<br/>
		</p>
        <hr/>
		<p>
			<label for="plaintext" style="vertical-align: top;">Message body:</label>
			<textarea rows="10" cols="40" name="plaintext" placeholder="minimum 20 characters"></textarea>
			<br/>
			<label for="signature">Signature:</label>
			<select name="signature">
			  <option value="yes" selected>Sign the message</option>
			  <option value="no">Anonymous message</option>
			</select>
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
				title="Sign the message with your name, or make the message anonymous"></i>
			<br/>
			<label for="expiry_date">Expiry Date:</label>
			<input type="text" id="expiry_date" name="expiry_date" autocomplete="off" 
				placeholder="Click to select date">
			&nbsp;
			<i class="white fa fa-question-circle" data-toggle="tooltip" data-placement="right" 
				title="Date on which message expires and will no longer be
				able to be decoded; leave blank if message should not expire">
			</i>
			<br/>
			<br/>
			<input class="raised-btn" type="submit" value="Encrypt now">
			
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
	print_r($_POST);
  	echo '</pre>';
*/
?>
  </body>
  
  <script src="moment.min.js"></script>
  <!-- https://dbushell.com/Pikaday/ -->
  <script src="pikaday.js?v=<?=filemtime('pikaday.js')?>"></script>
  <script>
	$(document).ready(function() {
		// typeaheads
        $('input#person').typeahead({
            name: 'person',
            remote: 'fetch.php?type=person&query=%QUERY'
        });
        $('input#shiptype').typeahead({
            name: 'shiptype',
            remote: 'fetch.php?type=shiptype&query=%QUERY'
        });
        $('input#system').typeahead({
            name: 'system',
            remote: 'fetch.php?type=system&query=%QUERY'
        });
        $('input#station').typeahead({
            name: 'station',
            remote: 'fetch.php?type=station&query=%QUERY'
        });
    })
    
    // datepicker
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
	var tomorrow = new Date();
	tomorrow.setDate(tomorrow.getDate() + 1);
	
    var picker = new Pikaday(
    {
        field: document.getElementById('expiry_date'),
        minDate: tomorrow,
        showMonthAfterYear: true,
        format: 'YYYY-MMM-DD',
        toString(date, format) {
            const day = ("0" + date.getDate()).slice(-2);
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear() - 1898;
            return `YC${year}-${month}-${day}`;
        }
    });

	// initialize tooltip display
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip({container: 'body'}); 
	});
  </script>
  
</html>