<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$database = new Database();
$pilot = new Pilot($database);
$arrPilots = $pilot->getMedals("'21','22','23'", true);
$pgtitle = "Hall of Dispatch Heroes";


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<?php 
	// Master column
	Pilot::printTable('sardisp', 'Master Dispatcher', 100, '23', $arrPilots);

	// Proficient column
	Pilot::printTable('sardisp', 'Proficient Dispatcher', 50, '22', $arrPilots);

	// Qualified column
	Pilot::printTable('sardisp', 'Qualified Dispatcher', 5, '21', $arrPilots);
	?>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
