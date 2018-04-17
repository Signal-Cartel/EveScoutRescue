<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 
require_once '../class/db.class.php';
require_once '../class/users.class.php';

// check if the user is logged in
if (!isset($_SESSION['auth_characterid'])) {
	// void the session entries on 'attack'
	session_unset();
	// save the redirect URL to current page
	$_SESSION['auth_redirect']=$_redirect_uri;
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH."auth/login.php");
	// stop processing
	exit;
}

/**
 * Test provided input data to be valid.
 * @param unknown $data data to check
 * @return string processed and cleaned data
 */
function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars_decode($data);
	return $data;
}
?>
<html>

<head>
	<?php
	$pgtitle = "Submit Your Testimonial";
	include_once '../includes/head.php';
	?>
</head>
<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
<?php
include_once '../includes/top-left.php';
include_once '../includes/top-center.php';
include_once '../includes/top-right.php';
?>
</div>
<div class="ws"></div>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Submit Your Testimonial</h2>
			</div>
			<div class="panel-body">
<?php 
// check if the request is made by a POST request
if (isset($_POST['pilot'])) {
	// yes, process the request
	$db = new Database();
	
	$pilot = $method = $testimonial = $errmsg = '';
	
	$pilot = test_input($_POST["pilot"]);
	$anon = $_POST["chkAnon"] == 1 ? 1 : 0; 
	$method = test_input($_POST["method"]);
	$testimonial = test_input($_POST["testimonial"]);
	
	//FORM VALIDATION
	
	// check that testimonial is not empty
	if (!isset($testimonial))
	{
		$errmsg = $errmsg . "No testimonial has been provided.";
	}
	
	//END FORM VALIDATION
	
	//display error message if there is one
	if (!empty($errmsg)) {
		echo $errmsg;
	}
	// otherwise, perform DB UPDATES
	else {
		// create a new cache activity
		$db->beginTransaction();
		$db->query("INSERT INTO testimonials (Pilot, Anon, Type, Note)
			VALUES (:pilot, :anon, :type, :note)");
		$db->bind(":pilot", $pilot);
		$db->bind(":anon", $anon);
		$db->bind(":type", $method);
		$db->bind(":note", $testimonial);
		$db->execute();
		$db->endTransaction();
		
		echo 'Thank you! Your testimonial has been submitted successfully and is awaiting moderation.
			Once approved, it should appear on the site within about a week.<br /><br />
			<a href="index.php">Return to home page.</a>';
	} 
}
else {
				?>
				<p>If you've recently been rescued from a wormhole via either a rescue cache or an
					assist from a Signal Cartel rescue pilot, we'd love to hear your feedback!
				</p>
				<p></p>
				<hr class="half-rule">
		  		<div class="row">
			      <form name="testform" id="testform" action="testimonial_submit.php" method="POST">
				      <div class="modal-body black">
						<div class="form-group">
							<input type="checkbox" id="chkAnon" name="chkAnon" value="1"> 
						  			Remain anonymous (your name will not be listed)
						</div>
						
						<div class="field form-group">
							<strong>Rescue Method: </strong>&nbsp;&nbsp;
							<label for="status_1" class="radio-inline">
								<input id="status_1" name="method" type="radio" value="ESRC" 
									required data-error="Please select a method for the rescue">
									Rescue Cache
							</label>
							<label for="status_2" class="radio-inline">
								<input id="status_2" name="method" type="radio" value="SAR" 
									required data-error="Please select a method for the rescue">
									Search and Rescue
							</label>
						</div>

					  	<div class="field form-group">
							<label class="control-label" for="testimonial">Please share your 
								experience with us</label>
							<textarea class="form-control" id="testimonial" name="testimonial" rows="5"
								required data-error="Please enter your testimonial"></textarea>
						</div>
				      </div>
				      <div class="modal-footer">
				        <div class="form-actions">
							<input type="hidden" name="pilot" value="<?php echo 
								$_SESSION['auth_charactername'] ?>" />
						    <button type="submit" class="btn btn-info">Submit</button>
						</div>
				      </div>   
						<script>
						  $( document ).ready(function() {
						    $("#testform").validator();
						  });
						</script>
			      </form>
			    </div>
<?php 
}
?>
			</div>
		</div>
	</div>
</div>

</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>