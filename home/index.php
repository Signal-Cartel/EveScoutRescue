<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$database = new Database();
$caches = new Caches($database);
$users = new Users($database);
$rescues = new Rescue($database);

$pgtitle = "New Eden's Premier Wormhole Rescue Service";
$rootCopilot = $_SERVER['DOCUMENT_ROOT'] . '/copilot/';
require_once $rootCopilot . 'auth/jcount.php';	// updated by Allison
require_once '../includes/fxn_mmmr.php';

$ctrESRCrescues = $rescues->getRescueCount('closed-esrc');
$ctrSARrescues = $rescues->getRescueCount('closed-rescued');
$ctrAllRescues = intval($ctrESRCrescues) + intval($ctrSARrescues);
$ctractive = $caches->getActiveCount();
$arrSARWaits = $rescues->getSARWaitTime();
$SARWaitMean = mmmr($arrSARWaits);
$SARWaitMode = mmmr($arrSARWaits, 'mode');
$SARWaitModeCnt = mmmr($arrSARWaits, 'modecnt');
$daysBack = "7";
$ctrSystems = $caches->getSystemsVisited($daysBack);


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<script type="text/javascript">
	$(window).load(function(){
		setCarouselHeight('#testimonial-carousel');

		function setCarouselHeight(id)
		{
			var slideHeight = [];
			$(id+' .item').each(function()
			{
				// add all slide heights to an array
				slideHeight.push($(this).height());
			});

			// find the tallest item
			max = Math.max.apply(null, slideHeight);

			// set the slide's height
			$(id+' .carousel-content').each(function()
			{
				$(this).css('height',max+'px');
			});
		}
	});
</script>

<div class="row">
	<div class="col-sm-4" style="text-align: center;">
		<span class="sechead white">
			Confirmed Rescues: 
			<span style="font-weight: bold; color: gold;"><?=$ctrAllRescues?></span>
		</span><br />
		<span class="white">since YC119-Mar-18</span>
		<br /><br />
		<span class="sechead white">Total Active Caches: 
			<span style="font-weight: bold; color: gold;"><?=$ctractive?></span>
		</span><br /> 
		<span class="white">
			<span style="font-weight: bold; color: gold;">
				<?=round((intval($ctractive)/2603)*100,1)?>%</span> of all wormhole systems
		</span>
		<br /><br />
		<span class="sechead white">Average Wait Time: 
			<span style="font-weight: bold; color: gold;"><?=round(intval($SARWaitMean))?> days</span>
		</span><br /> 
		<span class="white">
			<span style="font-weight: bold; color: gold;">
		    <?=round(intval($SARWaitModeCnt) / max(intval($ctrSARrescues), 1) * 100)?>%</span>
			of all rescues occur within <?=round(intval($SARWaitMode)+1*24)?> hours
		</span>
		<br /><br />
		<span class="sechead white">
			<span style="font-weight: bold; color: gold;"><?=intval($jcount)?></span> 
			J-space Systems</span><br /> 
		<span class="white">
			visited by our Rescue pilots in the last 
			<span style="font-weight: bold; color: gold;"><?=intval($daysBack)?></span> days
		</span><br /><br />
	</div>
	<div class="col-sm-8" style="text-align: center;">
		<!-- TESTIMONIAL CAROUSEL -->
		<div id="testimonial-carousel" class="carousel slide" data-ride="carousel"
			data-interval="20000">
	    	<!-- Wrapper for slides -->
		    <div class="row">
				<div class="col-sm-offset-1 col-sm-10">
				<div class="subhead white">PILOT TESTIMONIALS</div>
		            <div class="carousel-inner">
		                <?php include '../includes/testimonials.php'; ?>
		            </div>
		        </div>
		    </div>
		    <!-- Controls --> 
		    <a class="left carousel-control" href="#testimonial-carousel" data-slide="prev">
		    	<span class="glyphicon glyphicon-chevron-left"></span>
		 	</a>
		 	<a class="right carousel-control" href="#testimonial-carousel" data-slide="next">
		    	<span class="glyphicon glyphicon-chevron-right"></span>
		  	</a>
		</div>
		<!-- END TESTIMONIAL CAROUSEL -->
		<a class="btn btn-primary btn-md" href="testimonial_submit.php" role="button">Submit Your 
			Testimonial</a>&nbsp;&nbsp;&nbsp;&nbsp;
		<a class="btn btn-primary btn-md" href="testimonials_list.php" role="button">Read All 
			Testimonials</a>
	</div>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Rescue Cache&nbsp;&nbsp;&nbsp;&nbsp;
					<img src="../img/cache.png" height="40px" /></h2>
				<div class="sechead pull-right" style="padding-top: 12px;">
			        <span style="font-weight: bold; color: #2c608f;"><?=$ctrESRCrescues?></span> 
						rescues
			    </div>
			</div>
			<div class="panel-body">
				<p class="lead">Anchored throughout Anoikis, our rescue caches contain a probe 
					launcher, core scanner probes, and even a hug or two. Perfect if you have a 
					fitting service or just need probes. <a href="esrc.php">Learn more...</a></p>
				<p class="text-center">
					<a class="btn btn-primary btn-md" href="heroes.php" role="button">Sower/Tender 
						Hall of Fame</a> &nbsp;&nbsp;&nbsp; 
					<a class="btn btn-primary btn-md" href="heroes-disp.php" role="button">
						Dispatcher Hall of Fame</a></p>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Search &amp; Rescue &nbsp;&nbsp;&nbsp;
					<img src="../img/search.png" height="40px" /></h2>
				<div class="sechead pull-right" style="padding-top: 7.5px;">
			        <span style="font-weight: bold; color: #2c608f;"><?=$ctrSARrescues?></span> 
						rescues
			    </div>
			</div>
			<div class="panel-body">
				<p class="lead">If we don't have a rescue cache in your current wormhole system, 
					don't despair! Our Search and Rescue pilots will work hard to find you and 
					scout you back to known space. <a href="sar.php">Learn more...</a></p>
				<p class="text-center">
					<a class="btn btn-primary btn-md" href="heroes_sar.php" role="button">Search 
						and Rescue Hall of Fame</a></p>
			</div>
		</div>
	</div>
</div>

<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
