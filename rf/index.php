<?php include_once '../includes/auth-public-open.php'; ?>
<html>

<head>
<?php
include_once '../includes/bg.php';
$pgtitle = 'Rescue Frigate';
include_once '../includes/head.php';
?>
</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 white" style="text-align: center; height: 100px; vertical-align: middle;">
		<br /><span class="sechead">Rescue Frigate FAQ</span><br /><br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel-group" id="faqAccordion">
			<?php include_once '../includes/faq-q1.php'; ?>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question2">
					<h4 class="panel-title">
						<a href="#" class="ing">What is the Rescue Frigate program?</a>
					</h4>
				</div>
				<div id="question2" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>The EvE-Scout Rescue Frigate (RF) program, an outgrowth of the long-standing <a href="../esrc/">rescue cache</a> service, provides a basic emergency T1 scanning frigate for capsuleers stranded in wormholes, regardless of alliance, sovereignty, or play style.</p>
						<p>RF is only one facet of the EvE-Scout Rescue division, a dedicated group of Signal Cartel pilots who have answered to call of service to the greater New Eden community. As well as being a service to the EVE community, this program provides Signal Cartel members an opportunity to expand their exploration experiences, wormhole lifestyle, and game play content.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question3">
					<h4 class="panel-title">
						<a href="#" class="ing">What is a rescue frigate?</a>
					</h4>
				</div>
				<div id="question3" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>A rescue frigate is designed to be a stripped down vessel, usually a Venture, anchored somewhere in a wormhole that is capable of little beyond scanning down an exit from the wormhole where you are stranded. In alignment with our Credo, it is <em>not</em> fit with any weapons and has minimal defensive capability. It is fitted only with one (1) core probe launcher, eight (8) core scanner probes, and, optionally, modules that enhance scan strength and/or make the frigate harder to scan down. (Oh, and usually a few "hugs" [fireworks, snowballs] or other trinkets for fun!)</p>
						<p>When a capsuleer is stranded and in need, they can contact a Signal Cartel scout in the public EvE-Scout channel for assistance. If the fit of the frigate meets the stranded capsuleer's need, the scout will look up the rescue frigate's location, provide this info to the stranded pilot, and assist them in locating and gaining access to it.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question4">
					<h4 class="panel-title">
						<a href="#" class="ing">Who <em>won't</em> be helped by a rescue frigate?</a>
					</h4>
				</div>
				<div id="question4" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>The rescue frigate concept was created to address those pilots who cannot be helped by our rescue caches, namely, those pilots who do not have a probe launcher fit to their ship and/or do not have any way to fit one from within the wormhole. Thus, pilots who would not be helped by a resche cache, may be helped instead by a rescue frigate.</p>
						<p>Please note that rescue frigates are more expensive and more difficult to place than rescue caches. Therefore, they are relatively more rare in space. Every system that has a rescue frigate in place should also have a rescue cache, if that is of help to you.</p>
					</div>
				</div>
			</div>
			<?php include_once '../includes/faq-q10.php'; ?>
			<?php include_once '../includes/faq-q20.php'; ?>
		</div>
	</div>
</div>
</div>
</body>
</html>