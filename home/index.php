<?php include_once '../includes/auth-inc.php'; ?>
<html>

<head>
<?php
$pgtitle = "New Eden's Premier Wormhole Rescue Service";
include_once '../includes/head.php';
?>
</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 white" style="text-align: center; height: 100px; vertical-align: middle;">
		<br /><span class="sechead">New Eden's Premier Wormhole Rescue Service</span><br /><br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p class="lead">Ahoy, fellow traveler! You are probably here because you've come across one of our rescue caches or an unpiloted rescue frigate. Don't be alarmed; these items are completely harmless! Read on to learn more about our services.</p>
			</div>
			<div class="panel-body">
				<p><a href="http://www.eve-scout.com/signal-cartel/">Signal Cartel</a>, the core corporation of the <a href="https://gate.eveonline.com/Alliance/EvE-Scout%20Enclave">EvE-Scout Enclave</a> alliance, is a neutral, non-profit entity that aims to provide a valuable public service to all of New Eden. As such, one of our primary initiatives is to look for and rescue capsuleers who are stranded inside wormholes without equipment to get out by themselves. In accordance to our Credo, our services are free and available to capsuleers of all play styles and allegiance.</p>
				<p>If you also think that no one should be stranded inside a wormhole due to server problems or socket disconnects, please support this initiative by not blowing up our rescue caches and rescue frigates! We sincerely thank you for your cooperation! </p>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Cache&nbsp;&nbsp;&nbsp;&nbsp;<img src="../img/cache.png" height="40px" /></h2>
				<div class="btn-group pull-right" style="padding-top: 12px;">
			        <a class="btn btn-primary btn-lg" href="../esrc/" role="button">Learn more</a>
			    </div>
			</div>
			<div class="panel-body">
				<p class="lead">Anchored throughout Anoikis, our rescue caches contain a probe launcher, core scanner probes, and even a hug or two. Perfect if you have a fitting service or just need probes.</p>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Frigate&nbsp;&nbsp;<img src="../img/frig.png" height="40px" /></h2>
				<div class="btn-group pull-right" style="padding-top: 12px;">
			        <a class="btn btn-primary btn-lg" href="../rf/" role="button">Learn more</a>
			    </div>
			</div>
			<div class="panel-body">
				<p class="lead">Don't have any way to fit a probe launcher? Then our rescue frigates may be just what you're looking for. They have been custom fit so that you can pilot one with even minimal skills.</p>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Search&nbsp;&nbsp;&nbsp;<img src="../img/search.png" height="40px" /></h2>
				<div class="btn-group pull-right" style="padding-top: 7.5px;">
			        <a class="btn btn-primary btn-lg" href="../sar/" role="button">Learn more</a>
			    </div>
			</div>
			<div class="panel-body">
				<p class="lead">If we don't have a rescue cache or frigate in your current wormhole system, don't despair! Our Search and Rescue pilots will work hard to find you and scout you back to known space.</p>
			</div>
		</div>
	</div>
</div>
</div>
</body>
</html>