<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// check if the user is logged in
if (!isset($_SESSION['auth_characterid'])) {
	// void the session entries on 'attack'
	session_unset();
	// save the redirect URL to current page
	$_SESSION['auth_redirect'] = Config::ROOT_PATH . substr($_SERVER['PHP_SELF'], 1);
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH."auth/login.php");
	// stop processing
	exit;
}

// PAGE VARS
$database = new Database();
$pgtitle = "Submit Your Testimonial";


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left"><?=$pgtitle?></h2>
			</div>
			<div class="panel-body">
<?php 
// check if the request is made by a POST request
if (isset($_POST['pilot'])) {
	// yes, process the request
	$db = new Database();
	
	$pilot = $method = $testimonial = $errmsg = '';
	
	$pilot = Output::prepTextarea($_POST["pilot"]);
	$anon = $_POST["chkAnon"] ?? 0; 
	$method = Output::prepTextarea($_POST["method"]);
	$testimonial = Output::prepTextarea($_POST["testimonial"]);
	
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
		$database->beginTransaction();
		$database->query("INSERT INTO testimonials (Pilot, Anon, Type, Note)
			VALUES (:pilot, :anon, :type, :note)");
		$database->bind(":pilot", $pilot);
		$database->bind(":anon", $anon);
		$database->bind(":type", $method);
		$database->bind(":note", $testimonial);
		$database->execute();
		$database->endTransaction();
		
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


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
