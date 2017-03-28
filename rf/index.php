<?php
session_start();
if (isset($_SESSION['auth_characterid'])) {
	$charimg = '<img src="http://image.eveonline.com/Character/'.$_SESSION['auth_characterid'].'_64.jpg">';
	$charname = $_SESSION['auth_charactername'];
	$chardiv = '<div style="text-align: center;">'.$charimg.'<br /><span class="white">'.$charname.'</span><br /><span class="descr"><a href="/auth/logout.php">logout</a></span></div>';
}
else {
	$chardiv = '<a href="../auth/login.php"><img src="../img/EVE_SSO_Login_Buttons_Small_Black.png"></a>';
}
?>
<html>

<head>
	<meta http-equiv="Content-Language" content="en-us">
	<title>Rescue Frigate :: EvE-Scout Rescue</title>
	<meta charset="utf-8">
	<link href="../css/main.css" rel="stylesheet">
	<!-- Latest compiled and minified Bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="../js/typeahead.js"></script>
</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<div class="col-md-2 col-sm-6">
		<a href="../index.php"><img src="../img/eve-scout-logo.png" alt="EvE-Scout Rescue" /></a>
	</div>
	<div class="col-sm-8 white" style="text-align: center; height: 100px; vertical-align: middle;">
		<br /><span class="sechead">Rescue Frigate FAQ</span><br /><br />
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
	<div class="col-sm-12">
		<div class="panel-group" id="faqAccordion">
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question10">
					<h4 class="panel-title">
						<a href="#" class="ing">Help! I'm stranded in a wormhole. What do I do?</a>
					</h4>
				</div>
				<div id="question10" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>Please head over to our in-game public channel, "Eve-Scout", and ask for a Signal Cartel member to guide you to one of our rescue caches or rescue frigates. If you would like to be more discreet, you can EVEmail <a href="https://gate.eveonline.com/Profile/Thrice%20Hapus">Thrice Hapus</a> to further discuss rescue options.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question0">
					<h4 class="panel-title">
						<a href="#" class="ing">What is the Rescue Frigate program?</a>
					</h4>
				</div>
				<div id="question0" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>The EvE-Scout Rescue Frigate (RF) program, an outgrowth of the long-standing <a href="../esrc/">rescue cache</a> service, provides a basic emergency T1 scanning frigate for capsuleers stranded in wormholes, regardless of alliance, sovereignty, or play style.</p>
						<p>RF is only one facet of the EvE-Scout Rescue division, a dedicated group of Signal Cartel pilots who have answered to call of service to the greater New Eden community. As well as being a service to the EVE community, this program provides Signal Cartel members an opportunity to expand their exploration experiences, wormhole lifestyle, and game play content, as well as being an opportunity to earn a small, steady stream of ISK.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question1">
					<h4 class="panel-title">
						<a href="#" class="ing">What is a rescue frigate?</a>
					</h4>
				</div>
				<div id="question1" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>A rescue frigate is designed to be a stripped down vessel anchored somewhere in a wormhole that is capable of little beyond scanning down an exit from the wormhole where you are stranded. In alignment with our Credo, it is <em>not</em> fit with any weapons and has minimal defensive capability. It is fitted only with one (1) core probe launcher, eight (8) core scanner probes, and modules that enhance scan strength (and, usually, a few "hugs" [fireworks, snowballs] or other trinkets for fun).</p>
						<p>When a capsuleer is stranded and in need, they can contact a Signal Cartel scout in the public EvE-Scout channel for assistance. If the fit of the frigate meets the stranded capsuleer's need, the scout will look up the rescue frigate's location, provide this info to the stranded pilot, and assist them in locating and gaining access to it.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question2">
					<h4 class="panel-title">
						<a href="#" class="ing">Who <em>won't</em> be helped by a rescue frigate?</a>
					</h4>
				</div>
				<div id="question2" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>Because a rescue cache is a small secure container that contains a minimum of one (1) core probe launcher and eight (8) core scanner probes, it will not be any help to you if you do not already have a probe launcher fitted to your ship, if you do not have a way to change your fit in your current wormhole, or if you no longer have a ship at all!</p>
						<p>In these cases, be sure to see if we have a <a href="../rf/">rescue frigate</a> that is accessible to you.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question3">
					<h4 class="panel-title">
						<a href="#" class="ing">I live in this wormhole. Do rescue caches present any threat to me?</a>
					</h4>
				</div>
				<div id="question3" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>While we respect the inhabitants of every wormhole, it is not always possible to contact them prior to or after leaving a rescue cache. These resources that we leave behind do not provide any actionable intel nor do we share information about our travels with any third parties. Additionally, our pilots are instructed to avoid placing rescue caches within d-scan range of POSes or Citadels whenever possible. However, New Eden is a dangerous place and we cannot guarantee that our resources will not be untampered with. As in all scenarios, use caution when approaching a rescue cache!</p>
						<p>For further inquiries regarding EvE-Scout Rescue's initiatives or policies, please contact <a href="https://gate.eveonline.com/Profile/Thrice%20Hapus">Thrice Hapus</a>.</p>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse" data-parent="#faqAccordion" data-target="#question4">
					<h4 class="panel-title">
						<a href="#" class="ing">Why do you do this? What's in it for you?</a>
					</h4>
				</div>
				<div id="question4" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
						<p>The pilots of <a href="http://www.eve-scout.com/signal-cartel/">Signal Cartel</a>, the core corporation of the <a href="https://gate.eveonline.com/Alliance/EvE-Scout%20Enclave">EvE-Scout Enclave</a> alliance, abide by a strict Credo that emphasizes service to the capsuleer community of New Eden, respect to all regardless of affiliation or play style, non-aggression, friendship, kindness, and grace under pressure.</p>
						<p>Within Signal Cartel, each pilot is encouraged to participate in and invent ways to embody all that the Credo envisions. The Rescue Cache program is merely one of the myriad ways our pilots strive to live up to that high calling.</p>
						<p>There are many reasons capsuleers fly in New Eden: glory, fame, riches. Our pilots do so to be a part of one of the greatest stories of our time. Our glory, our fame, our wealth is the magnificent narrative tapestry we are weaving together. One of our favorite corporate pastimes is sharing with one another stories of successful rescues that occur as the result of our various rescue programs. We are grateful to you for granting us this unique opportunity!</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</body>
</html>