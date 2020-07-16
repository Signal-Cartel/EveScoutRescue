<?php 
$testimonials = new Testimonials($database);
$arrTestimonials = $testimonials->getTestimonials();

$ctr = 0;
foreach ($arrTestimonials as $val) {
	$ctr++;
	// set active CSS class
	$act = $ctr == 1 ? ' active' : '';

	// handle long notes
	$strNote = $val['Note'];
	$maxlen = 650;
	$len = strlen($val['Note']);
	if ($len > $maxlen) {
		$strNote = substr($val['Note'], 0, $maxlen) . '...<a href="testimonials_list.php#'. 
			$val['ID'] .'">more</a>';
	}

	// handle anonymous pilots
	$strPilotName = ($val['Anon'] == 1) ? 'anonymous pilot' : $val['Pilot'];	?>

	<div class="item<?=$act?>">
		<div class="carousel-content">
			<div style="width: 100%;">
				<?=$strNote?><br /><br />
				<span class="pull-right" style="text-align: right;">
					<?=$strPilotName?><br />
					<span style="font-size: 85%;">rescued on 
						<?=Output::getEveDate($val['RescueDate'])?></span>
				</span>
			</div>
		</div>
	</div>
	
	<?php
}
?>