<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 

?>
<html>

<head>
	<?php
	$pgtitle = "New Eden's Premier Wormhole Rescue Service";
	include_once '../includes/head.php';
	?>
	<script type="text/javascript">
		$(window).load(function(){
		    setCarouselHeight('#carousel-example');
	
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
    <style>
    <!--
    	.carousel-content {
		    color:white;
		    display:flex;
		    text-align: left;
		    padding-left: 20px;
		    padding-right: 20px;
		}
    -->
    </style>
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
<?php
require_once '../class/db.class.php';
require_once '../class/caches.class.php';

$database = new Database();
$caches = new Caches($database);

$ctrrescues = $caches->getRescueTotalCount();

$ctractive = $caches->getActiveCount();
?>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-4" style="text-align: center;">
		<span class="sechead white">
			Confirmed Rescues: 
			<span style="font-weight: bold; color: gold;"><?php echo $ctrrescues; ?></span>
		</span><br />
		<span class="white">since YC119-Mar-18</span><br /><br />
		<span class="sechead white">Total Active Caches: 
			<span style="font-weight: bold; color: gold;"><?php echo $ctractive; ?></span>
		</span><br /> 
		<span class="white">
			<span style="font-weight: bold; color: gold;"><?php echo round((intval($ctractive)/2603)*100,1); ?>% </span>
			of all wormhole systems
		</span>
	</div>
	<div class="col-sm-8" style="text-align: center;">
		<!-- TESTIMONIAL CAROUSEL -->
		<div id="carousel-example" class="carousel slide" data-ride="carousel"
			data-interval="20000">
	    	<!-- Wrapper for slides -->
		    <div class="row">
		        <div class="col-sm-offset-1 col-sm-10">
		            <div class="carousel-inner">
		                <?php include '../includes/testimonials.php'; ?>
		            </div>
		        </div>
		    </div>
		    <!-- Controls --> 
		    <a class="left carousel-control" href="#carousel-example" data-slide="prev">
		    	<span class="glyphicon glyphicon-chevron-left"></span>
		 	</a>
		 	<a class="right carousel-control" href="#carousel-example" data-slide="next">
		    	<span class="glyphicon glyphicon-chevron-right"></span>
		  	</a>
		</div>
		<!-- END TESTIMONIAL CAROUSEL -->
	</div>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Rescue Cache&nbsp;&nbsp;&nbsp;&nbsp;<img src="../img/cache.png" height="40px" /></h2>
				<div class="btn-group pull-right" style="padding-top: 12px;">
			        <a class="btn btn-primary btn-lg" href="../esrc/" role="button">Learn more</a>
			    </div>
			</div>
			<div class="panel-body">
				<p class="lead">Anchored throughout Anoikis, our rescue caches contain a probe launcher, core scanner probes, and even a hug or two. Perfect if you have a fitting service or just need probes.</p>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Search &amp; Rescue &nbsp;&nbsp;&nbsp;<img src="../img/search.png" height="40px" /></h2>
				<div class="btn-group pull-right" style="padding-top: 7.5px;">
			        <a class="btn btn-primary btn-lg" href="../sar/" role="button">Learn more</a>
			    </div>
			</div>
			<div class="panel-body">
				<p class="lead">If we don't have a rescue cache in your current wormhole system, don't despair! Our Search and Rescue pilots will work hard to find you and scout you back to known space.</p>
			</div>
		</div>
	</div>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<p><a href="http://www.eve-scout.com/signal-cartel/">Signal Cartel</a>, 
					the core corporation of the 
					<a href="https://gate.eveonline.com/Alliance/EvE-Scout%20Enclave">EvE-Scout 
					Enclave</a> alliance, is a neutral, non-profit entity that aims to 
					provide a valuable public service to all of New Eden. As such, one 
					of our primary initiatives is to look for and rescue capsuleers who 
					are stranded inside wormholes without equipment to get out by themselves. 
					In accordance to our Credo, our services are free and available to 
					capsuleers of all play styles and allegiance.</p>
				<p style="text-align: center;">
			        <a class="btn btn-primary btn-lg" href="about.php" role="button">
			        	Learn More About Us</a>
			    </p>
				<p>If you also think that no one should be stranded inside a wormhole due 
					to server problems or socket disconnects, please support this initiative 
					by not blowing up our rescue caches! We sincerely thank you for your 
					cooperation!</p>
			</div>
		</div>
	</div>
</div>

</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>