<?php
session_start();
if (isset($_SESSION['auth_characterid'])) {
	$charimg = '<img src="http://image.eveonline.com/Character/'.$_SESSION['auth_characterid'].'_64.jpg">';
	$charname = $_SESSION['auth_charactername'];
	$chardiv = '<div style="text-align: center;">'.$charimg.'<br /><span class="white">'.$charname.'</span><br /><span class="descr"><a href="/auth/logout.php">logout</a></span></div>';
}
else {
	$chardiv = '<a href="/auth/login.php"><img src="/img/EVE_SSO_Login_Buttons_Small_Black.png"></a>';
}
?>
<html>

<head>
	<meta http-equiv="Content-Language" content="en-us">
	<title>EvE-Scout Rescue :: New Eden's Premier Wormhole Rescue Service</title>
	<meta charset="utf-8">
	<link href="/css/main.css" rel="stylesheet">
	<!-- Latest compiled and minified Bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/js/typeahead.js"></script>
</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<div class="col-md-2 col-sm-6">
		<a href="/"><img src="/img/eve-scout-logo.png" alt="EvE-Scout Rescue" /></a>
	</div>
	<div class="col-sm-8 white" style="text-align: center; height: 100px; vertical-align: middle;">
		<span class="sechead">EVE SCOUT RESCUE<br />New Eden's Premier Wormhole Rescue Service</span><br /><br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<div class="col-md-2 col-sm-6">
		<div style="text-align: right;">
			<?php echo isset($chardiv) ? $chardiv : '' ?>
		</div>
	</div>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12 white">
		<p class="lead">Ahoy, fellow traveler! You are probably here because you've come across one of our rescue caches or an unpiloted T1 rescue frigate. Don't be alarmed; these items are completely harmless!</p>
		<p><a href="http://www.eve-scout.com/signal-cartel/">Signal Cartel</a>, the core corporation of the <a href="https://gate.eveonline.com/Alliance/EvE-Scout%20Enclave">EvE-Scout Enclave</a> alliance, is a neutral, non-profit entity that aims to provide valuable public service to all of New Eden. As such, one of our primary initiatives is to look for and rescue capsuleers who are stranded inside wormholes without equipment to get out by themselves. In accordance to our Credo, our services are free and available to capsuleers of all play styles and allegiance.</p>
		<p>If you also think that no one should be stranded inside a wormhole due to server problems or socket disconnects, please support this initiative by not blowing up our rescue caches and rescue frigates! We sincerely thank you for your cooperation! Read on to learn more.</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<div class="jumbotron">
			<h1 class="display-4">Cache</h1>
			<p class="lead">Anchored throughout Anoikis, our rescue caches contain a probe launcher, core scanner probes, and even a hug or two. Perfect if you have a fitting service or just need probes.</p>
			<p class="lead">
				<a class="btn btn-primary btn-lg" href="/esrc/" role="button">Learn more</a>
			</p>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="jumbotron">
			<h1 class="display-4">Frigate</h1>
			<p class="lead">Don't have any way to fit a probe launcher? Then our rescue frigates may be just what you're looking for. They have been custom fit so that you can pilot one with even minimal skills.</p>
			<p class="lead">
				<a class="btn btn-primary btn-lg" href="/rf/" role="button">Learn more</a>
			</p>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="jumbotron">
			<h1 class="display-4">Search</h1>
			<p class="lead">If we don't have a rescue cache or frigate in your current wormhole system, don't despair! Our Search and Rescue pilots will work hard to find you and scout you back to known space.</p>
			<p class="lead">
				<a class="btn btn-primary btn-lg" href="/sar/" role="button">Learn more</a>
			</p>
		</div>
	</div>
</div>
</div>
</body>
</html>