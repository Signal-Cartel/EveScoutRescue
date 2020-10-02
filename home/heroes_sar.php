<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

$database = new Database();
$pilot = new Pilot($database);
$arrPilots = $pilot->getMedals("'11','12','13','14'", true);
$pgtitle = "Hall of Rescue Heroes";


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<div class="col-md-4"></div>
	<?php 
	// Beacon of Anoikis column
	Pilot::printMedalsTable('sar_rescue', 'Beacon of Anoikis', 100, '14', $arrPilots);
	?>
	<div class="col-md-4"></div>
</div>

<div class="row">
	<?php 
	// Gold Medal column
	Pilot::printMedalsTable('sar_rescue', 'Gold Lifesaver', 50, '13', $arrPilots);
	
	// Silver Medal column
	Pilot::printMedalsTable('sar_rescue', 'Silver Lifesaver', 10, '12', $arrPilots);

	// Bronze Medal column
	Pilot::printMedalsTable('sar_rescue', 'Bronze Lifesaver', 1, '11', $arrPilots);
	?>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
