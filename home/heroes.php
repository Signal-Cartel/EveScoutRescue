<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$db = new Database();
$pilot = new Pilot($db);
$arrPilots = $pilot->getMedals("'1','2','3','4','5','6','7'", true);
$pgtitle = 'Hall of Cache Heroes';


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row white">
	<div class="col-md-4"></div>
	<div class="col-md-4">
		<h2 style="text-align: center;">The Crinkle Crown</h2>
		<p style="text-align: center;">Awarded to pilots upon sowing or tending 
			10000 rescue caches. First awarded to Captain Crinkle, who is the very embodiment of 
			the caring diligence that marks a true-blue Signaleer.<br />
			<img height="216" src="https://image.eveonline.com/Character/97117031_512.jpg">
		</p>
		<table class="table white">
			<thead>
				<tr>
					<th>Pilot</th>
					<th>Date Awarded</th>
				</tr>
			</thead>
			<tbody>
				<tr>	
					<td>Renek Dallocort</td>	
					<td>YC122-Nov-15</td>
				</tr>
				<tr>	
					<td>Captain Crinkle</td>	
					<td>YC122-May-19</td>
				</tr>
				</tbody>
		</table>
	</div>
	<div class="col-md-4"></div>
</div>

<div class="row white">
	<?php 
	// InsaneCacher column
	Pilot::printMedalsTable('esrc', 'InsaneCacher', 5000, '6', $arrPilots);

	// HeroCacher column
	Pilot::printMedalsTable('esrc', 'HeroicCacher', 3000, '5', $arrPilots);

	// UltraCacher column
	Pilot::printMedalsTable('esrc', 'UltraCacher', 1000, '4', $arrPilots);
	?>
</div>

<div class="row white">
	<?php 
	// HyperCacher column
	Pilot::printMedalsTable('esrc', 'HyperCacher', 500, '3', $arrPilots);

	// MegaCacher column
	Pilot::printMedalsTable('esrc', 'MegaCacher', 300, '2', $arrPilots);

	// SuperCacher column
	Pilot::printMedalsTable('esrc', 'SuperCacher', 100, '1', $arrPilots);
	?>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
