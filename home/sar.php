<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
?>
<html>

<head>
<?php
$pgtitle = 'EvE-Scout Search and Rescue';
include_once '../includes/head.php';
?>
</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<?php include_once '../includes/top-center.php'; ?>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-8">
		<div class="panel-group" id="faqAccordion">
			<?php include_once '../faq/faq-q1.php'; ?>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question2">
					<h4 class="panel-title">
						<a href="#" class="ing">What is the Search and Rescue program?</a>
					</h4>
				</div>
				<div id="question2" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>The EvE-Scout Search and Rescue (SAR) program is one arm of our 
							EvE-Scout Rescue (ESR) division. In situations where no rescue 
							cache is present, our SAR program strives to facilitate a live 
							rescue by one of our scouts. This service is offered to all pilots, 
							regardless of alliance, sovereignty, or play style.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question3">
					<h4 class="panel-title">
						<a href="#" class="ing">So, what will this cost me?</a>
					</h4>
				</div>
				<div id="question3" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>We do not charge any fee for our search and rescue service, and our pilots will never solicit payment from you. However, while no payment is required, a tip is always appreciated! The amount of this gratuity, if anything, is completely up to you to determine.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question4">
					<h4 class="panel-title">
						<a href="#" class="ing">Who <em>won't</em> be helped by search and rescue?</a>
					</h4>
				</div>
				<div id="question4" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>While our SAR pilots will do their very best to locate the wormhole system where you are stranded, we cannot offer any guarantees as to when or even if we will be able to locate you and effect a rescue. Due to the constant flux of wormhole connections, there is no way to be sure we will locate an entrance to your system in a timely fashion.</p>
						<p>Please also note that SAR does <strong>not</strong> conduct searches for 
						specific wormholes, due to the Signal Cartel 
						<a href="http://www.signalcartel.com/about">Credo</a>'s requirement of 
						maintaining neutrality. While we will not be able to help with these sorts 
						of wormhole search requests, we are happy to recommend the following 
						service(s) provided by other citizens of New Eden:</p>
						<ul>
							<li>
								<a href="http://www.torpedodelivery.com/wormhole-location" 
								target="_blank">Wingspan Delivery Services: Wormhole 
								Location</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<?php include_once '../faq/faq-q10.php'; ?>
			<?php include_once '../faq/faq-q20.php'; ?>
		</div>
	</div>
	<div class="col-sm-4 white" style="text-align: center;">
		<a href="https://t.co/EgOnvx3qby" target="_blank">
			<img class="img-responsive img-rounded" src="../img/SAR.jpg" width="320px" />
		</a>
		(thanks, <a href="https://t.co/EgOnvx3qby" target="_blank">@estedestirr</a>!)
	</div>
</div>

</div>
</body>
</html>