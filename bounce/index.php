<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
?>
<html>

<head>
<?php 
$pgtitle = 'Bounce Method';
include_once '../includes/head.php'; 
?>
</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 white" style="text-align: center; height: 100px; vertical-align: middle;">
		<br /><span class="sechead">Bounce Method Instructions</span><br /><a href="index_ru.php">русский</a><br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p class="lead">So, how do I go about finding a rescue cache?</p>
			</div>
			<div class="panel-body">
				<p>The process to follow to locate the rescue cache in space is fairly straightforward. We call it the "bounce method".</p>
				<p><img src="../img/BounceMethod.jpg" width="900px" style="vertical-align: middle; max-width: 100%; height: auto;" /></p>
				<ol>
					<li>Warp to 100 km off the planet (the "Align Planet").</li>
					<li>Warp to 100 km off the second planet (the "Rescue Cache Planet").</li>
					<li>During the warp, drop a temporary bookmark at ~100,000km to 50,000km of the Rescue Cache Planet and name it something like "T1". This initial bookmark is designed to reduce travel time and to gather a sense of bearing (speed, drop distance, etc.). Placement of this first bookmark is critical for reducing the number of bounces you will need to make. Here is a handy guideline to follow:<br />
						- 5AU/s ships: Submit BM when flying past 1,000,000 km to drop a BM around 40,000 km in space<br />
						- 3AU/s ships: Submit BM when flying between 100,000 and 200,000 km to drop a BM around 40,000 km in space<br /></li>
						<i>Note:</i> When setting bookmarks, name them the distance from the location planet (i.e. "23,801km", "42,087km"). This will quickly allow you identify which bookmarks are closest to each side of the cache. Keep bouncing and setting bookmarks between these bookmarks and delete those that are farther away until you can warp to the cache. In these steps, bookmark names of "T1", "T2," etc. are used for convenience. 
					<li>When you leave warp, drop a second temporary bookmark and name it something like "T2". You just completed pass #1.</li>
					<li>Time to start pass #2. Warp back to T1 bookmark.</li>
					<li>During the warp, drop another temporary bookmark between T1 and T2. Name it "T3".</li>
					<li>When you arrive at T1, check the distance from the Rescue Cache Planet. Is the distance between 22,000 and 50,000 km? If it is, check d-scan for the cache. If not, warp back to T3 and continue to next step.</li>
					<li>From T3, warp to T2. During the warp, drop a bookmark and name it "T4".</li>
					<li>When you arrive at T2, you have completed pass #2.</li>
					<li>Time to start pass #3. Warp to T4.</li>
					<li>Repeat steps 6-10, warping to the nearest temporary bookmark with each pass and gradually approaching the desired distance. As soon as you are between 22,000 and 50,000 km from the Rescue Cache Planet, check d-scan for the cache. As soon as it appears on your overview, warp to it.</li>
					<li>Someone in EvE-Scout channel will be able to give you the password to access the cache once you have located it.</li>
					<li><strong>When you are finished using the cache, it would be very helpful if you could return all supplies to it and/or return it to the location where you found it so it can be ready for use by the next stranded pilot. Thank you!</strong></li>
				</ol>
			</div>
		</div>
	</div>
</div>
</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>