<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$pgtitle = "Contribute";


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Contribute</h2>
			</div>
			<div class="panel-body">
				<p><a href="http://www.signalcartel.com/">Signal Cartel</a>, 
					the core corporation of the 
					<a href="http://www.eve-scout.com/">EvE-Scout 
					Enclave</a> alliance, is a neutral, non-profit entity that aims to 
					provide a valuable public service to all of New Eden. As such, one 
					of our primary initiatives is to look for and rescue capsuleers who 
					are stranded inside wormholes without equipment to get out by themselves. 
					In accordance to our Credo, our services are free and available to 
					capsuleers of all play styles and allegiance.</p>
				<p></p>
				<hr class="half-rule">
		  		<div class="row">
			      <div class="col-sm-4" style="text-align: center;">
			        <img src="../img/cache-lg.png" height="190" style="max-width: 70%; height: auto;">
			        <h3>Let It Be</h3>
			        <p>If you agree that no one should be stranded inside a wormhole due 
					to server problems or socket disconnects, please support this initiative 
					by not destroying any rescue caches you find in wormhole space.<br />
					We sincerely thank you for your	cooperation!</p>
			      </div>
			      <div class="col-sm-4" style="text-align: center;">
			        <img src="../img/donate.png" style="max-width: 70%; height: auto;">
			        <h3>Send Us a Tip</h3>
			        <p>Consider sending us a tip as a thank you. Your donations go directly to reward 
					our scouts.</p>
			        <p><em><strong>Please send your donations to the in-game corp 
					"<span style="color: #337ab7;">Signal Cartel</span>".</strong></em><br />
			        	Be sure to indicate "ESR" or "Rescue" on the memo line.</p>
			      </div>
			      <div class="col-sm-4" style="text-align: center;">
			        <img src="../img/wanted_final.png" style="max-width: 85%; height: auto;">
			        <h3>Join Us</h3>
			        <p>Is helping other capsuleers of more interest to you than blowing them up?
						<br />Want to be part of an active exploration corp filled with neutral 
						non-aggressors?<br/>If this sounds like a good fit for you, check out the 
						<a href="https://www.signalcartel.com/about">Credo</a> and consider 
						<a href="http://www.eve-scout.com/signal-cartel/how-to-join/">joining us</a>!</p>
			      </div>
			    </div>
				<p></p>
				<p style="text-align: center; font-weight: bold;">Thanks to everyone who made our 
				<a href="../donate/">YC122 Fund Drive</a> a huge success!</p>
			</div>
		</div>
	</div>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
