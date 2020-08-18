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
	<?php 
	// Beacon of Anoikis column
	Pilot::printTable('sar_rescue', 'Beacon of Anoikis', 100, '14', $arrPilots);

	// Gold Medal column
	Pilot::printTable('sar_rescue', 'Gold Lifesaver', 50, '13', $arrPilots);
	
	// Silver Medal column
	Pilot::printTable('sar_rescue', 'Silver Lifesaver', 10, '12', $arrPilots);
	?>
</div>

<div class="row">
	<?php 
	// Bronze Medal column
	Pilot::printTable('sar_rescue', 'Bronze Lifesaver', 1, '11', $arrPilots);
	?>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
