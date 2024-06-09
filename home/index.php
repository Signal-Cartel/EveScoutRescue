<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$database = new Database();
$caches = new Caches($database);
$users = new Users($database);
$rescues = new Rescue($database);
$storms = new Storms($database);

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
$daysBack = 7;
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
					fitting service or just need probes. <a style="text-decoration: underline;"  href="esrc.php">Learn more...</a></p>
				<p class="text-center">
					<a class="btn btn-primary btn-md" href="heroes.php" role="button">Sower/Tender 
						Hall of Fame</a> &nbsp;&nbsp;&nbsp; 
					<a class="btn btn-primary btn-md" href="heroes_disp.php" role="button">
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
					scout you back to known space. <a style="text-decoration: underline;"  href="sar.php">Learn more...</a></p>
				<p class="text-center">
					<a class="btn btn-primary btn-md" href="heroes_sar.php" role="button">Search 
						and Rescue Hall of Fame</a></p>
			</div>
		</div>
	</div>
</div>

<!-- NEWS TICKER -->
<style>
@import url('https://fonts.googleapis.com/css?family=Montserrat');

.onoffswitch3
{
    position: relative; 
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}

.onoffswitch3-checkbox {
    display: none;
}

.onoffswitch3-label {
    display: block; overflow: hidden; cursor: pointer;
    border: 0px solid #999999; border-radius: 0px;
}

.onoffswitch3-inner {
    display: block; width: 200%; margin-left: -100%;
    -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
    -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
}

.onoffswitch3-inner > span {
    display: block; float: left; position: relative; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: 'Montserrat', sans-serif; font-weight: bold;
    -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
}

.onoffswitch3-inner .onoffswitch3-active {
    padding-left: 10px;
    background-color: #EEEEEE; color: #FFFFFF;
}

.onoffswitch3-inner .onoffswitch3-inactive {
    width: 100px;
    padding-left: 16px;
    background-color: #EEEEEE; color: #FFFFFF;
    text-align: right;
}

.onoffswitch3-switch {
    display: block; width: 50%; margin: 0px; text-align: center; 
    border: 0px solid #999999;border-radius: 0px; 
    position: absolute; top: 0; bottom: 0;
}
.onoffswitch3-active .onoffswitch3-switch {
    background: #27A1CA; left: 0;
    width: 200px;
}
.onoffswitch3-inactive{
    background: #A1A1A1; right: 0;
    width: 20px;
}
.onoffswitch3-checkbox:checked + .onoffswitch3-label .onoffswitch3-inner {
    margin-left: 0;
}

.scroll-text{
    color: #000;
}

a.nostyle:link {
    text-decoration: inherit;
    color: inherit;
    cursor: pointer;
}

a.nostyle:visited {
    text-decoration: inherit;
    color: inherit;
    cursor: pointer;
}
</style>

<div class="onoffswitch3">
    <input type="checkbox" name="onoffswitch3" class="onoffswitch3-checkbox" id="myonoffswitch3" checked>
    <label class="onoffswitch3-label" for="myonoffswitch3">
        <span class="onoffswitch3-inner">
            <span class="onoffswitch3-active"><a class="nostyle" href="stormtrack.php">
				<marquee class="scroll-text">

				<?php
				$rows = $storms->getRecentReports("public", false);
				if (empty($rows)) {
					echo 'No current reports <span class="glyphicon glyphicon-forward"></span> 
						Stay tuned for our next update <span class="glyphicon glyphicon-forward"></span> ';
				}
				else {
					foreach ($rows as $value) {
                        if ($value['observation_type'] == 'toms_shuttle') {
                            $oddity = $value;
                            continue;
                        }
						echo 'Click for tabular view <span class="glyphicon glyphicon-forward"></span> ';
                        $type = explode(' ', Storms::getStormName($value['observation_type']));
                        $date = new DateTime($value['created_at']);
						echo $value['system_name'] .' ['. $value['region_name'] .'] - '.
							' Strong Metaliminal '. $type[0] .
							' Ray Storm - last reported: '. $date->format("M-d@H:i");
						echo ' <span class="glyphicon glyphicon-forward"></span> ';
					}
				}	?>
				</marquee>
                <span class="onoffswitch3-switch">NEW EDEN WEATHER</span>
				</a>
			</span>
        </span>
    </label>
</div>
<!-- /NEWS TICKER -->
<!-- SPACE ODDITY -->
<?php
    if (!is_null($oddity)) {
?>
<div class="onoffswitch3">
    <input type="checkbox" name="onoffswitch4" class="onoffswitch3-checkbox" id="myonoffswitch3" checked>
    <label class="onoffswitch3-label" for="myonoffswitch3">
        <span class="onoffswitch3-inner">
            <span class="onoffswitch3-active">
				<marquee class="scroll-text">
                  <?
                  $value = $oddity;
                  echo '<span class="glyphicon glyphicon-forward"></span> ';
                  $date = new DateTime($value['created_at']);
                  echo $value['system_name'] .' ['. $value['region_name'] .'] - '.
                    ' Tom\'s Shuttle ' .
                    ' - last reported: '. $date->format("M-d@H:i");
                  echo ' <span class="glyphicon glyphicon-forward"></span> ';

                  ?>
                </marquee>
                <span class="onoffswitch3-switch">SPACE ODDITY</span>
            </span>
        </span>
    </label>
</div>
<?php
    }
?>
<!-- /SPACE ODDITY -->
<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
