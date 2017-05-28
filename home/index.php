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
	<?php include_once '../includes/top-center.php'; ?>
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
	<div class="col-sm-12" style="text-align: center;">
		<span class="sechead" style="color: gold;">As seen on the o7 Show:</span><br />
		<iframe width="560" height="315" src="https://www.youtube.com/embed/y-V9a28ufoM?ecver=1" frameborder="0" allowfullscreen></iframe>
	</div>
</div>

</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>